<?php

namespace App\Locales;

trait enUS
{
    public $enUs = [
        Language::common['success'] => 'Success.',
        Language::common['found'] => 'Data found.',
        Language::common['notFound'] => 'Data not found.',

        Language::setupWizard['store'] => 'Initial setup completed. Now you can use {appName}.',

        Language::user['unauthenticated'] => "You don't have access to this resource.",
        Language::user['signin'] => 'Successfully sign in.',
        Language::user['signout'] => 'Successfully sign out.',
        Language::user['store'] => 'Successfully add user.',
        Language::user['update'] => 'Successfully update user.',
        Language::user['activate'] => 'Successfully activate user.',
        Language::user['deactivate'] => 'Successfully deactivate user.',

        Language::invitation['store'] => 'Successfully add invitation.',
        Language::invitation['update'] => 'Successfully update invitation.',
        Language::invitation['delete'] => 'Successfully delete invitation.',
        Language::invitation['restore'] => 'Successfully restore invitation.',
        Language::invitation['clear'] => 'Successfully delete all invitation.',
        Language::invitation['restoreAll'] => 'Successfully restore all invitation.',
        Language::invitation['import'] => 'Successfully import invitation.',
        Language::invitation['sent'] => 'Successfully sent invitation.',

        Language::wish['store'] => 'Successfully add wish.',
        Language::wish['update'] => 'Successfully update wish.',
        Language::wish['delete'] => 'Successfully delete wish.',
        Language::wish['restore'] => 'Successfully restore wish.',
        Language::wish['clear'] => 'Successfully delete all wish.',
        Language::wish['restoreAll'] => 'Successfully restore all wish.',

        Language::galery['store'] => 'Successfully add galery.',
        Language::galery['update'] => 'Successfully update galery.',
        Language::galery['delete'] => 'Successfully delete galery.',
        Language::galery['restore'] => 'Successfully restore galery.',
        Language::galery['clear'] => 'Successfully delete all galery.',
        Language::galery['restoreAll'] => 'Successfully restore all galery.',

        Language::setting['update'] => 'Successfully update setting.',
    ];
}
