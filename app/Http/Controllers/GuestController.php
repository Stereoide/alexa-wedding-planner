<?php

namespace App\Http\Controllers;

use App\Guest;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    /**
     * Add a guest
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addGuest(Request $request)
    {
        $response = new \Alexa\Response\Response;
        $response->respond('Gast hinzufügen');
        return response()->json($response->render());

        /* Determine whether this guest is already registered */



        /* Add guest if necessary */



        /* Get current guest counts */

        $guestsTotalCount = Guest::count();
        $guestsConfirmedCount = Guest::confirmed()->count();
        $guestsUndecidedCount = Guest::undecided()->count();
    }

    /**
     * Remove a guest
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeGuest(Request $request)
    {
        /* Remove guest */



        /* Get current guest counts */

        $guestsTotalCount = Guest::count();
        $guestsConfirmedCount = Guest::confirmed()->count();
        $guestsUndecidedCount = Guest::undecided()->count();
    }

    /**
     * Get guest status
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getGuestsStatus(Request $request)
    {
        //
    }

    /**
     * Get guest list
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getGuestsList(Request $request)
    {
        //
    }

    /**
     * Get undecided guest list
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getUndecidedGuestList(Request $request)
    {
        //
    }

    /**
     * Get confirmed guest list
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getConfirmedGuestList(Request $request)
    {
        //
    }

    /**
     * Get called off guest list
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getUnableGuestList(Request $request)
    {
        //
    }

    /**
     * Confirm guest status
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function confirmGuestStatus(Request $request)
    {
        //
    }

    /**
     * Call off guest status
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function calloffGuestStatus(Request $request)
    {
        //
    }
}
