<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissionGroup = DB::table('permission_groups')->first();
        $permission = DB::table('permissions')->first();

        if (env('APP_ENV') !== 'local' && $permissionGroup || $permission) return;

        $permissionGroups = [
            [
                'id' => 1,
                'permission_group_name' => json_encode(['en-US' => 'User', 'id-ID' => 'User']),
                'permissions' => [
                    [
                        'id' => 'Add User',
                        'permission_group_id' => 1,
                        'permission_label' => json_encode(['en-US' => 'Add User', 'id-ID' => 'Tambah User']),
                    ],
                    [
                        'id' => 'Update User',
                        'permission_group_id' => 1,
                        'permission_label' => json_encode(['en-US' => 'Update User', 'id-ID' => 'Ubah User']),
                    ],
                    [
                        'id' => 'Update User Status',
                        'permission_group_id' => 1,
                        'permission_label' => json_encode(['en-US' => 'Update User Status', 'id-ID' => 'Ubah Status User']),
                    ],
                ]
            ],
            [
                'id' => 2,
                'permission_group_name' => json_encode(['en-US' => 'Invitation', 'id-ID' => 'Undangan']),
                'permissions' => [
                    [
                        'id' => 'Add Invitation',
                        'permission_group_id' => 2,
                        'permission_label' => json_encode(['en-US' => 'Add Invitation', 'id-ID' => 'Tambah Undangan']),
                    ],
                    [
                        'id' => 'Update Invitation',
                        'permission_group_id' => 2,
                        'permission_label' => json_encode(['en-US' => 'Update Invitation', 'id-ID' => 'Ubah Undangan']),
                    ],
                    [
                        'id' => 'Delete Invitation',
                        'permission_group_id' => 2,
                        'permission_label' => json_encode(['en-US' => 'Delete Invitation', 'id-ID' => 'Hapus Undangan']),
                    ],
                    [
                        'id' => 'Restore Invitation',
                        'permission_group_id' => 2,
                        'permission_label' => json_encode(['en-US' => 'Restore Invitation', 'id-ID' => 'Pulihkan Undangan']),
                    ],
                    [
                        'id' => 'Delete All Invitation',
                        'permission_group_id' => 2,
                        'permission_label' => json_encode(['en-US' => 'Delete All Invitation', 'id-ID' => 'Hapus Semua Undangan']),
                    ],
                    [
                        'id' => 'Restore All Invitation',
                        'permission_group_id' => 2,
                        'permission_label' => json_encode(['en-US' => 'Restore All Invitation', 'id-ID' => 'Pulihkan Semua Undangan']),
                    ],
                ]
            ],
            [
                'id' => 3,
                'permission_group_name' => json_encode(['en-US' => 'Guest Book', 'id-ID' => 'Buku Tamu']),
                'permissions' => [
                    [
                        'id' => 'Update Guest Book',
                        'permission_group_id' => 3,
                        'permission_label' => json_encode(['en-US' => 'Update Guest Book', 'id-ID' => 'Ubah Buku Tamu']),
                    ],
                    [
                        'id' => 'Delete Guest Book',
                        'permission_group_id' => 3,
                        'permission_label' => json_encode(['en-US' => 'Delete Guest Book', 'id-ID' => 'Hapus Buku Tamu']),
                    ],
                    [
                        'id' => 'Restore Guest Book',
                        'permission_group_id' => 3,
                        'permission_label' => json_encode(['en-US' => 'Restore Guest Book', 'id-ID' => 'Pulihkan Buku Tamu']),
                    ],
                ]
            ],
            [
                'id' => 4,
                'permission_group_name' => json_encode(['en-US' => 'Wish', 'id-ID' => 'Ucapan']),
                'permissions' => [
                    [
                        'id' => 'Update Wish',
                        'permission_group_id' => 4,
                        'permission_label' => json_encode(['en-US' => 'Update Wish', 'id-ID' => 'Ubah Ucapan']),
                    ],
                    [
                        'id' => 'Delete Wish',
                        'permission_group_id' => 4,
                        'permission_label' => json_encode(['en-US' => 'Delete Wish', 'id-ID' => 'Hapus Ucapan']),
                    ],
                    [
                        'id' => 'Restore Wish',
                        'permission_group_id' => 4,
                        'permission_label' => json_encode(['en-US' => 'Restore Wish', 'id-ID' => 'Pulihkan Ucapan']),
                    ],
                ]
            ],
            [
                'id' => 5,
                'permission_group_name' => json_encode(['en-US' => 'Galery', 'id-ID' => 'Galeri']),
                'permissions' => [
                    [
                        'id' => 'Add Galery',
                        'permission_group_id' => 5,
                        'permission_label' => json_encode(['en-US' => 'Add Galery', 'id-ID' => 'Tambah Galeri']),
                    ],
                    [
                        'id' => 'Update Galery',
                        'permission_group_id' => 5,
                        'permission_label' => json_encode(['en-US' => 'Update Galery', 'id-ID' => 'Ubah Galeri']),
                    ],
                    [
                        'id' => 'Delete Galery',
                        'permission_group_id' => 5,
                        'permission_label' => json_encode(['en-US' => 'Delete Galery', 'id-ID' => 'Hapus Galeri']),
                    ],
                    [
                        'id' => 'Restore Galery',
                        'permission_group_id' => 5,
                        'permission_label' => json_encode(['en-US' => 'Restore Galery', 'id-ID' => 'Pulihkan Galeri']),
                    ],
                    [
                        'id' => 'Delete All Galery',
                        'permission_group_id' => 5,
                        'permission_label' => json_encode(['en-US' => 'Delete All Invitation', 'id-ID' => 'Hapus Semua Galeri']),
                    ],
                    [
                        'id' => 'Restore All Galery',
                        'permission_group_id' => 5,
                        'permission_label' => json_encode(['en-US' => 'Restore All Invitation', 'id-ID' => 'Pulihkan Semua Galeri']),
                    ],
                ]
            ],
        ];

        DB::beginTransaction();

        foreach ($permissionGroups as $permissionGroup) {
            $permissions = $permissionGroup['permissions'];
            unset($permissionGroup['permissions']);

            DB::table('permission_groups')->insert($permissionGroup);
            DB::table('permissions')->insert($permissions);
        }

        DB::commit();
    }
}
