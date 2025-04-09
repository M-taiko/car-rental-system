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
        // إنشاء الأدوار
        $superAdmin = Role::updateOrCreate(['name' => 'superadmin']);
        $admin = Role::updateOrCreate(['name' => 'admin']);
        $accountant = Role::updateOrCreate(['name' => 'accountant']);
        $salesperson = Role::updateOrCreate(['name' => 'salesperson']);
        $technician = Role::updateOrCreate(['name' => 'technician']);
        $rentalAgent = Role::updateOrCreate(['name' => 'rental-agent']);

        // تحديد الأذونات لكل دور

        // Super Admin: ليه صلاحية على كل حاجة
        $superAdminPermissions = Permission::all()->pluck('name')->toArray();
        $superAdmin->syncPermissions($superAdminPermissions);

        // Admin: ليه صلاحية على كل حاجة ما عدا حذف المستخدمين والحسابات
        $adminPermissions = array_diff($superAdminPermissions, ['delete-users', 'delete-accounts']);
        $admin->syncPermissions($adminPermissions);

        // Accountant (محاسب): بيتعامل مع الحسابات والمصروفات والتقارير
        $accountantPermissions = [
            'view-accounts',
            'create-accounts',
            'delete-accounts',
            'view-expenses',
            'create-expenses',
            'delete-expenses',
            'view-reports',
            'create-rental-invoice',
            'create-maintenance-invoice',
        ];
        $accountant->syncPermissions($accountantPermissions);

        // Salesperson (بياع): بيتعامل مع مبيعات قطع الغيار
        $salespersonPermissions = [
            'view-spare-parts',
            'view-spare-part-sales',
            'create-spare-part-sales',
            'delete-spare-part-sales',
        ];
        $salesperson->syncPermissions($salespersonPermissions);

        // Technician (فني تصليحات): بيتعامل مع الصيانة
        $technicianPermissions = [
            'view-maintenance',
            'create-maintenance',
            'edit-maintenance',
            'delete-maintenance',
            'complete-maintenance',
            'create-maintenance-invoice',
            'view-spare-parts', // عشان يقدر يستخدم قطع الغيار في الصيانة
        ];
        $technician->syncPermissions($technicianPermissions);

        // Rental Agent (الشخص اللي بيأجر الدراجات): بيتعامل مع الإيجارات والعملاء
        $rentalAgentPermissions = [
            'view-rentals',
            'create-rentals',
            'edit-rentals',
            'delete-rentals',
            'return-rentals',
            'create-rental-invoice',
            'view-customers',
            'create-customers',
            'edit-customers',
            'delete-customers',
            'view-bikes',
        ];
        $rentalAgent->syncPermissions($rentalAgentPermissions);
    }
}
