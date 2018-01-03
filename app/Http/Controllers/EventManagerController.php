<?php

namespace App\Http\Controllers;

use Alexa\Request\IntentRequest;
use App\Guest;
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
                    if (isset($alexaRequest->slots['Name']) && !empty($alexaRequest->slots['Name'])) {
                        $guestName = $alexaRequest->slots['Name'];

                        Guest::create(['name' => $guestName, 'status' => 'undecided']);
                        $response->respond($guestName . ' hinzugefügt');
                    } else {
                        $response->reprompt('Welcher Gast soll hinzugefügt werden?');
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
                    /* Fetch all guests */

                    $guestsConfirmed = Guest::confirmed()->all();
                    $guestsUndecided = Guest::undecided()->all();
                    $guestsUnable = Guest::unable()->all();

                    $responseText = ($guestsConfirmed->isEmpty() ? 'Es haben noch keine Gäste zugesagt' : $guestsConfirmed->count() . ' Gäste haben zugesagt');
                    $responseText .= ', ' . ($guestsUnable->isEmpty() ? 'es haben noch keine Gäste abgesagt' : $guestsUnable->count() . ' Gäste haben abgesagt');
                    $responseText .= ' und ' . ($guestsUndecided->isEmpty() ? 'es sind keine Anmeldungen mehr offen' : $guestsConfirmed->count() . ' Gäste haben sich noch nicht entschieden.');

                    $response->respond($responseText);

                    break;

                case 'GetConfirmedGuestsListIntent' :
                    /* Fetch confirmed guests */

                    $guests = Guest::confirmed()->all();

                    if ($guests->isEmpty()) {
                        $response->respond('Es haben noch keine Gäste zugesagt.');
                    } else {
                        $guestNames = [];
                        $guests->each(function($guest) use ($guestNames) {
                            $guestNames[] = $guest->name;
                        });

                        $responseText = 'Folgende Gäste haben bereits zugesagt: ' . implode(', ', $guestNames);
                        $response->respond($responseText);
                    }

                    break;

                case 'GetUnableGuestsListIntent' :
                    /* Fetch called off guests */

                    $guests = Guest::unable()->all();

                    if ($guests->isEmpty()) {
                        $response->respond('Es haben noch keine Gäste abgesagt.');
                    } else {
                        $guestNames = [];
                        $guests->each(function($guest) use ($guestNames) {
                            $guestNames[] = $guest->name;
                        });

                        $responseText = 'Folgende Gäste haben bereits abgesagt: ' . implode(', ', $guestNames);
                        $response->respond($responseText);
                    }

                    break;

                case 'GetUndecidedGuestsListIntent' :
                    /* Fetch called off guests */

                    $guests = Guest::undecided()->all();

                    if ($guests->isEmpty()) {
                        $response->respond('Es sind keine Anmeldungen mehr offen.');
                    } else {
                        $guestNames = [];
                        $guests->each(function($guest) use ($guestNames) {
                            $guestNames[] = $guest->name;
                        });

                        $responseText = 'Folgende Gäste haben noch nicht entschieden: ' . implode(', ', $guestNames);
                        $response->respond($responseText);
                    }

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
