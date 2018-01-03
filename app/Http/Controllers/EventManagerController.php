<?php

namespace App\Http\Controllers;

use Alexa\Request\IntentRequest;
use Illuminate\Http\Request;
use Faker\Generator as Faker;

class EventManagerController extends Controller
{
    public function index(Request $request, Faker $faker)
    {
        $alexaRequest = \Alexa\Request\Request::fromData($request->json()->all());

        $response = new \Alexa\Response\Response;
        if ($alexaRequest instanceof IntentRequest) {
            switch ($alexaRequest->intentName) {
                case 'AddGuestIntent' :
                    $responseText = 'Neuen Gast hinzufügen';

                    break;

                case 'RemoveGuestIntent' :
                    $responseText = 'Gast entfernen';

                    break;

                case 'ConfirmGuestIntent' :
                    $responseText = 'Gast bestätigen';

                    break;

                case 'CallOffGuestIntent' :
                    $responseText = 'Gast absagen';

                    break;

                case 'GetGuestsListIntent' :
                    $responseText = 'Gästeliste abfragen';

                    break;

                case 'GetConfirmedGuestsListIntent' :
                    $responseText = 'Bestätigte Gästeliste abfragen';

                    break;

                case 'GetUnableGuestsListIntent' :
                    $responseText = 'Abgesagte Gästeliste abfragen';

                    break;

                case 'GetUndecidedGuestsListIntent' :
                    $responseText = 'Unbestätigte Gästeliste abfragen';

                    break;

                case 'GetGuestStatusIntent' :
                    $responseText = 'Gaststatus abfragen';

                    break;

                case 'Intent' :
                    $responseText = '';

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
                    $responseText = $faker->shuffleArray($responses);

                    break;
            }

            $response->respond($responseText);
        } else {
            $responses = [
                'Hallo',
                'Herzlich Willkommen',
                'Willkommen',
            ];

            $response->respond($faker->shuffleArray($responses));
        }

        return response()->json($response->render());
    }
}
