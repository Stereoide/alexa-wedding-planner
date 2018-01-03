<?php

namespace App\Http\Controllers;

use Alexa\Request\IntentRequest;
use Illuminate\Http\Request;

class EventManagerController extends Controller
{
    public function index(Request $request)
    {
        $jsonDataAsArray = $request->json()->all(); // This is how you would retrieve this with Laravel
        $alexaRequest = \Alexa\Request\Request::fromData($jsonDataAsArray);

        $response = new \Alexa\Response\Response;
        if ($alexaRequest instanceof IntentRequest) {
            $response->respond('Absicht erkannt: ' . $alexaRequest->intentName);
        } else {
            $response->respond('Keine Absicht erkannt');
        }

        return response()->json($response->render());
    }
}
