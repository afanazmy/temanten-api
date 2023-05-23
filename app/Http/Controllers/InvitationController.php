<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Traits\Filter;
use App\Locales\Language;
use App\Imports\InvitationImport;
use App\Exports\InvitationExport;
use App\Http\Requests\ExportQrRequest;
use App\Http\Responses\DefaultResponse;
use App\Http\Requests\GenerateQrRequest;
use App\Http\Requests\StoreInvitationRequest;
use App\Http\Requests\ImportInvitationRequest;
use App\Http\Requests\UpdateInvitationRequest;
use App\Models\Invitation;
use ZipArchive;

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

    public function invitationPhoneNumbers($id)
    {
        $invitationPhoneNumbers = DB::table('invitation_phone_numbers')
            ->select(['phone_number'])
            ->where('invitation_id', $id)
            ->pluck('phone_number');

        return $invitationPhoneNumbers;
    }

    public function index(Request $request)
    {
        $query = Invitation::with('phoneNumbers')->select($this->columns);
        $result = $this->filter($request, $query);

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::common['success']), $result));
    }

    public function show(Request $request, $id)
    {
        $result = DB::table('invitations')->select($this->columns)->where('id', $id)->first();

        if (!$result) {
            return response()->json(DefaultResponse::parse('failed', $this->language->get(Language::common['notFound']), null), 404);
        }

        $result->phone_numbers = $this->invitationPhoneNumbers($id);

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

        $inviationPhoneNumbers = [];
        $phoneNumbers = $request->phone_numbers ?? [];

        foreach ($phoneNumbers as $key => $value) {
            $inviationPhoneNumbers[$key]['invitation_id'] = $result['id'];
            $inviationPhoneNumbers[$key]['phone_number'] = $value;
        }

        DB::table('invitation_phone_numbers')->insert($inviationPhoneNumbers);

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

        DB::table('invitation_phone_numbers')->where('invitation_id', $id)->delete();

        $inviationPhoneNumbers = [];
        $phoneNumbers = $request->phone_numbers ?? [];

        foreach ($phoneNumbers as $key => $value) {
            $inviationPhoneNumbers[$key]['invitation_id'] = $id;
            $inviationPhoneNumbers[$key]['phone_number'] = $value;
        }

        DB::table('invitation_phone_numbers')->insert($inviationPhoneNumbers);

        DB::commit();

        $result = $result->first();

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::invitation['update']), $result));
    }

    public function sent(Request $request)
    {
        DB::beginTransaction();

        $result = DB::table('invitation_phone_numbers')->whereIn('id', $request->id ?? []);

        if (count($result->get()) == 0) {
            DB::rollBack();
            return response()->json(DefaultResponse::parse('failed', $this->language->get(Language::common['notFound']), null), 404);
        }

        $result->update([
            'is_sent' => 1,
            'sent_by' => Auth::user()->username,
            'sent_at' => Date::now(),
        ]);

        DB::commit();

        $result = $result->get();

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::invitation['sent']), $result));
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

        if (count($result->get()) == 0) {
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

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::invitation['restoreAll'])));
    }

    public function downloadTemplate()
    {
        return Excel::download(new InvitationExport, 'invitation.xlsx', null, ['Access-Control-Allow-Origin' => '*']);
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

    public function exportQr(ExportQrRequest $request)
    {
        $zipName = 'qr-codes.zip';
        $zipPath = 'zip/';
        $tmpPath = 'zip/qr-codes/';
        Storage::makeDirectory($tmpPath);

        $invitations = DB::table('invitations')->select(['id', 'recipient_name'])->whereIn('id', $request->id)->get();

        if (count($invitations) == 0) {
            return response()->json(DefaultResponse::parse('failed', $this->language->get(Language::common['notFound']), null), 404);
        }

        foreach ($invitations as $invitation) {
            QrCode::size(400)->generate($invitation->id, storage_path() . '/app/' . $tmpPath . $invitation->recipient_name . '.svg');
        }

        $zip = new ZipArchive();
        $tempFile = tmpfile();
        $tempFileUri = stream_get_meta_data($tempFile)['uri'];

        if ($zip->open($tempFileUri, ZipArchive::CREATE) !== TRUE) {
            echo 'Could not open ZIP file.';
            return;
        }

        foreach ($invitations as $invitation) {
            $file = storage_path() . '/app/' . $tmpPath . $invitation->recipient_name . '.svg';
            if (!$zip->addFile($file, basename($file))) {
                echo 'Could not add file to ZIP: ' . $file;
            }
        }

        $zip->close();

        rename($tempFileUri, storage_path() . '/app/' . $zipPath . $zipName);
        Storage::deleteDirectory($tmpPath);

        return response()
            ->download(
                storage_path() . '/app/' . $zipPath . $zipName,
                $zipName,
                ['Access-Control-Allow-Origin' => '*']
            )->deleteFileAfterSend(true);
    }
}
