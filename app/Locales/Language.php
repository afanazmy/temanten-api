<?php

namespace App\Locales;

class Language
{
    use enUS, idID;

    private $language;

    public function __construct($user = null)
    {
        $this->language = $user->language ?? 'en-US';
    }

    private function languages()
    {
        $languages = [
            'en-US' => $this->enUs,
            'id-ID' => $this->idID,
        ];

        return $languages[$this->language];
    }

    /**
     * Get language.
     *
     * @param string  $key
     * @return string
     */
    public function get($key)
    {
        return $this->languages()[$key] ?? null;
    }

    const common = [
        'success' => 'common.success',
        'found' => 'common.found',
        'notFound' => 'common.notFound',
    ];

    const setupWizard = [
        'store' => 'setupWizard.store',
    ];

    const user = [
        'unauthenticated' => 'user.unauthenticated',
        'signin' => 'user.signin',
        'signout' => 'user.signout',
        'store' => 'user.store',
        'update' => 'user.update',
    ];
}
