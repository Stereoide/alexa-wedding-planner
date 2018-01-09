<?php

namespace App\Intents;

use App\Event;

class DefaultIntent extends Intent
{
    /* Methods */

    public function process()
    {
        return 'Es tut mir leid, das kann ich noch nicht.';
    }
}