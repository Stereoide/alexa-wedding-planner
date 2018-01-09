<?php

namespace App\Intents;

use Alexa\Request\IntentRequest;
use Alexa\Response\OutputSpeech;
use App\Event;
use App\Intents\Slots\Slot;
use App\User;

class Intent
{
    /* Properties */

    public $slots = [];
    public $user;
    public $event;
    public $requiredSlots = [];
    public $optionalSlots = [];
    public $confirmationStatus;

    /* Methods */

    public function __construct(User $user, Event $currentEvent, IntentRequest $request)
    {
        $this->user = $user;
        $this->currentEvent = $currentEvent;
        $this->confirmationStatus = 'NONE';

        /* Parse request data for slot values */

        $this->parseDataForSlots($request);

        /* Assert required slot values are set */

        $this->assertRequiredSlotValuesAreSet();
    }

    protected function parseDataForSlots(IntentRequest $request)
    {
        foreach ($request->slots as $slotName => $slotValue) {
            $this->slots[$slotName] = new Slot($slotName, ucwords($slotValue), 'NONE');
        }
    }

    protected function assertRequiredSlotValuesAreSet()
    {
        /* Optional slots */

        foreach ($this->optionalSlots as $slotName) {
            if (!isset($this->slots[$slotName])) {
                $this->slots[$slotName] = '';
            }
        }

        /* Required slots */

        foreach ($this->requiredSlots as $slotName) {
            if (!isset($this->slots[$slotName]) || empty($this->slots[$slotName])) {
                error_log('Slot ' . $slotName . ' missing');
                /* Redirect the Alexa Dialog */

                $this->delegateDialog();
            }
        }
    }

    public function process()
    {

    }

    public function reply(string $answer)
    {

    }

    public function delegateDialog()
    {
        error_log('delegating');

        $json = json_encode([
            'version' => '1.0',
            'sessionAttributes' => new \stdClass(),
            'response' => [
                'shouldEndSession' => false,
                'directives' => [
                    [
                        'type' => 'Dialog.Delegate'
                    ]
                ]
            ]
        ]);

        header('Content-Type: application/json;charset=UTF-8');
        header('Content-Length: ' . strlen($json));

        echo $json;
        exit;
    }
}