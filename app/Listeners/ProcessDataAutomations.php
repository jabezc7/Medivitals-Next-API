<?php

namespace App\Listeners;

use App\Mail\AutomationEmail;
use App\Models\Automation;
use App\Models\Data;
use App\Models\Notification;
use App\Models\Type;
use App\Services\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ProcessDataAutomations implements ShouldQueue
{
    protected string $patientId;

    protected array $actionedTriggers;

    public function handle($event)
    {
        if ($event->data->patient_id){

            $this->patientId = $event->data->patient_id;

            $globalAutomations = Automation::query()
                ->where('global', true)
                ->where('active', true)
                ->get();

            $patientAutomation = Automation::query()
                ->where('patient_id', $event->data->patient_id)
                ->get();

            $automations = $globalAutomations->merge($patientAutomation);

            $automations->each(function ($automation) use ($event) {
                $shouldProcessActions = false;

                // Loop through triggers - all must be true
                collect($automation->triggers)->each(function ($trigger) use ($event, &$shouldProcessActions) {
                    if (Str::contains($trigger['vital'], 'blood-pressure')){
                        $triggerDataType = 'blood_pressure';
                    } else {
                        $triggerDataType = str_replace('vital_types_', '', str_replace('-', '_', $trigger['vital']));
                    }

                    if ($triggerDataType){
                        if ($trigger['comparison']['period'] === 'reading'){
                            $shouldProcessActions = $this->compareReadings($trigger, $triggerDataType, $event->data);

                            if (!$shouldProcessActions) {
                                return false;
                            }

                            $this->actionedTriggers[] = $trigger;
                        }
                    } else {
                        $shouldProcessActions = false;
                    }

                    return true;
                });

                if ($shouldProcessActions){
                    Log::info('HANDLE AUTOMATION');
                    Log::info(json_encode($automation));

                    $this->handleActions($automation->actions, $event->data);
                } else {
                    Log::info('DO NOT ACTION');
                }
            });
        }
    }

    private function handleActions($actions, $data)
    {
        collect($actions)->each(function ($action) use ($data) {
             $type = Type::query()->find($action['action']);

             if (Str::contains($type->slug, 'change-testing-frequency')) {
                 $code = match ($action['vital']) {
                     'vital-types-heart-rate',
                     'vital-types-blood-pressure-diastolic',
                     'vital-types-blood-pressure-Systolic' => 'IWBP86',
                     'vital-types-temperature' => 'IWBP87',
                 };

                 Http::asJson()->post(config('services.websocket.endpoint') . '/command', [
                     'data' => $code . ',' . $data->device->imei . ',080835,1,' . $action['value'] . '#',
                     'imei' => (string) $data->device->imei
                 ]);
             }

            if (Str::contains($type->slug, 'send-email')) {
                $tos = explode(',', str_replace(' ', '', $action['to']));

                foreach ($tos as $to){
                    Mail::to($to)->queue(new AutomationEmail($action['content']));
                }
            }

            if (Str::contains($type->slug, 'send-sms')) {
                (new SmsService())->send($action['to'], $action['content']);
            }

            if (Str::contains($type->slug, 'send-notification-to-device')) {
                Http::asJson()->post(config('services.websocket.endpoint') . '/command', [
                    'data' => $action['content'],
                    'imei' => (string) $data->device->imei
                ]);
            }

            if (Str::contains($type->slug, 'create-alert')) {
                Notification::create([
                    'patient_id' => $data->patient_id,
                    'alert' => true,
                    'message' => $action['content'],
                    'triggers' => $this->actionedTriggers
                ]);
            }
        });
    }

    private function compareReadings($trigger, $type, $eventData): bool
    {
        $conditionMet = false;

        // Use the current data model as the first check
        $conditionMet = $this->getConditionMet($trigger, $eventData);

        if (!$conditionMet){
            return false;
        }

        // Get Patients Last X Readings
        $data = Data::query()
            ->where('patient_id', $this->patientId)
            ->where('type', $type)
            ->orderBy('created_at', 'DESC')
            ->limit($trigger['comparison']['value'] - 1)
            ->get();

        $data->each(function ($reading) use ($trigger, &$conditionMet) {
            $conditionMet = $this->getConditionMet($trigger, $reading);

            if (!$conditionMet){
                return false;
            }

            return true;
        });

        return $conditionMet;
    }

    private function getConditionMet($trigger, $eventData): bool
    {
        if ($trigger['vital'] === 'vital-types-blood-pressure-systolic') {
            if ($systolic = Str::before($eventData->value, '/')) {
                Log::info([$systolic, $trigger['operator'], $trigger['value']]);
                $conditionMet = $this->compare($trigger['operator'], $systolic, $trigger['value']);

                if (!$conditionMet){
                    return false;
                }
            } else {
                return false;
            }
        } else if ($trigger['vital'] === 'vital-types-blood-pressure-diastolic') {
            if ($diastolic = Str::after($eventData->value, '/')) {
                $conditionMet = $this->compare($trigger['operator'], $diastolic, $trigger['value']);

                if (!$conditionMet){
                    return false;
                }
            } else {
                return false;
            }
        } else {
            $conditionMet = $this->compare($trigger['operator'], $eventData->value, $trigger['value']);
        }

        return $conditionMet;
    }

    private function compare($operator, $leftValue, $rightValue): bool
    {
        switch ($operator) {
            default:
            case '=':
            case '==':  return $leftValue == $rightValue;
            case '!=':
            case '<>':  return $leftValue != $rightValue;
            case '<':   return (float)$leftValue < (float)$rightValue;
            case '>':   return (float)$leftValue > (float)$rightValue;
            case '<=':  return (float)$leftValue <= (float)$rightValue;
            case '>=':  return (float)$leftValue >= (float)$rightValue;
            case '===': return $leftValue === $rightValue;
            case '!==': return $leftValue !== $rightValue;
        }
    }
}
