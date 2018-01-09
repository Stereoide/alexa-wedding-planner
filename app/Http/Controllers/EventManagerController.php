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
