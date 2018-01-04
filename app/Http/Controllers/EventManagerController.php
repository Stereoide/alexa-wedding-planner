<?php

namespace App\Http\Controllers;

use Alexa\Request\IntentRequest;
use App\User;
use App\Event;
use App\Guest;
use Illuminate\Http\Request;

class EventManagerController extends Controller
{
    public function index(Request $request)
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
            $event = Event::create(['user_id' => $user->id, 'name' => 'Standard-Veranstaltung', ]);

            $user->event_id = $event->id;
            $user->save();

            $response->respond('Herzlich willkommen zum Eventplaner. Ich habe bereits eine Standard-Veranstaltung für Sie angelegt, Sie können also sofort loslegen.');
            return response()->json($response->render());
        }

        /* Fetch lastly used event */

        $event = $user->event;

        if ($alexaRequest instanceof IntentRequest) {
            switch ($alexaRequest->intentName) {
                case 'AddGuestIntent' :
                    if (isset($alexaRequest->slots['Name']) && !empty($alexaRequest->slots['Name'])) {
                        $guestName = $alexaRequest->slots['Name'];

                        /* Determine whether this guest already exists */

                        $guests = Guest::forEvent($event->id)->where('name', $guestName)->get();
                        if ($guests->isEmpty()) {
                            Guest::create(['event_id' => $event->id, 'name' => $guestName, 'status' => 'undecided']);
                            $response->respond('Ich habe ' . $guestName . ' zur Gästeliste hinzugefügt');
                        } else {
                            $response->respond($guestName . ' war bereits angemeldet.');
                        }
                    } else {
                        $response->reprompt('Welcher Gast soll zur Gästeliste hinzugefügt werden?');
                    }

                    break;

                case 'RemoveGuestIntent' :
                    if (isset($alexaRequest->slots['Name']) && !empty($alexaRequest->slots['Name'])) {
                        $guestName = $alexaRequest->slots['Name'];

                        Guest::forEvent($event->id)->where('name', $guestName)->delete();
                        $response->respond('Ich habe ' . $guestName . ' von der Gästeliste entfernt.');
                    } else {
                        $response->reprompt('Welcher Gast soll von der Gästeliste gelöscht werden?');
                    }

                    break;

                case 'RemoveAllGuestsIntent' :
                    Guest::forEvent($event->id)->delete();

                    $response->respond('Ich habe alle Gäste von der Gästeliste entfernt.');
                    break;

                case 'ConfirmGuestIntent' :
                    if (isset($alexaRequest->slots['Name']) && !empty($alexaRequest->slots['Name'])) {
                        $guestName = $alexaRequest->slots['Name'];

                        $guest = Guest::forEvent($event->id)->where('name', $guestName)->first();
                        if (!is_null($guest)) {
                            $guest->status = 'confirmed';
                            $guest->save();
                            $response->respond('Ich habe die Zusage für ' . $guestName . ' notiert.');
                        } else {
                            $response->respond('Ich konnte keinen Gast mit diesem Namen finden.');
                        }
                    } else {
                        $response->reprompt('Welcher Gast soll bestätigt werden?');
                    }

                    break;

                case 'CallOffGuestIntent' :
                    if (isset($alexaRequest->slots['Name']) && !empty($alexaRequest->slots['Name'])) {
                        $guestName = $alexaRequest->slots['Name'];

                        $guest = Guest::forEvent($event->id)->where('name', $guestName)->first();
                        if (!is_null($guest)) {
                            $guest->status = 'unable';
                            $guest->save();
                            $response->respond('Ich habe die Absage für ' . $guestName . ' notiert.');
                        } else {
                            $response->respond('Ich konnte keinen Gast mit diesem Namen finden.');
                        }
                    } else {
                        $response->reprompt('Welcher Gast soll abgesagt werden?');
                    }

                    break;

                case 'GetGuestsListIntent' :
                    /* Fetch all guests */

                    $guestsConfirmed = Guest::forEvent($event->id)->confirmed()->get();
                    $guestsUndecided = Guest::forEvent($event->id)->undecided()->get();
                    $guestsUnable = Guest::forEvent($event->id)->unable()->get();

                    if ($guestsConfirmed->isEmpty() && $guestsUndecided->isEmpty() && $guestsUnable->isEmpty()) {
                        $responseText = 'Es liegen noch keine Anmeldungen vor.';
                    } else {
                        $responseText = '';

                        if ($guestsConfirmed->isEmpty()) {
                            $responseText .= 'Es haben noch keine Gäste zugesagt';
                        } else {
                            if ($guestsConfirmed->count() == 1) {
                                $responseText .= 'Ein Gast hat zugesagt';
                            } else {
                                $responseText .= $guestsConfirmed->count() . ' Gäste haben zugesagt';
                            }
                        }

                        $responseText .= ', ';
                        if ($guestsUnable->isEmpty()) {
                            $responseText .= 'es haben noch keine Gäste abgesagt';
                        } else {
                            if ($guestsUnable->count() == 1) {
                                $responseText .= 'ein Gast hat abgesagt';
                            } else {
                                $responseText .= $guestsUnable->count() . ' Gäste haben abgesagt';
                            }
                        }

                        $responseText .= ' und ';
                        if ($guestsUndecided->isEmpty()) {
                            $responseText .= 'es sind keine Anmeldungen mehr offen';
                        } else {
                            if ($guestsUndecided->count() == 1) {
                                $responseText .= 'ein Gast hat sich noch nicht entschieden.';
                            } else {
                                $responseText .= $guestsUndecided->count() . ' Gäste haben sich noch nicht entschieden.';
                            }
                        }
                    }

                    $response->respond($responseText);

                    break;

                case 'GetConfirmedGuestsListIntent' :
                    /* Fetch confirmed guests */

                    $guests = Guest::forEvent($event->id)->confirmed()->get();

                    if ($guests->isEmpty()) {
                        $response->respond('Es haben noch keine Gäste zugesagt.');
                    } else {
                        $guestNames = [];
                        $guests->each(function($guest) use (&$guestNames) {
                            $guestNames[] = $guest->name;
                        });

                        $responseText = 'Folgende Gäste haben bereits zugesagt: ' . implode(', ', $guestNames);
                        $response->respond($responseText);
                    }

                    break;

                case 'GetUnableGuestsListIntent' :
                    /* Fetch called off guests */

                    $guests = Guest::forEvent($event->id)->unable()->get();

                    if ($guests->isEmpty()) {
                        $response->respond('Es haben noch keine Gäste abgesagt.');
                    } else {
                        $guestNames = [];
                        $guests->each(function($guest) use (&$guestNames) {
                            $guestNames[] = $guest->name;
                        });

                        $responseText = 'Folgende Gäste haben bereits abgesagt: ' . implode(', ', $guestNames);
                        $response->respond($responseText);
                    }

                    break;

                case 'GetUndecidedGuestsListIntent' :
                    /* Fetch called off guests */

                    $guests = Guest::forEvent($event->id)->undecided()->get();

                    if ($guests->isEmpty()) {
                        $response->respond('Es sind keine Anmeldungen mehr offen.');
                    } else {
                        $guestNames = [];
                        $guests->each(function($guest) use (&$guestNames) {
                            $guestNames[] = $guest->name;
                        });

                        $responseText = 'Folgende Gäste haben sich noch nicht entschieden: ' . implode(', ', $guestNames);
                        $response->respond($responseText);
                    }

                    break;

                case 'GetGuestStatusIntent' :
                    if (isset($alexaRequest->slots['Name']) && !empty($alexaRequest->slots['Name'])) {
                        $guestName = $alexaRequest->slots['Name'];

                        $guest = Guest::forEvent($event->id)->where('name', $guestName)->first();
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

                case 'AMAZON.HelpIntent' :
                    $response->respond('Mögliche Anweisungen lauten: Neue Veranstaltung erstellen, Veranstaltung wechseln, neuen Gast hinzufügen, Gästeliste oder wer hat bereits zugeasgt.');

                    break;

                default :
                    $responses = [
                        'Das habe ich leider nicht verstanden',
                        'Das weiß ich leider nicht',
                        'Es tut mir leid, damit kann ich dir leider nicht helfen',
                        'Es tut mir leid, das habe ich nicht verstanden',
                        'Es tut mir leid, das weiß ich nicht',
                        'Wie bitte?',
                    ];
                    $response->respond($responses[array_rand($responses)]);

                    $response->respond($alexaRequest->intentName);

                    break;
            }
        } else {
            $responses = [
                'Hallo',
                'Herzlich Willkommen',
                'Willkommen',
            ];
            $response->respond($responses[array_rand($responses)]);
        }

        return response()->json($response->render());
    }
}
