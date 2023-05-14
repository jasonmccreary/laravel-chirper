<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chirp;

class ChirpController extends Controller
{
    public function index()
    {
        return [
            'data' => [
                'count' => Chirp::count(),
            ],
        ];
    }

    public function show(string $id)
    {
        return [
            'data' => Chirp::findOrFail($id),
        ];
    }
}
