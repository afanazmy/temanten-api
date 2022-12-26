<?php

namespace App\Locales;

trait idID
{
    public $idID = [
        Language::common['success'] => 'Berhasil.',
        Language::common['found'] => 'Data ditemukan.',
        Language::common['notFound'] => 'Data tidak ditemukan.',

        Language::setupWizard['store'] => 'Pengaturan awal sudah selesai. Sekarang Anda dapat menggunakan {appName}.',

        Language::user['unauthenticated'] => "Anda tidak memiliki akses ke resource ini.",
        Language::user['signin'] => 'Berhasil masuk.',
        Language::user['signout'] => 'Berhasil keluar.',
        Language::user['store'] => 'Berhasil menambah user.',
        Language::user['update'] => 'Berhasil mengubah user.',
        Language::user['activate'] => 'Berhasil mengaktifkan user.',
        Language::user['deactivate'] => 'Berhasil menonaktifkan user.',

        Language::invitation['store'] => 'Berhasil menambah undangan.',
        Language::invitation['update'] => 'Berhasil mengubah undangan.',
        Language::invitation['delete'] => 'Berhasil menghapus undangan.',
        Language::invitation['restore'] => 'Berhasil memulihkan undangan.',
        Language::invitation['clear'] => 'Berhasil menghapus semua undangan.',
        Language::invitation['restoreAll'] => 'Berhasil memulihkan semua undangan.',
        Language::invitation['import'] => 'Berhasil mengimpor undangan.',
    ];
}
