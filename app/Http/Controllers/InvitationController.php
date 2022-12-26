<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Traits\Filter;
use App\Locales\Language;
use App\Imports\InvitationImport;
use App\Exports\InvitationExport;
use App\Http\Requests\GenerateQrRequest;
use App\Http\Responses\DefaultResponse;
use App\Http\Requests\StoreInvitationRequest;
use App\Http\Requests\ImportInvitationRequest;
use App\Http\Requests\UpdateInvitationRequest;

class InvitationController extends Controller
{
    use Filter;

    public $language;

    /**
     * Column to display in index and show.
     *
     * @var array
     */
    public $columns = [
        'id', 'recipient_name', 'is_group', 'is_family_member', 'deleted_by', 'deleted_at'
    ];

    public function __construct()
    {
        $this->language = new Language(Auth::user());
    }

    public function index(Request $request)
    {
        $query = DB::table('invitations')->select($this->columns);
        $result = $this->filter($request, $query);

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::common['success']), $result));
    }

    public function show(Request $request, $id)
    {
        $result = DB::table('invitations')->select($this->columns)->where('id', $id)->first();

        if (!$result) {
            return response()->json(DefaultResponse::parse('success', $this->language->get(Language::common['notFound']), null), 404);
        }

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::common['found']), $result));
    }

    public function store(StoreInvitationRequest $request)
    {
        $result = [
            'id' => Str::orderedUuid(),
            'recipient_name' => $request->recipient_name,
            'is_group' => $request->is_group,
            'is_family_member' => $request->is_family_member,
            'created_at' => Date::now(),
            'updated_at' => Date::now(),
        ];

        DB::beginTransaction();
        DB::table('invitations')->insert($result);
        DB::commit();

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::invitation['store']), $result));
    }

    public function update(UpdateInvitationRequest $request, $id)
    {
        DB::beginTransaction();

        $result = DB::table('invitations')->select($this->columns)->where('id', $id);

        if (!$result->first()) {
            DB::rollBack();
            return response()->json(DefaultResponse::parse('failed', $this->language->get(Language::common['notFound']), null), 404);
        }

        $result->update([
            'recipient_name' => $request->recipient_name,
            'is_group' => $request->is_group,
            'is_family_member' => $request->is_family_member,
            'updated_at' => Date::now(),
        ]);

        DB::commit();

        $result = $result->first();

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::invitation['update']), $result));
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();

        $result = DB::table('invitations')->select($this->columns)->whereIn('id', $request->id ?? []);

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

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::invitation['delete']), $result));
    }

    public function restore(Request $request)
    {
        DB::beginTransaction();

        $result = DB::table('invitations')->select($this->columns)->whereIn('id', $request->id ?? []);

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

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::invitation['restore']), $result));
    }

    public function clear()
    {
        DB::beginTransaction();

        $result = DB::table('invitations')->select($this->columns)->whereNull('deleted_at');

        if (!$result->first()) {
            DB::rollBack();
            return response()->json(DefaultResponse::parse('failed', $this->language->get(Language::common['notFound']), null), 404);
        }

        $result->update([
            'deleted_by' => Auth::user()->username,
            'deleted_at' => Date::now(),
        ]);

        DB::commit();

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::invitation['clear'])));
    }

    public function restoreAll()
    {
        DB::beginTransaction();

        $result = DB::table('invitations')->select($this->columns)->whereNotNull('deleted_at');

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

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::invitation['restoreAll'])));
    }

    public function downloadTemplate()
    {
        return Excel::download(new InvitationExport, 'invitation.xlsx');
    }

    public function importTemplate(ImportInvitationRequest $request)
    {
        Excel::import(new InvitationImport($request->type), $request->file);
        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::invitation['import'])));
    }

    public function generateQr(GenerateQrRequest $request)
    {
        $result = QrCode::size(400)->generate($request->invitation_id);
        return $result;
    }
}
