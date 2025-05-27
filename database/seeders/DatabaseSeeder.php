<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            PermissionTableSeeder::class,
            RoleTableSeeder::class,
            CreateAdminUserSeeder::class,
        ]);

        // التحقق من وجود دور superadmin
        $superAdminRole = Role::where('name', 'superadmin')->first();
        if (!$superAdminRole) {
            throw new \Exception('Role "superadmin" not found. Please ensure RoleTableSeeder creates this role.');
        }

        // إنشاء مستخدم Super Admin أو تحديثه إذا كان موجودًا
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('123456'),
                'status' => 'active'
            ]
        );

        // إسناد دور superadmin للمستخدم
        $superAdmin->assignRole('superadmin');
    }
}
