<?php

namespace App\Locales;

trait enUS
{
    public $enUs = [
        Language::common['success'] => 'Success.',
        Language::common['found'] => 'Data found.',
        Language::common['notFound'] => 'Data not found.',

        Language::setupWizard['store'] => 'Initial setup completed. Now you can use Temanten App.',

        Language::user['unauthenticated'] => "You don't have access to this resource.",
        Language::user['signin'] => 'Successfully sign in.',
        Language::user['signout'] => 'Successfully sign out.',
        Language::user['store'] => 'Successfully add user.',
        Language::user['update'] => 'Successfully update user.',
        Language::user['activate'] => 'Successfully activate user.',
        Language::user['deactivate'] => 'Successfully deactivate user.',
    ];
}
