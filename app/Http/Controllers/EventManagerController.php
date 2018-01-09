<?php

namespace App\Http\Controllers;

use Alexa\Request\IntentRequest;
use App\GuestNote;
use App\User;
use App\Event;
use App\Guest;
use Illuminate\Http\Request;

class EventManagerController extends Controller
{
    public function parseAlexaRequest(Request $request)
    {
        $alexaRequest = \Alexa\Request\Request::fromData($request->json()->all());
        $response = new \Alexa\Response\Response;

        /* Determine whether the current Amazon Alexa User already exists */

        $userId = $alexaRequest->user->userId;

        if (empty($userId)) {
            $response->respond('Es tut mir leid, aber ich konnte leider keinen Benutzer erkennen.');
            return response()->json($response->render());
        }

        $user = User::where('user_id', $userId)->first();
        if (empty($user)) {
            /* No user found -> Create user and default event */

            $user = User::create(['user_id' => $userId, ]);
            $event = Event::create(['user_id' => $user->id, 'name' => 'Standardveranstaltung', ]);

            $user->event_id = $event->id;
            $user->save();

            $response->respond('Herzlich willkommen zum Eventplaner. Ich habe bereits eine Standardveranstaltung für Sie angelegt, Sie können also sofort loslegen.');
            return response()->json($response->render());
        }

        /* Fetch lastly used event */

        $currentEvent = $user->lastEvent;

        /* Process known intents */

        if ($alexaRequest instanceof IntentRequest) {
            try {
                $className = 'App\\Intents\\' . $alexaRequest->intentName;
                $intent = new $className($user, $currentEvent, $alexaRequest);

                $responseText = $intent->process();
                error_log('Response-Text: ' . $responseText);
                $response->respond($responseText);

                return response()->json($response->render());
            } catch (Exception $e) {
                die('No matching intent class found');
            }

            switch ($alexaRequest->intentName) {
                case 'GetUndecidedGuestsListIntent' :
                    /* Fetch called off guests */

                    $guests = Guest::forEvent($this->currentEvent->id)->undecided()->get();

                    if ($guests->isEmpty()) {
                        $response->respond('Es sind keine Anmeldungen mehr offen.');
                    } else {
                        $firstGuestNames = $guests->pluck('name')->sort();
                        $lastGuestName = $firstGuestNames->splice($firstGuestNames->count() - 1)->first();

                        if ($firstGuestNames->isEmpty()) {
                            $responseText = 'Bisher hat sich nur ' . $lastGuestName . ' noch nicht entschieden.';
                        } else {
                            $responseText = 'Folgende Gäste haben sich noch nicht entschieden: ' . implode(', ', $firstGuestNames->all()) . ' und ' . $lastGuestName;
                        }

                        $response->respond($responseText);
                    }

                    break;

                case 'GetGuestStatusIntent' :
                    if (isset($alexaRequest->slots['Name']) && !empty($alexaRequest->slots['Name'])) {
                        $guestName = ucwords($alexaRequest->slots['Name']);

                        $guest = Guest::forEvent($this->currentEvent->id)->where('name', 'LIKE', $guestName)->first();
                        if (!is_null($guest)) {
                            switch ($guest->status) {
                                case 'confirmed' :
                                    $response->respond($guestName . ' hat zugesagt.');

                                    break;

                                case 'unable' :
                                    $response->respond($guestName . ' hat abgesagt.');

                                    break;

                                case 'undecided' :
                                    $response->respond($guestName . ' hat sich noch nicht entschieden.');

                                    break;

                                default :
                                    $status = 'unbekannt';
                            }
                        } else {
                            $response->respond('Ich konnte keinen Gast mit diesem Namen finden.');
                        }
                    } else {
                        $response->reprompt('Für welchen Gast möchtest Du den Anmeldestatus wissen?');
                    }

                    break;

                case 'AddNoteToGuestIntent' :
                    if (isset($alexaRequest->slots['Notiz']) && !empty($alexaRequest->slots['Notiz']) && isset($alexaRequest->slots['Gast']) && !empty($alexaRequest->slots['Gast'])) {
                        $noteName = ucwords($alexaRequest->slots['Notiz']);
                        $guestName = ucwords($alexaRequest->slots['Gast']);

                        /* Determine whether this guest is registered for the current event */

                        $guest = Guest::forEvent($this->currentEvent->id)->where('name', 'LIKE', $guestName)->first();
                        if (!empty($guest)) {
                            /* Determine whether this note is already registered for this guest */

                            $note = GuestNote::forGuest($guest->id)->where('note', 'LIKE', $noteName)->first();
                            if (empty($note)) {
                                $note = GuestNote::create(['guest_id' => $guest->id, 'note' => $noteName, ]);

                                $response->respond('Ich habe die Notiz ' . $noteName . ' für ' . $guestName . ' angelegt.');
                            } else {
                                $response->respond('Für ' . $guestName . ' war bereits eine Notiz ' . $noteName . ' hinterlegt.');
                            }
                        } else {
                            $response->respond($guestName . ' ist mir nicht als Gast für diese Veranstaltung bekannt.');
                        }
                    } else {
                        $response->reprompt('Welche Notiz soll für welchen Gast hinzugefügt werden?');
                    }

                    break;

                case 'RemoveNoteFromGuestIntent' :
                    if (isset($alexaRequest->slots['Notiz']) && !empty($alexaRequest->slots['Notiz']) && isset($alexaRequest->slots['Gast']) && !empty($alexaRequest->slots['Gast'])) {
                        $noteName = ucwords($alexaRequest->slots['Notiz']);
                        $guestName = ucwords($alexaRequest->slots['Gast']);

                        /* Determine whether this guest is registered for the current event */

                        $guest = Guest::forEvent($this->currentEvent->id)->where('name', 'LIKE', $guestName)->first();
                        if (!empty($guest)) {
                            /* Determine whether this note is registered for this guest */

                            $note = GuestNote::forGuest($guest->id)->where('note', 'LIKE', $noteName)->first();
                            if (!empty($note)) {
                                $note->delete();

                                $response->respond('Ich habe die Notiz ' . $noteName . ' für ' . $guestName . ' entfernt.');
                            } else {
                                $response->respond('Für ' . $guestName . ' war keine Notiz ' . $noteName . ' hinterlegt.');
                            }
                        } else {
                            $response->respond($guestName . ' ist mir nicht als Gast für diese Veranstaltung bekannt.');
                        }
                    } else {
                        $response->reprompt('Welche Notiz soll für welchen Gast entfernt werden?');
                    }

                    break;

                case 'RemoveAllNotesFromGuestIntent' :
                    if (isset($alexaRequest->slots['Gast']) && !empty($alexaRequest->slots['Gast'])) {
                        $guestName = ucwords($alexaRequest->slots['Gast']);

                        /* Determine whether this guest is registered for the current event */

                        $guest = Guest::forEvent($this->currentEvent->id)->where('name', 'LIKE', $guestName)->first();
                        if (!empty($guest)) {
                            /* Remove all notes for this guest */

                            GuestNote::forGuest($guest->id)->delete();

                            $response->respond('Ich habe alle Notizen für ' . $guestName . ' entfernt.');
                        } else {
                            $response->respond($guestName . ' ist mir nicht als Gast für diese Veranstaltung bekannt.');
                        }
                    } else {
                        $response->reprompt('Für welchen Gast sollen alle Notizen entfernt werden?');
                    }

                    break;

                case 'GetGuestNotesIntent' :
                    if (isset($alexaRequest->slots['Gast']) && !empty($alexaRequest->slots['Gast'])) {
                        $guestName = ucwords($alexaRequest->slots['Gast']);

                        /* Determine whether this guest is registered for the current event */

                        $guest = Guest::forEvent($this->currentEvent->id)->where('name', 'LIKE', $guestName)->first();
                        if (!empty($guest)) {
                            /* Fetch notes for this guest */

                            $notes = GuestNote::forGuest($guest->id)->get();

                            if ($notes->isEmpty()) {
                                $response->respond('Für ' . $guestName . ' sind keine Notizen eingetragen.');
                            } else if ($notes->count() == 1) {
                                $response->respond('Für ' . $guestName . ' ist folgende Notiz eingetragen: ' . $notes->first()->pluck('note')->first());
                            } else {
                                $firstNotes = $notes->pluck('note');
                                $lastNote = $firstNotes->splice($firstNotes->count() - 1)->first();

                                $response->respond('Folgende Notizen sind für ' . $guestName . ' eingetragen: ' . implode(', ', $firstNotes->all()) . ' und ' . $lastNote);
                            }
                        } else {
                            $response->respond($guestName . ' ist mir nicht als Gast für diese Veranstaltung bekannt.');
                        }
                    } else {
                        /* First fetch guests with notes */

                        $guests = Guest::forEvent($this->currentEvent->id)->has('notes')->get();
                        if (!$guests->isEmpty()) {
                            $responseText = 'Für folgende Gäste sind Notizen hinterlegt. ';

                            $guests->each(function($guest) use (&$responseText) {
                                $responseText .= $guest->name . ': ' . implode(', ', $guest->notes->pluck('note')->all()) . '. ';
                            });

                            $response->respond($responseText);
                        } else {
                            $response->respond('Es ist für keinen Gast eine Notiz hinterlegt');
                        }
                    }

                    break;

                case 'AMAZON.HelpIntent' :
                    $response->respond('Mögliche Anweisungen lauten: Neue Veranstaltung erstellen, Veranstaltung wechseln, neuen Gast hinzufügen, Gästeliste oder wer hat bereits zugeasgt.');

                    break;
            }
        } else {
            $responses = [
                'Herzlich Willkommen',
                'Willkommen beim Eventplaner',
                'Willkommen zurück',
            ];
            $response->respond($responses[array_rand($responses)]);
        }

        return response()->json($response->render());
    }
}
