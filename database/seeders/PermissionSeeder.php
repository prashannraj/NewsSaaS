<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'View Articles', 'slug' => 'articles.view', 'module' => 'articles'],
            ['name' => 'Create Articles', 'slug' => 'articles.create', 'module' => 'articles'],
            ['name' => 'Update Articles', 'slug' => 'articles.update', 'module' => 'articles'],
            ['name' => 'Publish Articles', 'slug' => 'articles.publish', 'module' => 'articles'],
            ['name' => 'Delete Articles', 'slug' => 'articles.delete', 'module' => 'articles'],
            ['name' => 'Manage Media', 'slug' => 'media.manage', 'module' => 'media'],
            ['name' => 'Manage Categories', 'slug' => 'categories.manage', 'module' => 'categories'],
            ['name' => 'Manage Menus', 'slug' => 'menus.manage', 'module' => 'menus'],
            ['name' => 'Manage Domains', 'slug' => 'domains.manage', 'module' => 'domains'],
            ['name' => 'View Billing', 'slug' => 'billing.view', 'module' => 'billing'],
            ['name' => 'Manage Billing', 'slug' => 'billing.manage', 'module' => 'billing'],
            ['name' => 'Invite Members', 'slug' => 'members.invite', 'module' => 'members'],
            ['name' => 'Manage Members', 'slug' => 'members.manage', 'module' => 'members'],
            ['name' => 'View Analytics', 'slug' => 'analytics.view', 'module' => 'analytics'],
            ['name' => 'Manage Settings', 'slug' => 'settings.manage', 'module' => 'settings'],
            ['name' => 'Manage Support', 'slug' => 'support.manage', 'module' => 'support'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['slug' => $permission['slug']], $permission);
        }
    }
}
