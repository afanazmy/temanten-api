<?php

namespace App\Http\Controllers;

use App\Http\Responses\DefaultResponse;
use App\Locales\Language;
use App\Models\Invitation;
use Illuminate\Http\Request;

class AppsController extends Controller
{
    public $language;

    public function __construct()
    {
        $this->language = new Language();
    }

    public function initial(Request $request)
    {
        $invitation = Invitation::find($request->id);

        if (!$invitation) {
            return response()->json(DefaultResponse::parse('failed', $this->language->get(Language::common['notFound']), null), 404);
        }

        $assets = [
            'typography' => 'https://assets.wedding.afanazmi.my.id/typography.svg',
            'cover' => [
                'background_url' => 'https://assets.wedding.afanazmi.my.id/IMG_5141-cropped.jpg'
            ]
        ];

        $result = [
            'guest' => $invitation,
            'assets' => $assets
        ];

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::common['success']), $result));
    }

    public function guest(Request $request)
    {
        $result = Invitation::find($request->id);

        if (!$result) {
            return response()->json(DefaultResponse::parse('failed', $this->language->get(Language::common['notFound']), null), 404);
        }

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::common['success']), $result));
    }
}
