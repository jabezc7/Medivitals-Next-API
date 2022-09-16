<?php

namespace App\Events;

use App\Models\Data;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DataCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Data $data;

    public function __construct(Data $data)
    {
        $this->data = $data;
    }
}
