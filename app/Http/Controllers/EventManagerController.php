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
            $intent = null;

            /* Try to instantiate the corresponding Intent class */

            try {
                $intentName = str_replace('AMAZON.', '', $alexaRequest->intentName);
                $className = 'App\\Intents\\' . $intentName;
                $intent = new $className($user, $currentEvent, $alexaRequest);
            } catch (Exception $e) {
                /* Try to instantiate the default Intent class */

                try {
                    $className = 'App\\Intents\\DefaultIntent';
                    $intent = new $className($user, $currentEvent, $alexaRequest);
                } catch (Exception $e) {
                    die('No matching intent class found');
                }
            }

            $responseText = $intent->process();
            $response->respond($responseText);
            return response()->json($response->render());
        } else {
            $responses = [
                'Herzlich Willkommen',
                'Willkommen beim Veranstaltungsplaner',
                'Willkommen zurück',
            ];
            $response->respond($responses[array_rand($responses)]);
        }

        return response()->json($response->render());
    }
}
