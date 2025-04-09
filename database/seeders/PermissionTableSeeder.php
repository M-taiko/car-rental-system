<?php

namespace Database\Seeders;

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
            // أذونات الدراجات
            'view-bikes',
            'create-bikes',
            'edit-bikes',
            'delete-bikes',

            // أذونات الإيجارات
            'view-rentals',
            'create-rentals',
            'edit-rentals',
            'delete-rentals',
            'return-rentals', // لإرجاع الدراجة
            'create-rental-invoice', // لإنشاء فاتورة إيجار

            // أذونات قطع الغيار
            'view-spare-parts',
            'create-spare-parts',
            'edit-spare-parts',
            'delete-spare-parts',

            // أذونات مبيعات قطع الغيار
            'view-spare-part-sales',
            'create-spare-part-sales',
            'delete-spare-part-sales',

            // أذونات الحسابات
            'view-accounts',
            'create-accounts',
            'delete-accounts',

            // أذونات الصيانة
            'view-maintenance',
            'create-maintenance',
            'edit-maintenance',
            'delete-maintenance',
            'complete-maintenance', // لإكمال الصيانة
            'create-maintenance-invoice', // لإنشاء فاتورة صيانة

            // أذونات المصروفات
            'view-expenses',
            'create-expenses',
            'delete-expenses',

            // أذونات المستخدمين
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',

            // أذونات العملاء
            'view-customers',
            'create-customers',
            'edit-customers',
            'delete-customers',

            // أذونات التقارير
            'view-reports', // لعرض التقارير (مثل تقرير أرباح قطع الغيار)
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission]);
        }
    }
}
