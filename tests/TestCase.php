<?php

namespace Tests;

use App\Models\Type;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseTransactions;
//    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Vital Types
        Type::factory()->create([
            'name' => 'Blood Pressure (Systolic)',
            'group' => 'vital-types'
        ]);

        Type::factory()->create([
            'name' => 'Blood Pressure (Diastolic)',
            'group' => 'vital-types'
        ]);

        Type::factory()->create([
            'name' => 'Heart Rate',
            'group' => 'vital-types'
        ]);

        Type::factory()->create([
            'name' => 'Temperature',
            'group' => 'vital-types'
        ]);

        Type::factory()->create([
            'name' => 'Oxygen Saturation',
            'group' => 'vital-types'
        ]);

        // Automation Actions
        Type::factory()->create([
            'name' => 'Send Email',
            'group' => 'automation-actions'
        ]);

        Type::factory()->create([
            'name' => 'Send SMS',
            'group' => 'automation-actions'
        ]);

        Type::factory()->create([
            'name' => 'Send Notification to Device',
            'group' => 'automation-actions'
        ]);

        Type::factory()->create([
            'name' => 'Change Testing Frequency',
            'group' => 'automation-actions'
        ]);

        Type::factory()->create([
            'name' => 'Create Alert',
            'group' => 'automation-actions'
        ]);
    }
}
