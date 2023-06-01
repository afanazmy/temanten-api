<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGuestBookRequest;
use App\Http\Responses\DefaultResponse;
use App\Locales\Language;
use App\Models\GuestBook;
use App\Traits\Filter;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class GuestBookController extends Controller
{
    use Filter;

    public $language;

    /**
     * Column to display in index and show.
     *
     * @var array
     */
    public $columns = [
        'id', 'invitation_id', 'number_of_guest', 'is_group', 'deleted_by', 'deleted_at'
    ];

    public function __construct()
    {
        $this->language = new Language(Auth::user());
    }

    public function index(Request $request)
    {
        $query = GuestBook::with('invitation')->select($this->columns);
        $result = $this->filter($request, $query);

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::common['success']), $result));
    }

    public function show(Request $request, $id)
    {
        $result = GuestBook::with('invitation')->select($this->columns)->where('id', $id)->first();

        if (!$result) {
            return response()->json(DefaultResponse::parse('failed', $this->language->get(Language::common['notFound']), null), 404);
        }

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::common['found']), $result));
    }

    public function store(StoreGuestBookRequest $request)
    {
        $invitation = DB::table('invitations')->where('invitation_id', $request->invitation_id)->first();
        if (!$invitation) {
            return response()->json(DefaultResponse::parse('failed', $this->language->get(Language::guestBook['invitationNotFound']), null), 404);
        }

        $guestBook = GuestBook::with('invitation')->select($this->columns)->where('invitation_id', $request->invitation_id)->where('deleted_at', null);
        $_guestBook = $guestBook->first();
        if ($_guestBook && $invitation->is_group == 0) {
            return response()->json(DefaultResponse::parse('failed', $this->language->get(Language::guestBook['invitationAlreadyUsed']), null), 422);
        }

        DB::beginTransaction();

        if ($_guestBook) {
            $guestBook->update(
                [
                    'number_of_guest' => $_guestBook->number_of_guest + 1,
                    'updated_at' => Date::now(),
                ]
            );

            DB::commit();

            return response()->json(DefaultResponse::parse('success', $this->language->get(Language::guestBook['store']), $guestBook->first()));
        }

        $result = [
            'id' => Str::orderedUuid(),
            'invitation_id' => $request->invitation_id,
            'is_group' => $invitation->is_group,
            'number_of_guest' => 1,
            'created_at' => Date::now(),
            'updated_at' => Date::now(),
        ];


        DB::table('guest_books')->insert($result);

        DB::commit();

        $result['invitation'] = $invitation;

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::guestBook['store']), $result));
    }
}
