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
        // تشغيل الـ Seeders للأذونات والأدوار
        $this->call([
            PermissionTableSeeder::class,
            RoleTableSeeder::class,
        ]);

        // التحقق من وجود دور SuperAdmin
        $superAdminRole = Role::where('name', 'SuperAdmin')->first();
        if (!$superAdminRole) {
            throw new \Exception('Role "SuperAdmin" not found. Please ensure RoleTableSeeder creates this role.');
        }

        // إنشاء مستخدم Super Admin أو تحديثه إذا كان موجودًا
        $superAdmin = User::updateOrCreate(
            ['email' => 'donia.a5ra2019@gmail.com'],
            [
                'name' => 'Mohamed Tarek Hussain',
                'password' => bcrypt('123456789'),
                // أضف الحقول دي فقط لو موجودة في جدول users وفي $fillable
                'status' => 'active',
            ]
        );

        // تعيين دور SuperAdmin للمستخدم
        $superAdmin->assignRole('SuperAdmin');
    }
}
