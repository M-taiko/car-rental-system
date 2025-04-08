<?php

namespace Database\Seeders; // Ensure the correct namespace for Laravel 10

use Illuminate\Database\Seeder;
use App\Models\User; // Adjust this if your User model is in a different namespace
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create the user
        $user = User::create([
            'name' => 'Mohamed Tarek Hussain',
            'email' => 'donia.a5ra2019@gmail.com',
            'password' => bcrypt('123456789'), // Ensure strong password hashing
            'roles_name' => 'SuperAdmin',
            'status' =>'active'
        ]);

        // Create the role if it doesn't exist
        $role = Role::firstOrCreate(['name' => 'SuperAdmin']);

        // Sync permissions
        $permissions = Permission::pluck('id' ,'id')->all();

        $role->syncPermissions($permissions);

        // Assign the role to the user
        $user->assignRole([$role->id]);
    }
}
