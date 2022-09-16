<?php

namespace App\Http\Controllers\Api;

use App\Jobs\ProcessPayload;
use App\Models\Payload;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class PayloadController extends Controller
{
    public function __invoke(Request $request): Response|Application|ResponseFactory
    {
        $payload = Payload::create([
            'payload' => [
                'data' => $request->data,
                'imei' => $request->imei
            ]
        ]);

        ProcessPayload::dispatch($payload);

        return response($this->payloadResponse($payload->payload['data']));
    }

    private function payloadResponse($payload): ?string
    {
        // Login Package
        if (Str::contains($payload, 'AP00')){
            return sprintf('IWBP00,%s,8#',
                now()->utc()->format('YmdHis')
            );
        }

        // Location Package
        if (Str::contains($payload, 'AP01')){
            return 'IWBP01#';
        }

        // Multiple Bases Location Package
        if (Str::contains($payload, 'AP02')){
            return 'IWBP02#';
        }

        // Heartbeat Package
        if (Str::contains($payload, 'AP03')){
            return 'IWBP03#';
        }

        // Heart Rate
        if (Str::contains($payload, 'AP49')){
            return 'IWBP49#';
        }

        // Heart Rate and BP
        if (Str::contains($payload, 'APHT')){
            return 'IWBPHT#';
        }

        // Heart Rate, BP, SPO2, Blood Sugar
        if (Str::contains($payload, 'APHP')){
            return 'IWBPHP#';
        }

        // Body Temperature
        if (Str::contains($payload, 'AP50')){
            return 'IWBP50#';
        }

        // ECG Upload
        // This return value is not correct, come back and check
        if (Str::contains($payload, 'APHD')){
            return 'IWBPHD#';
        }

        return null;
    }
}
