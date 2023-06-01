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
     * @param array  $values
     * @return string
     */
    public function get($key, $values = [])
    {
        $message = $this->languages()[$key] ?? '';

        foreach ($values as $key => $value) {
            $message = str_replace('{' . $key . '}', $value, $message);
        }

        if ($message == '') $message = null;

        return $message;
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
        'activate' => 'user.activate',
        'deactivate' => 'user.deactivate',
    ];

    const invitation = [
        'store' => 'invitation.store',
        'update' => 'invitation.update',
        'delete' => 'invitation.delete',
        'restore' => 'invitation.restore',
        'clear' => 'invitation.clear',
        'restoreAll' => 'invitation.restoreAll',
        'import' => 'invitation.import',
        'sent' => 'invitation.sent',
    ];

    const wish = [
        'store' => 'wish.store',
        'update' => 'wish.update',
        'delete' => 'wish.delete',
        'restore' => 'wish.restore',
        'clear' => 'wish.clear',
        'restoreAll' => 'wish.restoreAll',
    ];

    const galery = [
        'store' => 'galery.store',
        'update' => 'galery.update',
        'delete' => 'galery.delete',
        'restore' => 'galery.restore',
        'clear' => 'galery.clear',
        'restoreAll' => 'galery.restoreAll',
    ];

    const guestBook = [
        'store' => 'guestBook.store',
        'update' => 'guestBook.update',
        'delete' => 'guestBook.delete',
        'restore' => 'guestBook.restore',
        'clear' => 'guestBook.clear',
        'restoreAll' => 'guestBook.restoreAll',
        'invitationNotFound' => 'guestBook.invitationNotFound',
        'invitationAlreadyUsed' => 'guestBook.invitationAlreadyUsed',
    ];

    const setting = [
        'update' => 'setting.update',
    ];
}
