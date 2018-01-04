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
            $event = Event::create(['user_id' => $user->id, 'name' => 'Standardveranstaltung', ]);

            $user->event_id = $event->id;
            $user->save();

            $response->respond('Herzlich willkommen zum Eventplaner. Ich habe bereits eine Standardveranstaltung für Sie angelegt, Sie können also sofort loslegen.');
            return response()->json($response->render());
        }

        /* Fetch lastly used event */

        $currentEvent = $user->lastEvent;

        if ($alexaRequest instanceof IntentRequest) {
            switch ($alexaRequest->intentName) {
                case 'AddEventIntent' :
                    if (isset($alexaRequest->slots['Veranstaltung']) && !empty($alexaRequest->slots['Veranstaltung'])) {
                        $eventName = ucwords($alexaRequest->slots['Veranstaltung']);

                        /* Determine whether this event already exists */

                        $event = Event::forUser($user->id)->where('name', 'LIKE', $eventName)->first();
                        if (empty($event)) {
                            $currentEvent = Event::create(['user_id' => $user->id, 'name' => $eventName, ]);
                            $user->event_id = $currentEvent->id;
                            $user->save();

                            $response->respond('Ich habe ' . $eventName . ' angelegt und zur aktiven Veranstaltung gemacht.');
                        } else {
                            $currentEvent = $event;
                            $user->event_id = $currentEvent->id;
                            $user->save();

                            $response->respond('Es gibt bereits eine Veranstaltung ' . $eventName . ' - ich habe diese zur aktiven Veranstaltung gemacht.');
                        }
                    } else {
                        $response->reprompt('Welche Veranstaltung soll zur hinzugefügt werden?');
                    }

                    break;

                case 'ChangeEventIntent' :
                    if (isset($alexaRequest->slots['Veranstaltung']) && !empty($alexaRequest->slots['Veranstaltung'])) {
                        $eventName = ucwords($alexaRequest->slots['Veranstaltung']);

                        /* Determine whether this event exists */

                        $event = Event::forUser($user->id)->where('name', 'LIKE', $eventName)->first();
                        if (!empty($event)) {
                            $currentEvent = $event;
                            $user->event_id = $currentEvent->id;
                            $user->save();

                            $response->respond('Ich habe ' . $eventName . ' zur aktiven Veranstaltung gemacht.');
                        } else {
                            $response->respond('Ich konnte keine Veranstaltung ' . $eventName . ' finden.');
                        }
                    } else {
                        $response->reprompt('Zu welcher Veranstaltung möchten Sie wechseln?');
                    }

                    break;

                case 'RemoveEventIntent' :
                    if (isset($alexaRequest->slots['Veranstaltung']) && !empty($alexaRequest->slots['Veranstaltung'])) {
                        $eventName = ucwords($alexaRequest->slots['Veranstaltung']);

                        if ($eventName == 'Standardveranstaltung') {
                            $response->respond('Die Standardveranstaltung kann leider nicht gelöscht werden.');
                        } else {
                            /* Determine whether this event exists */

                            $event = Event::forUser($user->id)->where('name', 'LIKE', $eventName)->first();
                            if (!empty($event)) {
                                /* Remove guests first */

                                $event->guests()->delete();

                                /* Remove event */

                                $event->delete();

                                /* Fetch default event */

                                $currentEvent = Event::forUser($user->id)->where('name', 'LIKE', 'Standardveranstaltung')->first();
                                $user->event_id = $currentEvent->id;
                                $user->save();

                                $response->respond('Ich habe ' . $eventName . ' gelöscht. Ab sofort ist die Standardveranstaltung aktiv.');
                            } else {
                                $response->respond('Ich konnte keine Veranstaltung ' . $eventName . ' finden.');
                            }
                        }
                    } else {
                        $response->reprompt('Welche Veranstaltung soll gelöscht werden?');
                    }

                    break;

                case 'GetEventsListIntent' :
                    /* Fetch all events */

                    $events = Event::forUser($user->id)->get();

                    if ($events->isEmpty()) {
                        $responseText = 'Es liegen aktuell keine Veranstaltungen vor.';
                    } else {
                        if ($events->count() == 1) {
                            $responseText = 'Es gibt zur Zeit nur eine Veranstaltung: ' . $events->first()->name;
                        } else {
                            $eventNames = [];
                            $events->each(function($event) use (&$eventNames) {
                                $eventNames[] = $event->name;
                            });

                            $firstEventNames = collect($eventNames);
                            $lastEventName = $firstEventNames->splice($firstEventNames->count() - 1)->first();

                            $responseText = 'Es gibt ' . $events->count() . ' Veranstaltungen: ' . implode(', ', $firstEventNames->all()) . ' und ' . $lastEventName;
                            $response->respond($responseText);
                        }
                    }

                    $response->respond($responseText);

                    break;

                case 'WhichEventIntent' :
                    $response->respond('Aktuell ist ' . $currentEvent->name . ' die aktive Veranstaltung.');

                    break;

                case 'AddGuestIntent' :
                    if (isset($alexaRequest->slots['Name']) && !empty($alexaRequest->slots['Name'])) {
                        $guestName = ucwords($alexaRequest->slots['Name']);

                        /* Determine whether this guest already exists */

                        $guests = Guest::forEvent($currentEvent->id)->where('name', 'LIKE', $guestName)->get();
                        if ($guests->isEmpty()) {
                            Guest::create(['event_id' => $currentEvent->id, 'name' => $guestName, 'status' => 'undecided']);
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
                        $guestName = ucwords($alexaRequest->slots['Name']);

                        Guest::forEvent($currentEvent->id)->where('name', 'LIKE', $guestName)->delete();
                        $response->respond('Ich habe ' . $guestName . ' von der Gästeliste entfernt.');
                    } else {
                        $response->reprompt('Welcher Gast soll von der Gästeliste gelöscht werden?');
                    }

                    break;

                case 'RemoveAllGuestsIntent' :
                    Guest::forEvent($currentEvent->id)->delete();

                    $response->respond('Ich habe alle Gäste von der Gästeliste entfernt.');
                    break;

                case 'ConfirmGuestIntent' :
                    if (isset($alexaRequest->slots['Name']) && !empty($alexaRequest->slots['Name'])) {
                        $guestName = ucwords($alexaRequest->slots['Name']);

                        $guest = Guest::forEvent($currentEvent->id)->where('name', 'LIKE', $guestName)->first();
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
                        $guestName = ucwords($alexaRequest->slots['Name']);

                        $guest = Guest::forEvent($currentEvent->id)->where('name', 'LIKE', $guestName)->first();
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

                    $guestsConfirmed = Guest::forEvent($currentEvent->id)->confirmed()->get();
                    $guestsUndecided = Guest::forEvent($currentEvent->id)->undecided()->get();
                    $guestsUnable = Guest::forEvent($currentEvent->id)->unable()->get();

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

                    $guests = Guest::forEvent($currentEvent->id)->confirmed()->get();

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

                    $guests = Guest::forEvent($currentEvent->id)->unable()->get();

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

                    $guests = Guest::forEvent($currentEvent->id)->undecided()->get();

                    if ($guests->isEmpty()) {
                        $response->respond('Es sind keine Anmeldungen mehr offen.');
                    } else {
                        $guestNames = collect();
                        $guests->each(function($guest) use (&$guestNames) {
                            $guestNames->push($guest->name);
                        });

                        $firstGuestNames = $guestNames->sort();
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

                        $guest = Guest::forEvent($currentEvent->id)->where('name', 'LIKE', $guestName)->first();
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
