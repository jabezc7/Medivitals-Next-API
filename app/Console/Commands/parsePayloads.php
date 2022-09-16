<?php

namespace App\Console\Commands;

use App\Jobs\ProcessPayload;
use App\Models\Data;
use App\Models\Payload;
use Illuminate\Console\Command;

class parsePayloads extends Command
{
    protected $signature = 'parse:payloads';

    protected $description = 'Cycle through payloads';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): int
    {
        Payload::all()->each(function($payload){
            if (!Data::where('payload_id', $payload->id)->first()){
                ProcessPayload::dispatch($payload);
            };
        });

        return 0;
    }
}
