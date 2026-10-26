<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\OrganizationService;
use Illuminate\Database\Seeder;

class DemoOrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::factory()->create([
            'name' => 'Demo Owner',
            'email' => 'demo@example.com',
            'is_platform_admin' => false,
        ]);

        $organization = app(OrganizationService::class)->create($owner, [
            'name' => 'Demo News',
            'subdomain' => 'demo',
        ]);

        $this->command?->info('Demo organization created: '.$organization->name);
    }
}
