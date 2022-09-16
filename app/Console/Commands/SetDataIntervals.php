<?php

namespace App\Console\Commands;

use App\Models\Device;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SetDataIntervals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:set-intervals {interval=15}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the devices to send data at a specified interval (in minutes)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $devices = Device::all();

        foreach ($devices as $device){
            try {
                // Temperature Interval
                $response = Http::asJson()->timeout(10)->post('http://203.29.243.212:8080/command', [
                    'command' => 'IWBP87,' . $device->imei . ',080835,1,' . $this->argument('interval') . '#',
                    'imei' => (string)$device->imei
                ]);

                if ($response->successful()) {
                    $this->info(sprintf('Temperature for %s (%s) set to %s minute intervals',
                        $device->nickname ?? 'Device #' . $device->id,
                        $device->imei,
                        $this->argument('interval')
                    ));
                }

                // Heart Rate Interval
                $response = Http::asJson()->timeout(10)->post('http://203.29.243.212:8080/command', [
                    'command' => 'IWBP86,' . $device->imei . ',080835,1,' . $this->argument('interval') . '#',
                    'imei' => (string)$device->imei
                ]);

                if ($response->successful()) {
                    $this->info(sprintf('Heart Rate for %s (%s) set to %s minute intervals',
                        $device->nickname ?? 'Device #' . $device->id,
                        $device->imei,
                        $this->argument('interval')
                    ));
                }

                // Location Interval
                $response = Http::asJson()->timeout(10)->post('http://203.29.243.212:8080/command', [
                    'command' => 'IWBP33,' . $device->imei . ',080835,8,60,5#',
                    'imei' => (string)$device->imei
                ]);

                if ($response->successful()) {
                    $this->info(sprintf('Location for %s (%s) set to 5 minute intervals',
                        $device->nickname ?? 'Device #' . $device->id,
                        $device->imei,
                    ));
                }
            } catch (Exception $e) {
                $this->error(sprintf('Failed to connect to %s (%s)',
                    $device->nickname ?? 'Device #' . $device->id,
                    $device->imei
                ));
            }
        }

        return Command::SUCCESS;
    }
}
