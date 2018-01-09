<?php

namespace App\Intents\Slots;

class Slot
{
    /* Properties */

    public $name;
    public $value;
    public $confirmationStatus;

    public function __construct($name, $value, $confirmationStatus)
    {
        $this->name = $name;
        $this->value = $value;
        $this->confirmationStatus = $confirmationStatus;
    }
}