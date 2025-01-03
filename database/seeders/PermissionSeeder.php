<?php

namespace Database\Seeders;

use App\Models\Permission;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            [
                'id'    => 1,
                'guard_name' => 'web',
                'name' => 'user_management_access',
            ],
            [
                'id'    => 2,
                'guard_name' => 'web',
                'name' => 'permission_create',
            ],
            [
                'id'    => 3,
                'guard_name' => 'web',
                'name' => 'permission_edit',
            ],
            [
                'id'    => 4,
                'guard_name' => 'web',
                'name' => 'permission_show',
            ],
            [
                'id'    => 5,
                'guard_name' => 'web',
                'name' => 'permission_delete',
            ],
            [
                'id'    => 6,
                'guard_name' => 'web',
                'name' => 'permission_access',
            ],
            [
                'id'    => 7,
                'guard_name' => 'web',
                'name' => 'role_create',
            ],
            [
                'id'    => 8,
                'guard_name' => 'web',
                'name' => 'role_edit',
            ],
            [
                'id'    => 9,
                'guard_name' => 'web',
                'name' => 'role_show',
            ],
            [
                'id'    => 10,
                'guard_name' => 'web',
                'name' => 'role_delete',
            ],
            [
                'id'    => 11,
                'guard_name' => 'web',
                'name' => 'role_access',
            ],
            [
                'id'    => 12,
                'guard_name' => 'web',
                'name' => 'user_create',
            ],
            [
                'id'    => 13,
                'guard_name' => 'web',
                'name' => 'user_edit',
            ],
            [
                'id'    => 14,
                'guard_name' => 'web',
                'name' => 'user_show',
            ],
            [
                'id'    => 15,
                'guard_name' => 'web',
                'name' => 'user_delete',
            ],
            [
                'id'    => 16,
                'guard_name' => 'web',
                'name' => 'user_access',
            ],
            [
                'id'    => 17,
                'guard_name' => 'web',
                'name' => 'driver_create',
            ],
            [
                'id'    => 18,
                'guard_name' => 'web',
                'name' => 'driver_edit',
            ],
            [
                'id'    => 19,
                'guard_name' => 'web',
                'name' => 'driver_show',
            ],
            [
                'id'    => 20,
                'guard_name' => 'web',
                'name' => 'driver_delete',
            ],
            [
                'id'    => 21,
                'guard_name' => 'web',
                'name' => 'driver_access',
            ],
            [
                'id'    => 22,
                'guard_name' => 'web',
                'name' => 'car_create',
            ],
            [
                'id'    => 23,
                'guard_name' => 'web',
                'name' => 'car_edit',
            ],
            [
                'id'    => 24,
                'guard_name' => 'web',
                'name' => 'car_show',
            ],
            [
                'id'    => 25,
                'guard_name' => 'web',
                'name' => 'car_delete',
            ],
            [
                'id'    => 26,
                'guard_name' => 'web',
                'name' => 'car_access',
            ],
            [
                'id'    => 27,
                'guard_name' => 'web',
                'name' => 'attendance_create',
            ],
            [
                'id'    => 28,
                'guard_name' => 'web',
                'name' => 'attendance_edit',
            ],
            [
                'id'    => 29,
                'guard_name' => 'web',
                'name' => 'attendance_show',
            ],
            [
                'id'    => 30,
                'guard_name' => 'web',
                'name' => 'attendance_delete',
            ],
            [
                'id'    => 31,
                'guard_name' => 'web',
                'name' => 'attendance_access',
            ],
            [
                'id'    => 32,
                'guard_name' => 'web',
                'name' => 'barcode_create',
            ],
            [
                'id'    => 33,
                'guard_name' => 'web',
                'name' => 'barcode_edit',
            ],
            [
                'id'    => 34,
                'guard_name' => 'web',
                'name' => 'barcode_show',
            ],
            [
                'id'    => 35,
                'guard_name' => 'web',
                'name' => 'barcode_delete',
            ],
            [
                'id'    => 36,
                'guard_name' => 'web',
                'name' => 'barcode_access',
            ],
            [
                'id'    => 37,
                'guard_name' => 'web',
                'name' => 'car_driver_create',
            ],
            [
                'id'    => 38,
                'guard_name' => 'web',
                'name' => 'car_driver_edit',
            ],
            [
                'id'    => 39,
                'guard_name' => 'web',
                'name' => 'car_driver_show',
            ],
            [
                'id'    => 40,
                'guard_name' => 'web',
                'name' => 'car_driver_delete',
            ],
            [
                'id'    => 41,
                'guard_name' => 'web',
                'name' => 'car_driver_access',
            ],
            [
                'id'    => 42,
                'guard_name' => 'web',
                'name' => 'car_link_history_create',
            ],
            [
                'id'    => 43,
                'guard_name' => 'web',
                'name' => 'car_link_history_edit',
            ],
            [
                'id'    => 44,
                'guard_name' => 'web',
                'name' => 'car_link_history_show',
            ],
            [
                'id'    => 45,
                'guard_name' => 'web',
                'name' => 'car_link_history_delete',
            ],
            [
                'id'    => 46,
                'guard_name' => 'web',
                'name' => 'car_link_history_access',
            ],
            [
                'id'    => 47,
                'guard_name' => 'web',
                'name' => 'client_create',
            ],
            [
                'id'    => 48,
                'guard_name' => 'web',
                'name' => 'client_edit',
            ],
            [
                'id'    => 49,
                'guard_name' => 'web',
                'name' => 'client_show',
            ],
            [
                'id'    => 50,
                'guard_name' => 'web',
                'name' => 'client_delete',
            ],
            [
                'id'    => 51,
                'guard_name' => 'web',
                'name' => 'client_access',
            ],
            [
                'id'    => 52,
                'guard_name' => 'web',
                'name' => 'location_create',
            ],
            [
                'id'    => 53,
                'guard_name' => 'web',
                'name' => 'location_edit',
            ],
            [
                'id'    => 54,
                'guard_name' => 'web',
                'name' => 'location_show',
            ],
            [
                'id'    => 55,
                'guard_name' => 'web',
                'name' => 'location_delete',
            ],
            [
                'id'    => 56,
                'guard_name' => 'web',
                'name' => 'location_access',
            ],
            [
                'id'    => 57,
                'guard_name' => 'web',
                'name' => 'container_create',
            ],
            [
                'id'    => 58,
                'guard_name' => 'web',
                'name' => 'container_edit',
            ],
            [
                'id'    => 59,
                'guard_name' => 'web',
                'name' => 'container_show',
            ],
            [
                'id'    => 60,
                'guard_name' => 'web',
                'name' => 'container_delete',
            ],
            [
                'id'    => 61,
                'guard_name' => 'web',
                'name' => 'container_access',
            ],
            [
                'id'    => 62,
                'guard_name' => 'web',
                'name' => 'client_location_create',
            ],
            [
                'id'    => 63,
                'guard_name' => 'web',
                'name' => 'client_location_edit',
            ],
            [
                'id'    => 64,
                'guard_name' => 'web',
                'name' => 'client_location_show',
            ],
            [
                'id'    => 65,
                'guard_name' => 'web',
                'name' => 'client_location_delete',
            ],
            [
                'id'    => 66,
                'guard_name' => 'web',
                'name' => 'client_location_access',
            ],
            [
                'id'    => 67,
                'guard_name' => 'web',
                'name' => 'client_account_create',
            ],
            [
                'id'    => 68,
                'guard_name' => 'web',
                'name' => 'client_account_edit',
            ],
            [
                'id'    => 69,
                'guard_name' => 'web',
                'name' => 'client_account_show',
            ],
            [
                'id'    => 70,
                'guard_name' => 'web',
                'name' => 'client_account_delete',
            ],
            [
                'id'    => 71,
                'guard_name' => 'web',
                'name' => 'client_account_access',
            ],
            [
                'id'    => 72,
                'guard_name' => 'web',
                'name' => 'contact_create',
            ],
            [
                'id'    => 73,
                'guard_name' => 'web',
                'name' => 'contact_edit',
            ],
            [
                'id'    => 74,
                'guard_name' => 'web',
                'name' => 'contact_show',
            ],
            [
                'id'    => 75,
                'guard_name' => 'web',
                'name' => 'contact_delete',
            ],
            [
                'id'    => 76,
                'guard_name' => 'web',
                'name' => 'contact_access',
            ],
            [
                'id'    => 77,
                'guard_name' => 'web',
                'name' => 'task_create',
            ],
            [
                'id'    => 78,
                'guard_name' => 'web',
                'name' => 'task_edit',
            ],
            [
                'id'    => 79,
                'guard_name' => 'web',
                'name' => 'task_show',
            ],
            [
                'id'    => 80,
                'guard_name' => 'web',
                'name' => 'task_delete',
            ],
            [
                'id'    => 81,
                'guard_name' => 'web',
                'name' => 'task_access',
            ],
            [
                'id'    => 82,
                'guard_name' => 'web',
                'name' => 'sample_create',
            ],
            [
                'id'    => 83,
                'guard_name' => 'web',
                'name' => 'sample_edit',
            ],
            [
                'id'    => 84,
                'guard_name' => 'web',
                'name' => 'sample_show',
            ],
            [
                'id'    => 85,
                'guard_name' => 'web',
                'name' => 'sample_delete',
            ],
            [
                'id'    => 86,
                'guard_name' => 'web',
                'name' => 'sample_access',
            ],
            [
                'id'    => 87,
                'guard_name' => 'web',
                'name' => 'term_create',
            ],
            [
                'id'    => 88,
                'guard_name' => 'web',
                'name' => 'term_edit',
            ],
            [
                'id'    => 89,
                'guard_name' => 'web',
                'name' => 'term_show',
            ],
            [
                'id'    => 90,
                'guard_name' => 'web',
                'name' => 'term_delete',
            ],
            [
                'id'    => 91,
                'guard_name' => 'web',
                'name' => 'term_access',
            ],
            [
                'id'    => 92,
                'guard_name' => 'web',
                'name' => 'elm_notification_show',
            ],
            [
                'id'    => 93,
                'guard_name' => 'web',
                'name' => 'elm_notification_access',
            ],
            [
                'id'    => 94,
                'guard_name' => 'web',
                'name' => 'driver_schedule_create',
            ],
            [
                'id'    => 95,
                'guard_name' => 'web',
                'name' => 'driver_schedule_edit',
            ],
            [
                'id'    => 96,
                'guard_name' => 'web',
                'name' => 'driver_schedule_show',
            ],
            [
                'id'    => 97,
                'guard_name' => 'web',
                'name' => 'driver_schedule_delete',
            ],
            [
                'id'    => 98,
                'guard_name' => 'web',
                'name' => 'driver_schedule_access',
            ],
            [
                'id'    => 99,
                'guard_name' => 'web',
                'name' => 'profile_password_edit',
            ],

            [
                'id'    => 99,
                'guard_name' => 'web',
                'name' => 'swaprequest_create',
            ],
            [
                'id'    => 100,
                'guard_name' => 'web',
                'name' => 'swaprequest_edit',
            ],
            [
                'id'    => 101,
                'guard_name' => 'web',
                'name' => 'swaprequest_show',
            ],
            [
                'id'    => 102,
                'guard_name' => 'web',
                'name' => 'swaprequest_delete',
            ],
            [
                'id'    => 103,
                'guard_name' => 'web',
                'name' => 'swaprequest_access',
            ],
            [
                'id'    => 104,
                'guard_name' => 'web',
                'name' => 'profile_password_edit',
            ],
            [
                'id'    => 105,
                'guard_name' => 'web',
                'name' => 'audit_log_access',
            ],
            [
                'id'    => 1001,
                'guard_name' => 'web',
                'name' => 'audit_log_show',
            ],


            [
                'id'    => 106,
                'title' => 'zone_create',
            ],
            [
                'id'    => 107,
                'title' => 'zone_edit',
            ],
            [
                'id'    => 108,
                'title' => 'zone_show',
            ],
            [
                'id'    => 109,
                'title' => 'zone_delete',
            ],
            [
                'id'    => 110,
                'title' => 'zone_access',
            ],
            [
                'id'    => 111,
                'title' => 'client_driver_create',
            ],
            [
                'id'    => 112,
                'title' => 'client_driver_edit',
            ],
            [
                'id'    => 113,
                'title' => 'client_driver_show',
            ],
            [
                'id'    => 114,
                'title' => 'client_driver_delete',
            ],
            [
                'id'    => 115,
                'title' => 'client_driver_access',
            ],
        ];

        Permission::insert($permissions);
    }
}
