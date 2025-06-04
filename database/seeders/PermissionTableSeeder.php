<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // المستخدمين والأدوار
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            // السيارات
            'car-list',
            'car-create',
            'car-edit',
            'car-delete',

            // السائقين
            'driver-list',
            'driver-create',
            'driver-edit',
            'driver-delete',

            // العملاء
            'customer-list',
            'customer-create',
            'customer-edit',
            'customer-delete',

            // التأجير
            'rental-list',
            'rental-create',
            'rental-edit',
            'rental-delete',
            'rental-return',

            // الصيانة
            'maintenance-list',
            'maintenance-create',
            'maintenance-edit',
            'maintenance-delete',
            'maintenance-complete',

            // قطع الغيار
            'spare-part-list',
            'spare-part-create',
            'spare-part-edit',
            'spare-part-delete',

            // المصروفات
            'expense-list',
            'expense-create',
            'expense-edit',
            'expense-delete',

            // الحسابات
            'account-list',
            'account-create',
            'account-edit',
            'account-delete',

            // الإعدادات
            'setting-list',
            'setting-edit',
            
            // التقارير
            'report-view-rentals',
            'report-view-third-party-cars',
            'report-view-car-types',
            'report-view-monthly-revenue',
            'report-export',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
