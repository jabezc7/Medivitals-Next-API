<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Type;

class TypesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Permission & Group Types
            [
                'name' => 'System',
                'group' => 'permission-types',
                'locked' => true,
            ],
            [
                'name' => 'System',
                'group' => 'group-types',
                'locked' => true,
            ],
            // Simple Options
            [
                'name' => 'Yes',
                'group' => 'simple-options',
                'ordering' => 1,
                'locked' => true
            ],
            [
                'name' => 'No',
                'group' => 'simple-options',
                'ordering' => 2,
                'locked' => true
            ],
            [
                'name' => 'N/A',
                'group' => 'simple-options',
                'ordering' => 3,
                'locked' => true
            ],
            // Templates
            [
                'name' => 'Document',
                'group' => 'template-types',
                'locked' => true,
            ],
            [
                'name' => 'Email',
                'group' => 'template-types',
                'locked' => true,
            ],
            [
                'name' => 'SMS',
                'group' => 'template-types',
                'locked' => true,
            ],
            // Vitals
            [
                'name' => 'Blood Pressure (Systolic)',
                'group' => 'vital-types',
                'locked' => true,
            ],
            [
                'name' => 'Blood Pressure (Diastolic)',
                'group' => 'vital-types',
                'locked' => true,
            ],
            [
                'name' => 'Heart Rate',
                'group' => 'vital-types',
                'locked' => true,
            ],
            [
                'name' => 'Temperature',
                'group' => 'vital-types',
                'locked' => true,
            ],
            [
                'name' => 'Oxygen Saturation',
                'group' => 'vital-types',
                'locked' => true,
            ],
            // Actions
            [
                'name' => 'Send Email',
                'group' => 'automation-actions',
                'locked' => true,
            ],
            [
                'name' => 'Send SMS',
                'group' => 'automation-actions',
                'locked' => true,
            ],
            [
                'name' => 'Send Notification to Device',
                'group' => 'automation-actions',
                'locked' => true,
            ],
            [
                'name' => 'Change Testing Frequency',
                'group' => 'automation-actions',
                'locked' => true,
            ],
            [
                'name' => 'Create Alert',
                'group' => 'automation-actions',
                'locked' => true,
            ],
        ];

        foreach ($data as $type) {
            Type::query()->create($type);
        }
    }
}
