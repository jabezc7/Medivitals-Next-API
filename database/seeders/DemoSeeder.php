<?php

namespace Database\Seeders;

use App\Models\Automation;
use App\Models\Data;
use App\Models\Device;
use App\Models\Group;
use App\Models\Note;
use App\Models\Notification;
use App\Models\Patient;
use App\Models\Template;
use App\Models\Type;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoSeeder extends Seeder
{
    public function run()
    {
        // Create Doctor
        $doctor = User::query()->create([
            'first' => 'Nick',
            'last' => 'Riviera',
            'email' => 'dr.nick@medivitals.app',
            'password' => 'password',
        ]);

        $doctor->groups()->attach(Group::query()->where('name', 'Stakeholder')->first());

        // Create Device
        Device::query()->create([
            'imei' => '865513041160034',
            'number' => '0472 595 900',
            'nickname' => 'Bio-Band #19'
        ]);

        // Mock Device
        $mockDevice = Device::query()->create([
            'imei' => '111111111111111',
            'number' => '0411 111 111',
            'nickname' => 'Mock Device'
        ]);

        // Create Patient
        $patient = Patient::query()->create([
            'first' => 'Peter',
            'last' => 'Patient',
            'email' => 'peter@medivitals.app',
            'mobile' => '0411 111 111',
            'phone' => '9444 5555',
            'active' => true,
            'address_1' => '123 Fake Street',
            'suburb' => 'Perth',
            'postcode' => 6000,
            'state' => 'WA',
            'country' => 'Australia',
            'medicare_number' => '1234 12345 1',
            'medicare_expiry' => '11/25',
            'medicare_position' => '1',
            'private_health_fund' => 'HBF',
            'private_health_membership_no' => '123456789',
            'gp_medical_centre' => 'Springfield Medical',
            'gp_name' => 'Dr Dennis',
            'gp_phone' => '9123 1234',
            'gp_email' => 'dr.dennis@springfieldmedical.io',
            'assignee_id' => $doctor->id,
        ]);

        // Attach Watch to
        $mockDevice->patients()->attach($patient);

        // Create Additional Patients
        Patient::factory(30)->create([
            'assignee_id' => $doctor->id,
        ]);

        $start = now()->subDays(3);
        $end = now();

        while($start->lte($end)){
            $this->generateSetOfData($patient->id, $mockDevice->id, $start);
            $start->addHour();
        }

        Note::factory(30)->create([
            'created_by' => $doctor->id
        ]);

        Note::factory(25)->create([
            'created_by' => User::query()->where('email', 'ryan@niftee.com.au')->first()->id
        ]);

        Notification::factory(30)->create();

        Notification::factory()->alert()->count(20)->create();

        Patient::all()->each(function($patient) use ($doctor) {
             DB::table('patient_views')->insert([
                 'patient_id' => $patient->id,
                 'user_id' => $doctor->id,
                 'last_viewed_at' => now()->subDays(rand(1,30))->subHours(rand(1,23))
             ]);
        });

        Template::query()->create([
            'type_id' => Type::query()->where('slug', 'template-types-sms')->first()->id,
            'name' => 'Running 15 Min Late',
            'content' => 'We are running 15 minutes late to your appointment.',
            'active' => true,
            'quick_link' => true
        ]);

        Template::query()->create([
            'type_id' => Type::query()->where('slug', 'template-types-sms')->first()->id,
            'name' => 'Running 30 Min Late',
            'content' => 'We are running 30 minutes late to your appointment.',
            'active' => true,
            'quick_link' => true
        ]);

        Template::query()->create([
            'type_id' => Type::query()->where('slug', 'template-types-sms')->first()->id,
            'name' => 'Recharge Your Device',
            'content' => 'This is a reminder to recharge your device',
            'active' => true,
            'quick_link' => true
        ]);

        Template::query()->create([
            'type_id' => Type::query()->where('slug', 'template-types-sms')->first()->id,
            'name' => 'Are You OK?',
            'content' => 'We are just checking in to see if you are ok?',
            'active' => true,
            'quick_link' => false
        ]);

        Template::query()->create([
            'type_id' => Type::query()->where('slug', 'template-types-sms')->first()->id,
            'name' => 'Do you need anything?',
            'content' => 'Hello, we are just checking in to see if you need anything',
            'active' => true,
            'quick_link' => false
        ]);

        Automation::query()->create([
            'name' => 'Global Demo Automation',
            'description' => 'Example Global Automation',
            'triggers' => [[
                'vital' => Type::query()->where('name', 'Heart Rate')->where('group', 'vital-types')->first()->slug,
                'operator' => '>',
                'value' => '120',
                'comparison' => [
                    'period' => 'reading',
                    'value' => '2'
                ]
            ]],
            'actions' => [
                [
                    'action' => $this->getType('Change Testing Frequency', 'automation-actions')->id,
                    'vital' => $this->getType('Temperature', 'vital-types')->slug,
                    'value' => '15'
                ],
                [
                    'action' => $this->getType('Create Alert', 'automation-actions')->id,
                    'content' => 'Patient Heart Rate is Very High',
                    'priority' => 'High'
                ],
                [
                    'action' => $this->getType('Send Email', 'automation-actions')->id,
                    'content' => 'Just letting you know this patient has a high heart rate',
                    'to' => 'tester@niftee.com.au,dr.nick@niftee.com.au'
                ],
                [
                    'action' => $this->getType('Send SMS', 'automation-actions')->id,
                    'content' => 'Just letting you know this patient has a high heart rate',
                    'to' => '0426191924'
                ],
                [
                    'action' => $this->getType('Send Notification to Device', 'automation-actions')->id,
                    'content' => 'Are you Ok?',
                ],
            ],
            'global' => true
        ]);

        $patient->automations()->create([
            'name' => 'Demo Automation',
            'description' => 'Example Automation',
            'triggers' => [[
                'vital' => Type::query()->where('name', 'Temperature')->where('group', 'vital-types')->first()->slug,
                'operator' => '>',
                'value' => '38',
                'comparison' => [
                    'period' => 'reading',
                    'value' => '2'
                ]
            ]],
            'actions' => [
                [
                    'action' => $this->getType('Change Testing Frequency', 'automation-actions')->id,
                    'vital' => $this->getType('Temperature', 'vital-types')->slug,
                    'value' => '5'
                ],
                [
                    'action' => $this->getType('Create Alert', 'automation-actions')->id,
                    'content' => 'Patient Temperature is Very High',
                ],
                [
                    'action' => $this->getType('Send Email', 'automation-actions')->id,
                    'content' => 'Just letting you know this patient has a high temperature',
                    'to' => 'tester@niftee.com.au,dr.nick@niftee.com.au'
                ],
                [
                    'action' => $this->getType('Send SMS', 'automation-actions')->id,
                    'content' => 'Just letting you know this patient has a high temperature',
                    'to' => '0426191924'
                ],
                [
                    'action' => $this->getType('Send Notification to Device', 'automation-actions')->id,
                    'content' => 'Are you Ok?',
                ],
            ],
        ]);
    }

    private function getType($name, $group): Model|Builder|null
    {
        return Type::query()
            ->where('name', $name)
            ->where('group', $group)
            ->first();
    }

    private function generateSetOfData($patientId, $deviceId, $datetime)
    {
        Data::query()->create([
            'type' => 'heart_rate',
            'value' => rand(50, 120),
            'patient_id' => $patientId,
            'device_id' => $deviceId,
            'created_at' => $datetime
        ]);

        Data::query()->create([
            'type' => 'blood_pressure',
            'value' => rand(115,130) . '/' . rand(70,90),
            'patient_id' => $patientId,
            'device_id' => $deviceId,
            'created_at' => $datetime
        ]);

        Data::query()->create([
            'type' => 'oxygen_saturation',
            'value' => rand(90, 100),
            'patient_id' => $patientId,
            'device_id' => $deviceId,
            'created_at' => $datetime
        ]);

        Data::query()->create([
            'type' => 'temperature',
            'value' => rand(36*10, 37.9*10) / 10,
            'patient_id' => $patientId,
            'device_id' => $deviceId,
            'created_at' => $datetime
        ]);
    }
}
