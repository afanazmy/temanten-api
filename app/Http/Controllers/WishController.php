<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Auth;

use App\Models\Wish;
use App\Traits\Filter;
use App\Locales\Language;
use App\Http\Requests\WishRequest;
use App\Http\Responses\DefaultResponse;

class WishController extends Controller
{
    use Filter;

    public $language;

    /**
     * Column to display in index and show.
     *
     * @var array
     */
    public $columns = [
        'id', 'invitation_id', 'group_member_name', 'wish', 'rsvp', 'deleted_by', 'deleted_at'
    ];

    public function __construct()
    {
        $this->language = new Language(Auth::user());
    }

    public function index(Request $request)
    {
        $query = Wish::with(
            ['invitation' => function ($query) {
                $query->select(['id', 'recipient_name', 'is_group', 'is_family_member']);
            }]
        )
            ->select($this->columns);

        $result = $this->filter($request, $query);

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::common['success']), $result));
    }

    public function hasWish(Request $request)
    {
        $pagination = $request->pagination ?? 10;

        $result = Wish::with(
            ['invitation' => function ($query) {
                $query->select(['id', 'recipient_name', 'is_group', 'is_family_member']);
            }]
        )
            ->select($this->columns)
            ->whereNotNull('wish')
            ->whereNull('deleted_at')
            ->paginate($pagination);

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::common['success']), $result));
    }

    public function show(Request $request, $id)
    {
        $result = DB::table('wishes')->select($this->columns)->where('id', $id)->first();

        if (!$result) {
            return response()->json(DefaultResponse::parse('success', $this->language->get(Language::common['notFound']), null), 404);
        }

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::common['found']), $result));
    }

    public function store(WishRequest $request)
    {
        $result = [
            'id' => Str::orderedUuid(),
            'invitation_id' => $request->invitation_id,
            'group_member_name' => $request->group_member_name,
            'wish' => $request->wish,
            'rsvp' => $request->rsvp,
            'created_at' => Date::now(),
            'updated_at' => Date::now(),
        ];

        DB::beginTransaction();
        DB::table('wishes')->insert($result);
        DB::commit();

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::wish['store']), $result));
    }

    public function update(WishRequest $request, $id)
    {
        DB::beginTransaction();

        $result = DB::table('wishes')->select($this->columns)->where('id', $id);

        if (!$result->first()) {
            DB::rollBack();
            return response()->json(DefaultResponse::parse('failed', $this->language->get(Language::common['notFound']), null), 404);
        }

        $result->update([
            'invitation_id' => $request->invitation_id,
            'group_member_name' => $request->group_member_name,
            'wish' => $request->wish,
            'rsvp' => $request->rsvp,
            'updated_at' => Date::now(),
        ]);

        DB::commit();

        $result = $result->first();

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::wish['update']), $result));
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();

        $result = DB::table('wishes')->select($this->columns)->whereIn('id', $request->id ?? []);

        if (count($result->get()) == 0) {
            DB::rollBack();
            return response()->json(DefaultResponse::parse('failed', $this->language->get(Language::common['notFound']), null), 404);
        }

        $result->update([
            'deleted_by' => Auth::user()->username,
            'deleted_at' => Date::now(),
        ]);

        DB::commit();

        $result = $result->get();

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::wish['delete']), $result));
    }

    public function restore(Request $request)
    {
        DB::beginTransaction();

        $result = DB::table('wishes')->select($this->columns)->whereIn('id', $request->id ?? []);

        if (count($result->get()) == 0) {
            DB::rollBack();
            return response()->json(DefaultResponse::parse('failed', $this->language->get(Language::common['notFound']), null), 404);
        }

        $result->update([
            'deleted_by' => null,
            'deleted_at' => null,
            'updated_at' => Date::now(),
        ]);

        DB::commit();

        $result = $result->get();

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::wish['restore']), $result));
    }

    public function clear()
    {
        DB::beginTransaction();

        $result = DB::table('wishes')->select($this->columns)->whereNull('deleted_at');

        if (!$result->first()) {
            DB::rollBack();
            return response()->json(DefaultResponse::parse('failed', $this->language->get(Language::common['notFound']), null), 404);
        }

        $result->update([
            'deleted_by' => Auth::user()->username,
            'deleted_at' => Date::now(),
        ]);

        DB::commit();

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::wish['clear'])));
    }

    public function restoreAll()
    {
        DB::beginTransaction();

        $result = DB::table('wishes')->select($this->columns)->whereNotNull('deleted_at');

        if (!$result->first()) {
            DB::rollBack();
            return response()->json(DefaultResponse::parse('failed', $this->language->get(Language::common['notFound']), null), 404);
        }

        $result->update([
            'deleted_by' => null,
            'deleted_at' => null,
            'updated_at' => Date::now(),
        ]);

        DB::commit();

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::wish['restoreAll'])));
    }
}
