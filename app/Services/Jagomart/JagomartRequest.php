<?php

namespace App\Services\Jagomart;
use Illuminate\Http\Request;

interface JagomartRequest {
    //User Register
    public function validateNewUser(Request $request);

    public function validateCategory(Request $request);

    public function validateNewItem(Request $request);

    public function validateTimeOperational(Request $request);

    public function validatePin(Request $request, $new = false);
}
