<?php

namespace Database\Seeders; // Ensure the correct namespace for Laravel 10

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'user',
            'admin',
            'superadmin',
            'update',
            'edite',
            'delete',
            'tenant',
            'posts',
            'channels',
            

        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission]);
        }
    }
}
