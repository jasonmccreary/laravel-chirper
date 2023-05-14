<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class HealthCheck extends Controller
{
    public function __invoke()
    {
        return response()->json([
            'status' => 'ok',
            'message' => 'Chirp chirp!',
        ]);
    }
}
