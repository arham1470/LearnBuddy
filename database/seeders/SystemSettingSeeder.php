<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            [
                'key' => 'site_name',
                'value' => 'LearnBuddy LMS Sri Lanka',
                'group' => 'general',
                'type' => 'string',
                'label' => 'Platform Name',
                'description' => 'The brand name displayed across emails, receipts, and pages.',
            ],
            [
                'key' => 'currency_code',
                'value' => 'LKR',
                'group' => 'localization',
                'type' => 'string',
                'label' => 'Default Currency',
                'description' => 'Sri Lankan Rupee (LKR) transaction and display currency.',
            ],
            [
                'key' => 'currency_symbol',
                'value' => 'Rs. ',
                'group' => 'localization',
                'type' => 'string',
                'label' => 'Currency Symbol',
                'description' => 'Symbol prefixed to prices on customer receipts and store pages.',
            ],
            [
                'key' => 'timezone',
                'value' => 'Asia/Colombo',
                'group' => 'localization',
                'type' => 'string',
                'label' => 'Default Timezone',
                'description' => 'Standard timezone for class scheduling, deadlines, and notifications.',
            ],
            [
                'key' => 'default_physical_capacity',
                'value' => '30',
                'group' => 'courses',
                'type' => 'integer',
                'label' => 'Default Physical Classroom Capacity',
                'description' => 'Maximum student seats allocated when a new in-person batch is created.',
            ],
            [
                'key' => 'tax_rate_percent',
                'value' => '0',
                'group' => 'billing',
                'type' => 'float',
                'label' => 'Applicable VAT/Tax Rate (%)',
                'description' => 'Configurable Sri Lankan tax rate. Zero if not registered for VAT.',
            ],
            [
                'key' => 'invoice_company_name',
                'value' => 'LearnBuddy Academy (Pvt) Ltd',
                'group' => 'billing',
                'type' => 'string',
                'label' => 'Billing Legal Entity',
                'description' => 'Legal business entity printed on official student tax receipts.',
            ],
            [
                'key' => 'jitsi_domain',
                'value' => 'meet.jit.si',
                'group' => 'providers',
                'type' => 'string',
                'label' => 'Jitsi Live Meeting Domain',
                'description' => 'Hosted Jitsi Meet domain for interactive virtual classrooms.',
            ],
        ];

        foreach ($defaults as $setting) {
            SystemSetting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
