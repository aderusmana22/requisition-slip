<?php

namespace Database\Seeders;

use App\Models\Master\Department;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AllSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Create Permissions
         Permission::create(['name' => 'view role']);
        Permission::create(['name' => 'create role']);
        Permission::create(['name' => 'update role']);
        Permission::create(['name' => 'delete role']);

        Permission::create(['name' => 'view permission']);
        Permission::create(['name' => 'create permission']);
        Permission::create(['name' => 'update permission']);
        Permission::create(['name' => 'delete permission']);

        Permission::create(['name' => 'view user']);
        Permission::create(['name' => 'create user']);
        Permission::create(['name' => 'update user']);
        Permission::create(['name' => 'delete user']);

        Permission::create(['name' => 'view department']);
        Permission::create(['name' => 'create department']);
        Permission::create(['name' => 'update department']);
        Permission::create(['name' => 'delete department']);

        Permission::create(['name' => 'view requisition']);
        Permission::create(['name' => 'create requisition']);
        Permission::create(['name' => 'update requisition']);
        Permission::create(['name' => 'delete requisition']);

        Permission::create(['name' => 'view approval']);

        Permission::create(['name' => 'view item']);
        Permission::create(['name' => 'create item']);
        Permission::create(['name' => 'update item']);
        Permission::create(['name' => 'delete item']);


        Permission::create(['name' => 'view customer']);
        Permission::create(['name' => 'create customer']);
        Permission::create(['name' => 'update customer']);
        Permission::create(['name' => 'delete customer']);

        Permission::create(['name' => 'view report']);
        Permission::create(['name' => 'create report']);
        Permission::create(['name' => 'update report']);
        Permission::create(['name' => 'delete report']);

        Permission::create(['name' => 'view approval-sequence']);
        Permission::create(['name' => 'update approval-sequence']);
        Permission::create(['name' => 'delete approval-sequence']);
        
        Permission::create(['name' => 'view requisition-approval']);
        Permission::create(['name' => 'approve requisition']);
        Permission::create(['name' => 'reject requisition']);
        
        Permission::create(['name' => 'view dashboard']);

        $now = Carbon::now();
        $departments = [
            [
                'id' => 1,
                'name' => 'Engineering & Maintenance',
                'code' => '5300',
                'slug' => 'engineering-maintainance',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Finance Admin',
                'code' => '5300',
                'slug' => 'finance-admin',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'HCD',
                'code' => '5300',
                'slug' => 'hcd',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'Manufacturing',
                'code' => '5300',
                'slug' => 'manufacturing',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'name' => 'QM & HSE',
                'code' => '5300',
                'slug' => 'qm-hse',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'name' => 'R&D',
                'code' => '5303',
                'slug' => 'rd',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'name' => 'Sales & Marketing',
                'code' => '5302',
                'slug' => 'sales-marketing',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 8,
                'name' => 'Supply Chain',
                'code' => '5300',
                'slug' => 'supply-chain',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 9,
                'name' => 'Supply & Maintenance',
                'code' => '5300',
                'slug' => 'supply-and-maintenance',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(['id' => $dept['id']], $dept);
        }

         // Create Roles
        $superAdminRole = Role::create(['name' => 'super-admin']); //as super-admin
        $userRequisitionRole = Role::create(['name' => 'user-requisition']); // as user-requisition
        $approvalRole = Role::create(['name' => 'user-approval']); // as head-department

        $headQA = Role::create(['name' => 'head-qa']); // as head-qa
        $headQA->givePermissionTo(['view requisition-approval', 'approve requisition', 'reject requisition']);
        $headHSE = Role::create(['name' => 'head-hse']);
        $headHSE->givePermissionTo(['view requisition-approval', 'approve requisition', 'reject requisition']);

        // Lets give all permission to super-admin role.
        $allPermissionNames = Permission::pluck('name')->toArray();
        $userRequisitionRole->givePermissionTo([
            'view requisition',
            'create requisition',
            'update requisition',
            'delete requisition',
        ]);
        $approvalRole->givePermissionTo([
            'view approval',
            'approve requisition',
        ]);


        $superAdminRole->givePermissionTo($allPermissionNames);
        $userRequisitionRole->givePermissionTo($userRequisitionRole);
        $approvalRole->givePermissionTo($approvalRole);

        $superAdminUser = \App\Models\User::updateOrCreate(
            ['email' => '092023090191@student.jgu.ac.id'],
            [
            'name' => 'Super Admin',
            'nik' => 'AG1111',
            'username' => 'superadmin',
            'email' => '092023090191@student.jgu.ac.id',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 1,
            'status' => 'active',
            'atasan_nik' => 'AG2222',
            'avatar' => null,
            ]
        );

        $superAdminUser->assignRole($superAdminRole);

        $userRequsition = \App\Models\User::updateOrCreate(
            ['email' => 'zidanazzahra916@gmail.com'],
            [
            'name' => 'User Requisition',
            'nik' => 'AG2222',
            'username' => 'user-requisition',
            'email' => 'zidanazzahra916@gmail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 2,
            'status' => 'active',
            'atasan_nik' => 'AG1111',
            'avatar' => null,
            ]
        );

        $userRequsition->assignRole($userRequisitionRole);

        $userApproval = \App\Models\User::updateOrCreate(
            ['email' => 'ziddanazzahra10@gmail.com'],
            [
            'name' => 'User Approval',
            'nik' => 'AG3333',
            'username' => 'user-approval',
            'email' => 'ziddanazzahra10@gmail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 3,
            'status' => 'active',
            'atasan_nik' => 'AG1111',
            'avatar' => null,
            ]
        );

        $userApproval->assignRole($approvalRole);

        $headQAUser = User::updateOrCreate(
            ['email' => 'head-qa@example.com'],
            [
                'name' => 'Head QA',
                'nik' => 'AG4444',
                'username' => 'head-qa',
                'email' => 'head-qa@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'department_id' => 5,
                'status' => 'active',
                'atasan_nik' => 'AG1111',
                'avatar' => null,
            ]
        );
        $headQAUser->assignRole($headQA);
    }

}
