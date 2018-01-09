<?php

namespace App\Intents;

use Alexa\Request\IntentRequest;
use App\Intents\Slots\Slot;
use App\User;

class Intent
{
    /* Properties */

    public $slots = [];
    public $user;
    public $requiredSlots = [];
    public $optionalSlots = [];
    public $confirmationStatus;

    /* Methods */

    public function __construct(User $user, IntentRequest $request)
    {
        $this->user = $user;
        $this->confirmationStatus = 'NONE';

        /* Parse request data for slot values */

        foreach ($request->slots as $slotName => $slotValue) {
            $slotValue = ucwords($slotValue);
            $this->slots[$slotName] = new Slot($slotName, $slotValue, 'NONE');
        }

        /* Assert required slot values are set */

        $this->assertRequiredSlotValuesAreSet();
    }

    protected function assertRequiredSlotValuesAreSet()
    {
        /* Optional slots */

        foreach ($this->optionalSlots as $slotName) {
            if (!isset($this->slotValues[$slotName])) {
                $this->slotValues[$slotName] = '';
            }
        }

        /* Required slots */

        foreach ($this->requiredSlots as $slotName) {
            if (!isset($this->slotValues[$slotName]) || empty($this->slotValues[$slotName])) {
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
        echo json_encode([
            'type' => 'Dialog.Delegate',
            'updatedIntent' => [
                'name' => get_class($this),
                'confirmationStatus' => $this->confirmationStatus,
                'slots' => $this->slots,
            ]
        ]);

        exit;
    }
}