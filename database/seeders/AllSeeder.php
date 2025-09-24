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
        Permission::create(['name' => 'approve requisition']);


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

        Permission::create(['name' => 'view dashboard']);

        $now = Carbon::now();
        $departments = [
            [
                'id' => 1,
                'name' => 'Engineering & Maintenance',
                'code' => '-',
                'slug' => 'engineering-maintainance',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Finance Admin',
                'code' => '-',
                'slug' => 'finance-admin',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'HCD',
                'code' => '-',
                'slug' => 'hcd',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'Manufacturing',
                'code' => '-',
                'slug' => 'manufacturing',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'name' => 'QM & HSE',
                'code' => '5302',
                'slug' => 'qm-hse',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'name' => 'R&D',
                'code' => '5302',
                'slug' => 'rd',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'name' => 'Sales & Marketing',
                'code' => '5300',
                'slug' => 'sales-marketing',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 8,
                'name' => 'Supply Chain',
                'code' => '-',
                'slug' => 'supply-chain',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 9,
                'name' => 'Supply & Maintenance',
                'code' => '-',
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

        $superAdminUser = User::updateOrCreate(
            ['email' => 'zidanazzahra916@gmail.com'],
            [
            'name' => 'Super Admin',
            'nik' => 'AG1111',
            'username' => 'superadmin',
            'email' => 'zidanazzahra916@gmail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 1,
            'status' => 'active',
            'atasan_nik' => null,
            'avatar' => null,
            ]
        );

        $superAdminUser->assignRole($superAdminRole);

        $userRequsition = User::updateOrCreate(
            ['email' => 'fendlstr03@gmail.com'],
            [
            'name' => 'User Requisition',
            'nik' => 'AG2222',
            'username' => 'user-requisition',
            'email' => 'fendlstr03@gmail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 2,
            'status' => 'active',
            'atasan_nik' => 'AG1111',
            'avatar' => null,
            ]
        );

        $userRequsition->assignRole($userRequisitionRole);

        $userApproval = User::updateOrCreate(
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

        $departmentHeads = [
            ['name' => 'Head Engineering', 'nik' => 'HD0001', 'username' => 'head.eng', 'email' => 'head.eng@example.com', 'department_id' => 1],
            ['name' => 'Head Finance', 'nik' => 'HD0002', 'username' => 'head.fin', 'email' => 'head.fin@example.com', 'department_id' => 2],
            ['name' => 'Head HCD', 'nik' => 'HD0003', 'username' => 'head.hcd', 'email' => 'head.hcd@example.com', 'department_id' => 3],
            ['name' => 'Head Manufacturing', 'nik' => 'HD0004', 'username' => 'head.mfg', 'email' => 'head.mfg@example.com', 'department_id' => 4],
            ['name' => 'Head QM & HSE', 'nik' => 'HD0005', 'username' => 'head.qm', 'email' => 'head.qm@example.com', 'department_id' => 5],
            ['name' => 'Head R&D', 'nik' => 'HD0006', 'username' => 'head.rd', 'email' => 'head.rd@example.com', 'department_id' => 6],
            ['name' => 'Head Sales & Marketing', 'nik' => 'HD0007', 'username' => 'head.sales', 'email' => 'head.sales@example.com', 'department_id' => 7],
            ['name' => 'Head Supply Chain', 'nik' => 'HD0008', 'username' => 'head.sc', 'email' => 'head.sc@example.com', 'department_id' => 8],
            ['name' => 'Head Supply & Maintenance', 'nik' => 'HD0009', 'username' => 'head.sm', 'email' => 'head.sm@example.com', 'department_id' => 9],
        ];

        foreach ($departmentHeads as $head) {
            $user = User::updateOrCreate(
                ['email' => $head['email']],
                [
                    'name' => $head['name'],
                    'nik' => $head['nik'],
                    'username' => $head['username'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'department_id' => $head['department_id'],
                    'status' => 'active',
                    'atasan_nik' => 'AG1111',
                ]
            );
            $user->assignRole($approvalRole);
        }

        $anotherUserRequisition = User::updateOrCreate(
            ['email' => 'staff.eng@example.com'],
            [
                'name' => 'Staff Engineering',
                'nik' => 'ST0001',
                'username' => 'staff.eng',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'department_id' => 7,
                'status' => 'active',
                'atasan_nik' => 'HD0001',
            ]
        );
        $anotherUserRequisition->assignRole($userRequisitionRole);
    }
}
