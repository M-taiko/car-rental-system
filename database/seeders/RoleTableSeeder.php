<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create basic roles if they don't exist
        $roles = [
            'superadmin',
            'admin',
            'accountant',
            'salesperson',
            'technician',
            'rental-agent'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Update permissions for existing roles
        $superadmin = Role::where('name', 'superadmin')->first();
        if ($superadmin) {
            $superadmin->syncPermissions(Permission::all());
        }

        $admin = Role::where('name', 'admin')->first();
        if ($admin) {
            $admin->syncPermissions([
                'driver-list', 'driver-create', 'driver-edit', 'driver-delete',
                'user-list', 'user-create', 'user-edit',
                'car-list', 'car-create', 'car-edit',
                'rental-list', 'rental-create', 'rental-edit', 'rental-return',
                'customer-list', 'customer-create', 'customer-edit',
                'account-list', 'account-create', 'account-edit',
                'maintenance-list', 'maintenance-create', 'maintenance-complete'
            ]);
        }

        $accountant = Role::where('name', 'accountant')->first();
        if ($accountant) {
            $accountant->syncPermissions([
                'account-list', 'account-create', 'account-edit',
                'rental-list', 'customer-list'
            ]);
        }

        $salesperson = Role::where('name', 'salesperson')->first();
        if ($salesperson) {
            $salesperson->syncPermissions([
                'car-list',
                'rental-list', 'rental-create',
                'customer-list', 'customer-create',
                'account-create'
            ]);
        }

        $technician = Role::where('name', 'technician')->first();
        if ($technician) {
            $technician->syncPermissions([
                'maintenance-list', 'maintenance-create', 'maintenance-complete',
                'car-list'
            ]);
        }

        $rentalAgent = Role::where('name', 'rental-agent')->first();
        if ($rentalAgent) {
            $rentalAgent->syncPermissions([
                'rental-list', 'rental-create', 'rental-return',
                'customer-list', 'customer-create',
                'car-list',
                'account-create'
            ]);
        }
    }
}
