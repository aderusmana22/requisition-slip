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

         //! Create Roles
        $superAdminRole = Role::create(['name' => 'super-admin']); //as super-admin
        $userRequisitionRole = Role::create(['name' => 'user-requisition']); // as user-requisition
        $approvalRole = Role::create(['name' => 'user-approval']); // as head-department

        //* Sales & Marketing
        $headSalesMarketingRole = Role::create(['name' => 'head-SNM']);
        $headSalesMarketingRole->givePermissionTo([
            'view requisition-approval',
            'approve requisition',
            'reject requisition']);

        $staffSalesMarketingRole = Role::create(['name' => 'staff-SNM']);
        $staffSalesMarketingRole->givePermissionTo([
            'view requisition',
            'create requisition',
            'update requisition',
            'delete requisition']);

        //* R&D
        $headRndRole = Role::create(['name' => 'head-R&D']);
        $headRndRole->givePermissionTo([
            'view requisition-approval',
            'approve requisition',
            'reject requisition']);

        $staffRndRole = Role::create(['name' => 'staff-R&D']);
        $staffRndRole->givePermissionTo([
            'view requisition',
            'create requisition',
            'update requisition',
            'delete requisition']);

        //* QA
        $headQaRole = Role::create(['name' => 'head-QA']);
        $headQaRole->givePermissionTo([
            'view requisition-approval',
            'approve requisition',
            'reject requisition']);

        $staffQaRole = Role::create(['name' => 'staff-QA']);
        $staffQaRole->givePermissionTo([
            'view requisition',
            'create requisition',
            'update requisition',
            'delete requisition']);

        //* HCD
        $headHcdRole = Role::create(['name' => 'head-HCD']);
        $headHcdRole->givePermissionTo([
            'view requisition-approval',
            'approve requisition',
            'reject requisition']);

        $staffHcdRole = Role::create(['name' => 'staff-HCD']);
        $staffHcdRole->givePermissionTo([
            'view requisition',
            'create requisition',
            'update requisition',
            'delete requisition']);

        //! Create Users and Assign Roles

        //* Sales & Marketing Users
        $headSales = User::updateOrCreate([
            'email' => 'head.sales@example.com'
        ], [
            'name' => 'Head SNM',
            'nik' => 'HDSM01',
            'username' => 'head.sales',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 7,
            'status' => 'active',
            'atasan_nik' => 'AG1111',
        ]);
        $headSales->assignRole($headSalesMarketingRole);

        $staffSales1 = User::updateOrCreate([
            'email' => 'staff.sales1@example.com'
        ], [
            'name' => 'Staff SNM 1',
            'nik' => 'STSM01',
            'username' => 'staff.sales1',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 7,
            'status' => 'active',
            'atasan_nik' => 'HDSM01',
        ]);
        $staffSales1->assignRole($staffSalesMarketingRole);

        $staffSales2 = User::updateOrCreate([
            'email' => 'staff.sales2@example.com'
        ], [
            'name' => 'Staff SNM 2',
            'nik' => 'STSM02',
            'username' => 'staff.sales2',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 7,
            'status' => 'active',
            'atasan_nik' => 'HDSM01',
        ]);
        $staffSales2->assignRole($staffSalesMarketingRole);

        //* R&D Users
        $headRnd = User::updateOrCreate([
            'email' => 'head.rnd@example.com'
        ], [
            'name' => 'Head R&D',
            'nik' => 'HDRD01',
            'username' => 'head.rnd',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 6,
            'status' => 'active',
            'atasan_nik' => 'AG1111',
        ]);
        $headRnd->assignRole($headRndRole);

        $staffRnd1 = User::updateOrCreate([
            'email' => 'staff.rnd1@example.com'
        ], [
            'name' => 'Staff R&D 1',
            'nik' => 'STRD01',
            'username' => 'staff.rnd1',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 6,
            'status' => 'active',
            'atasan_nik' => 'HDRD01',
        ]);
        $staffRnd1->assignRole($staffRndRole);

        $staffRnd2 = User::updateOrCreate([
            'email' => 'staff.rnd2@example.com'
        ], [
            'name' => 'Staff R&D 2',
            'nik' => 'STRD02',
            'username' => 'staff.rnd2',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 6,
            'status' => 'active',
            'atasan_nik' => 'HDRD01',
        ]);
        $staffRnd2->assignRole($staffRndRole);

        //* QA Users
        $headQa = User::updateOrCreate([
            'email' => 'head.qa@example.com'
        ], [
            'name' => 'Head QA',
            'nik' => 'HDQA01',
            'username' => 'head.qa',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 5,
            'status' => 'active',
            'atasan_nik' => 'AG1111',
        ]);
        $headQa->assignRole($headQaRole);

        $staffQa1 = User::updateOrCreate([
            'email' => 'staff.qa1@example.com'
        ], [
            'name' => 'Staff QA 1',
            'nik' => 'STQA01',
            'username' => 'staff.qa1',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 5,
            'status' => 'active',
            'atasan_nik' => 'HDQA01',
        ]);
        $staffQa1->assignRole($staffQaRole);

        $staffQa2 = User::updateOrCreate([
            'email' => 'staff.qa2@example.com'
        ], [
            'name' => 'Staff QA 2',
            'nik' => 'STQA02',
            'username' => 'staff.qa2',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 5,
            'status' => 'active',
            'atasan_nik' => 'HDQA01',
        ]);
        $staffQa2->assignRole($staffQaRole);

        //* HCD Users
        $headHcd = User::updateOrCreate([
            'email' => 'head.hcd@example.com'
        ], [
            'name' => 'Head HCD',
            'nik' => 'HDHCD01',
            'username' => 'head.hcd',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 3,
            'status' => 'active',
            'atasan_nik' => 'AG1111',
        ]);
        $headHcd->assignRole($headHcdRole);

        $staffHcd1 = User::updateOrCreate([
            'email' => 'staff.hcd1@example.com'
        ], [
            'name' => 'Staff HCD 1',
            'nik' => 'STHCD01',
            'username' => 'staff.hcd1',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 3,
            'status' => 'active',
            'atasan_nik' => 'HDHCD01',
        ]);
        $staffHcd1->assignRole($staffHcdRole);

        $staffHcd2 = User::updateOrCreate([
            'email' => 'staff.hcd2@example.com'
        ], [
            'name' => 'Staff HCD 2',
            'nik' => 'STHCD02',
            'username' => 'staff.hcd2',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 3,
            'status' => 'active',
            'atasan_nik' => 'HDHCD01',
        ]);
        $staffHcd2->assignRole($staffHcdRole);

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
            ['email' => 'superadmin@example.com'],
            [
            'name' => 'Super Admin',
            'nik' => 'AG1111',
            'username' => 'superadmin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 1,
            'status' => 'active',
            'atasan_nik' => 'AG2222',
            'avatar' => null,
            ]
        );

        $superAdminUser->assignRole($superAdminRole);

        $userRequsition = User::updateOrCreate(
            ['email' => 'user-requisition@example.com'],
            [
            'name' => 'User Requisition',
            'nik' => 'AG2222',
            'username' => 'user-requisition',
            'email' => 'user-requisition@example.com',
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
            ['email' => 'no-reply@example.com'],
            [
            'name' => 'User Approval',
            'nik' => 'AG3333',
            'username' => 'user-approval',
            'email' => 'no-reply@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'department_id' => 3,
            'status' => 'active',
            'atasan_nik' => 'AG1111',
            'avatar' => null,
            ]
        );
        $userApproval->assignRole($approvalRole);

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
        $anotherUserRequisition->assignRole('user-requisition');

        // Users for Warehouse roles
        $warehouseUsers = [
            [
                'name' => 'Inward WH Supervisor',
                'nik' => 'WH0001',
                'username' => 'inward.wh',
                'email' => 'inward.wh@example.com',
                'department_id' => 8, // Asumsi Dept. Supply Chain
            ],
            [
                'name' => 'Material Support Supervisor',
                'nik' => 'MS0001',
                'username' => 'material.support',
                'email' => 'material.support@example.com',
                'department_id' => 8, // Asumsi Dept. Supply Chain
            ],
            [
                'name' => 'Outward WH Supervisor',
                'nik' => 'WH0002',
                'username' => 'outward.wh',
                'email' => 'outward.wh@example.com',
                'department_id' => 8, // Asumsi Dept. Supply Chain
            ],
        ];

        foreach ($warehouseUsers as $whUser) {
            $user = User::updateOrCreate(
                ['email' => $whUser['email']],
                [
                    'name' => $whUser['name'],
                    'nik' => $whUser['nik'],
                    'username' => $whUser['username'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'department_id' => $whUser['department_id'],
                    'status' => 'active',
                    'atasan_nik' => 'AG1111',
                ]
            );

            $user->assignRole('user-requisition');
        }
    }

}
