<?php

namespace App\Jobs;

use App\Models\Data;
use App\Models\Device;
use App\Models\Payload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ProcessPayload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payload;

    public function __construct(Payload $payload)
    {
        $this->payload = $payload;
    }

    public function handle()
    {
        $device = Device::where('imei', $this->payload->payload['imei'])->first();

        if ($device->patients->count() > 0){
            $device->patients->each(function($patient) use ($device) {
                $this->parsePayload($this->payload->payload['data'])
                    ->each(function($data) use ($patient, $device) {
                        Data::create([
                            'payload_id' => $this->payload->id,
                            'device_id' => $device->id,
                            'patient_id' => $patient->id,
                            'type' => $data['type'],
                            'value' => $data['value']
                        ]);
                    });
            });
        }
    }

    private function parsePayload($string){
        // Heart Rate
        if (Str::contains($string, 'AP49')){
            return collect([
                [
                    'type' => 'heart_rate',
                    'value' => Str::of($string)->match('/IWAP49,(.*)#/')
                ]
            ]);
        }

        // Heart Rate and BP
        if (Str::contains($string, 'APHT')){
            preg_match('/IWAPHT,(.*),(.*),(.*)#/', $string, $matches);
            array_shift($matches);

            return collect([
                [
                    'type' => 'heart_rate',
                    'value' => $matches[0]
                ],
                [
                    'type' => 'blood_pressure',
                    'value' => $matches[1].'/'.$matches[2]
                ],
            ]);
        }

        // Heart Rate, BP, SPO2, Blood Sugar
        if (Str::contains($string, 'APHP')){
            $matches = explode(",", $string);
            array_shift($matches);

            return collect([
                [
                    'type' => 'heart_rate',
                    'value' => $matches[0]
                ],
                [
                    'type' => 'blood_pressure',
                    'value' => $matches[1].'/'.$matches[2]
                ],
                [
                    'type' => 'oxygen_saturation', // SpO2
                    'value' => $matches[3]
                ],
                [
                    'type' => 'blood_sugar',
                    'value' => $matches[4]
                ],
                [
                    'type' => 'temperature',
                    'value' => $matches[5]
                ],
            ]);
        }

        // Body Temperature
        if (Str::contains($string, 'AP50')){
            preg_match('/IWAP50,(.*),(.*)#/', $string, $matches);
            array_shift($matches);

            return collect([
                [
                    'type' => 'temperature',
                    'value' => $matches[0]
                ]
            ]);
        }

        // GPS Location
        if (Str::contains($string, 'AP01')){
            preg_match('/IWAP01(.*)A(.*)S(.*)E/', $string, $matches);
            array_shift($matches);

            if (count($matches) >= 1) {
                $latString = Str::of($matches[1])->explode('.');
                $latDecimal = substr($latString[0], -2);

                $latInt = (float)($latDecimal.'.'.$latString[1]) / 60;

                $lat = (float)trim(Str::replace($latDecimal, '', $latString[0])) + (float)$latInt;

                $lngString = Str::of(trim($matches[2]))->explode('.');
                $lngDecimal = substr($lngString[0], -2);

                $lngInt = (float)($lngDecimal.'.'.$lngString[1]) / 60;

                $lng = (float)trim(Str::replace($lngDecimal, '', $lngString[0])) + (float)$lngInt;

                return collect([
                    [
                        'type' => 'location',
                        'value' => $lat . '|' . $lng
                    ]
                ]);
            }
        }

        return collect([]);
    }
}
