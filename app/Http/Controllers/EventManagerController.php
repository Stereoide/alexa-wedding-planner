<?php

namespace App\Http\Controllers;

use Alexa\Request\IntentRequest;
use Illuminate\Http\Request;

class EventManagerController extends Controller
{
    public function index(Request $request)
    {
        $alexaRequest = \Alexa\Request\Request::fromData($request->json()->all());

        $response = new \Alexa\Response\Response;
        if ($alexaRequest instanceof IntentRequest) {
            switch ($alexaRequest->intentName) {
                case 'AddGuestIntent' :
                    if (isset($alexaRequest->slots['Name']['value']) && !empty($alexaRequest->slots['Name']['value'])) {
                        $guestName = $alexaRequest->slots['Name']['value'];

                        Guest::create(['name' => $guestName, 'status' => 'undecided']);
                        $response->respond($guestName . ' hinzugefügt');
                    } else {
                        $response->reprompt('Nachfrage');
                    }

                    break;

                case 'RemoveGuestIntent' :
                    $response->respond('Gast entfernen');

                    break;

                case 'ConfirmGuestIntent' :
                    $response->respond('Gast bestätigen');

                    break;

                case 'CallOffGuestIntent' :
                    $response->respond('Gast absagen');

                    break;

                case 'GetGuestsListIntent' :
                    $response->respond('Gästeliste abfragen');

                    break;

                case 'GetConfirmedGuestsListIntent' :
                    $response->respond('Bestätigte Gästeliste abfragen');

                    break;

                case 'GetUnableGuestsListIntent' :
                    $response->respond('Abgesagte Gästeliste abfragen');

                    break;

                case 'GetUndecidedGuestsListIntent' :
                    $response->respond('Unbestätigte Gästeliste abfragen');

                    break;

                case 'GetGuestStatusIntent' :
                    $response->respond('Gaststatus abfragen');

                    break;

                case 'Intent' :
                    $response->respond('Unbekannte Absicht');

                    break;

                default :
                    $responses = [
                        'Das habe ich leider nicht verstanden',
                        'Das weiß ich leider nicht',
                        'Es tut mir leid, damit kann ich dir nicht leider nicht helfen',
                        'Es tut mir leid, das habe ich nicht verstanden',
                        'Es tut mir leid, das weiß ich nicht',
                        'Wie bitte?',
                    ];
                    $response->respond($responses[array_rand($responses)]);

                    break;
            }

            $response->respond($responseText);
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
