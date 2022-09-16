<?php

namespace Tests\Unit\Listeners;

use App\Mail\AutomationEmail;
use App\Models\Automation;
use App\Models\Data;
use App\Models\Device;
use App\Models\Patient;
use App\Models\Type;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ProcessDataAutomationsTest extends TestCase
{
    protected Patient $patient;

    public function setUp(): void
    {
        parent::setUp();

        $this->patient = Patient::factory()->hasAttached(
            Device::factory([
                'imei' => '111111111111111'
            ])->count(1)
        )->create();
    }

    public function test_change_frequency_action_fires_when_triggers_pass()
    {
        Http::fake();

        Automation::factory([
            'triggers' => [
                $this->heartRateTrigger('121', '>=')
            ],
            'actions' => [
                $this->changeFrequencyAction('Heart Rate', '10')
            ],
            'global' => true
        ])->create();

        // Add some existing data
        Data::factory(['patient_id' => $this->patient->id])
            ->heartRate(121)
            ->count(3)
            ->createQuietly();

        // This will meet the trigger criteria
        Data::factory(['patient_id' => $this->patient->id])
            ->heartRate(121)
            ->create();

        Http::assertSent(function ($request) {
            return $request->url() === config('services.websocket.endpoint') . '/command' &&
                $request['imei'] === '111111111111111' &&
                $request['data'] === 'IWBP86,111111111111111,080835,1,10#';
        });
    }

    public function test_send_email_action_fires_when_triggers_pass()
    {
        Mail::fake();

        Automation::factory([
            'triggers' => [
                $this->heartRateTrigger('121', '>=')
            ],
            'actions' => [
                $this->sendEmailAction(to: 'a@a.com,b@b.com')
            ],
            'global' => true
        ])->create();

        // Add some existing data
        Data::factory(['patient_id' => $this->patient->id])
            ->heartRate(121)
            ->count(3)
            ->createQuietly();

        // This will meet the trigger criteria
        Data::factory(['patient_id' => $this->patient->id])
            ->heartRate(121)
            ->create();

        Mail::assertQueued(AutomationEmail::class, 2);
    }

    public function test_send_sms_action_fires_when_triggers_pass()
    {
        Http::fake();

        Automation::factory([
            'triggers' => [
                $this->heartRateTrigger('121', '>=')
            ],
            'actions' => [
                $this->sendSmsAction('Testing SMS Message', '4000000000')
            ],
            'global' => true
        ])->create();

        // Add some existing data
        Data::factory(['patient_id' => $this->patient->id])
            ->heartRate(121)
            ->count(3)
            ->createQuietly();

        // This will meet the trigger criteria
        Data::factory(['patient_id' => $this->patient->id])
            ->heartRate(121)
            ->create();

        Http::assertSent(function ($request) {
            return $request->url() === 'https://www.smsbroadcast.com.au/api-adv.php' &&
                $request['to'] === '4000000000' &&
                $request['message'] === 'Testing SMS Message';
        });
    }

    public function test_device_notification_action_fires_when_triggers_pass()
    {
        Http::fake();

        Automation::factory([
            'triggers' => [
                $this->heartRateTrigger('121', '>=')
            ],
            'actions' => [
                $this->sendNotificationToDeviceAction('Device Notification')
            ],
            'global' => true
        ])->create();

        // Add some existing data
        Data::factory(['patient_id' => $this->patient->id])
            ->heartRate(121)
            ->count(3)
            ->createQuietly();

        // This will meet the trigger criteria
        Data::factory(['patient_id' => $this->patient->id])
            ->heartRate(121)
            ->create();

        Http::assertSent(function ($request) {
            return $request->url() === config('services.websocket.endpoint') . '/command' &&
                $request['imei'] === '111111111111111' &&
                $request['data'] === 'Device Notification';
        });
    }

    public function test_create_alert_action_fires_when_triggers_pass()
    {
        Http::fake();

        Automation::factory([
            'triggers' => [
                $this->heartRateTrigger('121', '>=')
            ],
            'actions' => [
                $this->createAlertAction('Testy Alert McTestface')
            ],
            'global' => true
        ])->create();

        // Add some existing data
        Data::factory(['patient_id' => $this->patient->id])
            ->heartRate(121)
            ->count(3)
            ->createQuietly();

        // This will meet the trigger criteria
        Data::factory(['patient_id' => $this->patient->id])
            ->heartRate(121)
            ->create();

        $this->assertDatabaseHas('notifications' ,[
            'patient_id' => $this->patient->id,
            'alert' => true,
            'message' => 'Testy Alert McTestface'
        ]);
    }

    public function test_change_frequency_action_DOES_NOT_fire_when_triggers_fail()
    {
        Http::fake();

        Automation::factory([
            'triggers' => [
                $this->heartRateTrigger('121', '<')
            ],
            'actions' => [
                $this->changeFrequencyAction('Heart Rate', '10')
            ],
            'global' => true
        ])->create();

        // Add some existing data
        Data::factory(['patient_id' => $this->patient->id])
            ->heartRate(121)
            ->count(3)
            ->createQuietly();

        // This will meet the trigger criteria
        Data::factory(['patient_id' => $this->patient->id])
            ->heartRate(121)
            ->create();

        Http::assertNotSent(function ($request) {
            return $request->url() === config('services.websocket.endpoint') . '/command' &&
                $request['imei'] === '111111111111111' &&
                $request['data'] === 'IWBP86,111111111111111,080835,1,10#';
        });
    }

    public function test_send_email_action_DOES_NOT_fire_when_triggers_fail()
    {
        Mail::fake();

        Automation::factory([
            'triggers' => [
                $this->heartRateTrigger('121', '<')
            ],
            'actions' => [
                $this->sendEmailAction(to: 'a@a.com,b@b.com')
            ],
            'global' => true
        ])->create();

        // Add some existing data
        Data::factory(['patient_id' => $this->patient->id])
            ->heartRate(121)
            ->count(3)
            ->createQuietly();

        // This will meet the trigger criteria
        Data::factory(['patient_id' => $this->patient->id])
            ->heartRate(121)
            ->create();

        Mail::assertNotQueued(AutomationEmail::class);
    }

    public function test_send_sms_action_DOES_NOT_fire_when_triggers_fail()
    {
        Http::fake();

        Automation::factory([
            'triggers' => [
                $this->heartRateTrigger('121', '<')
            ],
            'actions' => [
                $this->sendSmsAction('Testing SMS Message', '4000000000')
            ],
            'global' => true
        ])->create();

        // Add some existing data
        Data::factory(['patient_id' => $this->patient->id])
            ->heartRate(121)
            ->count(3)
            ->createQuietly();

        // This will meet the trigger criteria
        Data::factory(['patient_id' => $this->patient->id])
            ->heartRate(121)
            ->create();

        Http::assertNotSent(function ($request) {
            return $request->url() === 'https://www.smsbroadcast.com.au/api-adv.php' &&
                $request['to'] === '4000000000' &&
                $request['message'] === 'Testing SMS Message';
        });
    }

    public function test_device_notification_action_DOES_NOT_fire_when_triggers_fail()
    {
        Http::fake();

        Automation::factory([
            'triggers' => [
                $this->heartRateTrigger('121', '<')
            ],
            'actions' => [
                $this->sendNotificationToDeviceAction('Device Notification')
            ],
            'global' => true
        ])->create();

        // Add some existing data
        Data::factory(['patient_id' => $this->patient->id])
            ->heartRate(121)
            ->count(3)
            ->createQuietly();

        // This will meet the trigger criteria
        Data::factory(['patient_id' => $this->patient->id])
            ->heartRate(121)
            ->create();

        Http::assertNotSent(function ($request) {
            return $request->url() === config('services.websocket.endpoint') . '/command' &&
                $request['imei'] === '111111111111111' &&
                $request['data'] === 'Device Notification';
        });
    }

    public function test_create_alert_action_DOES_NOT_fire_when_triggers_fail()
    {
        Http::fake();

        Automation::factory([
            'triggers' => [
                $this->heartRateTrigger('121', '<')
            ],
            'actions' => [
                $this->createAlertAction('Testy Alert McTestface')
            ],
            'global' => true
        ])->create();

        // Add some existing data
        Data::factory(['patient_id' => $this->patient->id])
            ->heartRate(121)
            ->count(3)
            ->createQuietly();

        // This will meet the trigger criteria
        Data::factory(['patient_id' => $this->patient->id])
            ->heartRate(121)
            ->create();

        $this->assertDatabaseMissing('notifications' ,[
            'patient_id' => $this->patient->id,
            'alert' => true,
            'message' => 'Testy Alert McTestface'
        ]);
    }

    public function test_action_fires_when_temperature_trigger_passes()
    {
        Http::fake();

        Automation::factory([
            'triggers' => [
                $this->temperatureTrigger('39', '>', 1)
            ],
            'actions' => [
                $this->sendNotificationToDeviceAction('Device Notification')
            ],
            'global' => true
        ])->create();

        // Add some existing data
        Data::factory(['patient_id' => $this->patient->id])
            ->temperature(36.6)
            ->createQuietly();

        // This will meet the trigger criteria
        Data::factory(['patient_id' => $this->patient->id])
            ->temperature(40)
            ->create();

        Http::assertSent(function ($request) {
            return $request->url() === config('services.websocket.endpoint') . '/command' &&
                $request['imei'] === '111111111111111' &&
                $request['data'] === 'Device Notification';
        });
    }

    public function test_action_fires_when_blood_pressure_trigger_passes()
    {
        Http::fake();

        Automation::factory([
            'triggers' => [
                $this->bloodPressureSystolicTrigger(),
                $this->bloodPressureDiastolicTrigger()
            ],
            'actions' => [
                $this->sendNotificationToDeviceAction('Device Notification')
            ],
            'global' => true
        ])->create();

        // Add some existing data
        Data::factory(['patient_id' => $this->patient->id])
            ->bloodPressure('121/81')
            ->createQuietly();

        // This will meet the trigger criteria
        Data::factory(['patient_id' => $this->patient->id])
            ->bloodPressure('121/81')
            ->create();

        Http::assertSent(function ($request) {
            return $request->url() === config('services.websocket.endpoint') . '/command' &&
                $request['imei'] === '111111111111111' &&
                $request['data'] === 'Device Notification';
        });
    }

    public function test_action_DOES_NOT_fire_when_blood_pressure_trigger_fails()
    {
        Http::fake();

        Automation::factory([
            'triggers' => [
                $this->bloodPressureSystolicTrigger(),
                $this->bloodPressureDiastolicTrigger()
            ],
            'actions' => [
                $this->sendNotificationToDeviceAction('Device Notification')
            ],
            'global' => true
        ])->create();

        // Add some existing data
        Data::factory(['patient_id' => $this->patient->id])
            ->bloodPressure('110/81')
            ->createQuietly();

        // This will meet the trigger criteria
        Data::factory(['patient_id' => $this->patient->id])
            ->bloodPressure('121/70')
            ->create();

        Http::assertNotSent(function ($request) {
            return $request->url() === config('services.websocket.endpoint') . '/command' &&
                $request['imei'] === '111111111111111' &&
                $request['data'] === 'Device Notification';
        });
    }

    private function getType($name, $group): Model|Builder|null
    {
        return Type::query()
            ->where('name', $name)
            ->where('group', $group)
            ->first();
    }

    private function heartRateTrigger($value = '120', $operator = '>', $readings = '2'): array
    {
        return [
            'vital' => Type::query()->where('name', 'Heart Rate')->where('group', 'vital-types')->first()->slug,
            'operator' => $operator,
            'value' => $value,
            'comparison' => [
                'period' => 'reading',
                'value' => $readings
            ]
        ];
    }

    private function temperatureTrigger($value = '38', $operator = '>', $readings = '2'): array
    {
        return [
            'vital' => Type::query()->where('name', 'Temperature')->where('group', 'vital-types')->first()->slug,
            'operator' => $operator,
            'value' => $value,
            'comparison' => [
                'period' => 'reading',
                'value' => $readings
            ]
        ];
    }

    private function bloodPressureSystolicTrigger($value = '120', $operator = '>', $readings = '2'): array
    {
        return [
            'vital' => Type::query()->where('name', 'Blood Pressure (Systolic)')->where('group', 'vital-types')->first()->slug,
            'operator' => $operator,
            'value' => $value,
            'comparison' => [
                'period' => 'reading',
                'value' => $readings
            ]
        ];
    }

    private function bloodPressureDiastolicTrigger($value = '80', $operator = '>', $readings = '2'): array
    {
        return [
            'vital' => Type::query()->where('name', 'Blood Pressure (Diastolic)')->where('group', 'vital-types')->first()->slug,
            'operator' => $operator,
            'value' => $value,
            'comparison' => [
                'period' => 'reading',
                'value' => $readings
            ]
        ];
    }

    private function changeFrequencyAction($vital = 'Temperature', $value = '15'): array
    {
        return [
            'action' => $this->getType('Change Testing Frequency', 'automation-actions')->id,
            'vital' => $this->getType($vital, 'vital-types')->slug,
            'value' => $value
        ];
    }

    private function createAlertAction($content = 'Dummy Alert Content'): array
    {
        return [
            'action' => $this->getType('Create Alert', 'automation-actions')->id,
            'content' => $content
        ];
    }

    private function sendEmailAction($content = 'Dummy Alert Content', $to = 'tester@niftee.com.au,dr.nick@niftee.com.au'): array
    {
        return [
            'action' => $this->getType('Send Email', 'automation-actions')->id,
            'content' => $content,
            'to' => $to
        ];
    }

    private function sendSmsAction($content = 'Dummy Alert Content', $to = 'tester@niftee.com.au,dr.nick@niftee.com.au'): array
    {
        return [
            'action' => $this->getType('Send SMS', 'automation-actions')->id,
            'content' => $content,
            'to' => $to
        ];
    }

    private function sendNotificationToDeviceAction($content = 'Dummy Alert Content'): array
    {
        return [
            'action' => $this->getType('Send Notification to Device', 'automation-actions')->id,
            'content' => $content,
        ];
    }
}
