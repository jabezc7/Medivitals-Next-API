<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class CommandsController extends Controller
{
    public function __invoke(Request $request): Response|Application|ResponseFactory
    {
        Http::asJson()->post(config('services.websocket.endpoint') . '/command', [
            'data' => (string) $request->data,
            'imei' => (string) $request->imei
        ]);

        return response();
    }
}
