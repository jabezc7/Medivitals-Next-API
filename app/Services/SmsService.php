<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class SmsService
{
    const URL = 'https://www.smsbroadcast.com.au/api-adv.php';

    protected string $username;

    protected string $password;

    protected string $from;

    protected string $output;

    public function __construct()
    {
        $this->username = config('services.sms-broadcast.username');
        $this->password = config('services.sms-broadcast.password');
        $this->from = config('services.sms-broadcast.from');
    }

    public function send($to, $message): Collection
    {
        $response = Http::asForm()->post(self::URL, [
            'username' => $this->username,
            'password' => $this->password,
            'to' => str_replace(' ', '', $to),
            'from' => $this->from,
            'message' => $message,
            'maxsplit' => 5
        ]);

        $this->output = $response->body();

        return $this->parseResponse();
    }

    private function parseResponse() : Collection
    {
        $response = explode("\n", $this->output);
        $output = collect();

        foreach($response as $line){
            $data = explode(':',$line);
            if ($data[0] == 'OK') {
                $output->push([
                    'status' => 'success',
                    'destination' => $data[1],
                    'reference' => $data[2]
                ]);
            } elseif ($data[0] == 'BAD' ) {
                $output->push([
                    'status' => 'not sent',
                    'destination' => $data[1],
                    'reason' => $data[2]
                ]);
            } elseif ($data[0] == 'ERROR') {
                $output->push([
                    'status' => 'error',
                    'reason' => $data[1]
                ]);
            }
        }

        return $output;
    }
}
