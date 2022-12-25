<?php

namespace App\Locales;

trait enUS
{
    public $enUs = [
        Language::setupWizard['store'] => 'Initial setup completed. Now you can use Temanten App.',

        Language::user['unauthenticated'] => "You don't have access to this resource.",
        Language::user['signin'] => 'Successfully sign in.',
        Language::user['signout'] => 'Successfully sign out.',
    ];
}
