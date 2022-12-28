<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetImageRequest;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

use App\Traits\Filter;
use App\Locales\Language;
use App\Http\Responses\DefaultResponse;
use App\Http\Requests\StoreGaleryRequest;
use App\Http\Requests\UpdateGaleryRequest;

class GaleryController extends Controller
{
    use Filter;

    public $language;

    /**
     * Column to display in index and show.
     *
     * @var array
     */
    public $columns = [
        'id', 'name', 'type', 'path', 'deleted_by', 'deleted_at'
    ];

    public function __construct()
    {
        $this->language = new Language(Auth::user());
    }

    public function index(Request $request)
    {
        $query = DB::table('galeries')->select($this->columns);
        $result = $this->filter($request, $query);

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::common['success']), $result));
    }

    public function show(Request $request, $id)
    {
        $result = DB::table('galeries')->select($this->columns)->where('id', $id)->first();

        if (!$result) {
            return response()->json(DefaultResponse::parse('failed', $this->language->get(Language::common['notFound']), null), 404);
        }

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::common['found']), $result));
    }

    public function store(StoreGaleryRequest $request)
    {
        $result = [];

        foreach ($request->galeries as $galery) {
            $path = 'app/';
            $storagePath = 'galeries/';
            $name = bin2hex(random_bytes(10)) . '.' . $galery['extension'];
            $fullPath = $path . $storagePath . $name;

            Storage::makeDirectory($storagePath);
            Image::make($galery['file'])->save(storage_path($fullPath));

            array_push($result, [
                'id' => Str::orderedUuid(),
                'name' => $galery['name'],
                'type' => $galery['type'],
                'path' => $fullPath,
                'created_at' => Date::now(),
                'updated_at' => Date::now(),
            ]);
        }

        DB::beginTransaction();
        DB::table('galeries')->insert($result);
        DB::commit();

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::galery['store']), $result));
    }

    public function update(UpdateGaleryRequest $request, $id)
    {
        DB::beginTransaction();

        $result = DB::table('galeries')->select($this->columns)->where('id', $id);
        $galery = $result->first();

        if (!$galery) {
            DB::rollBack();
            return response()->json(DefaultResponse::parse('failed', $this->language->get(Language::common['notFound']), null), 404);
        }

        $fullPath = $galery->path;

        if ($request->file) {
            $path = 'app/';
            $storagePath = 'galeries/';
            $name = bin2hex(random_bytes(10)) . '.' . $request->extension;
            $fullPath = $path . $storagePath . $name;

            $deletePath = explode("app/", $galery->path);
            $deletePath = $deletePath[1];

            Storage::delete($deletePath);
            Image::make($request->file)->save(storage_path($fullPath));
        }


        $result->update([
            'name' => $request->name,
            'type' => $request->type,
            'path' => $fullPath,
            'updated_at' => Date::now(),
        ]);

        DB::commit();

        $result = $result->first();

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::galery['update']), $result));
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();

        $result = DB::table('galeries')->select($this->columns)->whereIn('id', $request->id ?? []);
        $galeries = $result->get();

        if (count($galeries) == 0) {
            DB::rollBack();
            return response()->json(DefaultResponse::parse('failed', $this->language->get(Language::common['notFound']), null), 404);
        }

        if ($request->permanent === true) {
            foreach ($galeries as $galery) {
                $deletePath = explode("app/", $galery->path);
                $deletePath = $deletePath[1];

                Storage::delete($deletePath);
            }

            $result->delete();
        } else {
            $result->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => Date::now(),
            ]);
        }

        DB::commit();

        $result = $result->get();

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::galery['delete']), $result));
    }

    public function restore(Request $request)
    {
        DB::beginTransaction();

        $result = DB::table('galeries')->select($this->columns)->whereIn('id', $request->id ?? []);

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

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::galery['restore']), $result));
    }

    public function clear(Request $request)
    {
        DB::beginTransaction();

        $result = DB::table('galeries')->select($this->columns)->whereNull('deleted_at');
        $galeries = $result->get();

        if (count($galeries) == 0) {
            DB::rollBack();
            return response()->json(DefaultResponse::parse('failed', $this->language->get(Language::common['notFound']), null), 404);
        }

        if ($request->permanent === true) {
            foreach ($galeries as $galery) {
                $deletePath = explode("app/", $galery->path);
                $deletePath = $deletePath[1];

                Storage::delete($deletePath);
            }

            $result->delete();
        } else {
            $result->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => Date::now(),
            ]);
        }

        DB::commit();

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::galery['clear'])));
    }

    public function restoreAll()
    {
        DB::beginTransaction();

        $result = DB::table('galeries')->select($this->columns)->whereNotNull('deleted_at');

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

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::galery['restoreAll'])));
    }

    public function getImage(GetImageRequest $request)
    {
        $result = DB::table('galeries')->select($this->columns)->where('id', $request->id)->first();

        if (!$result) {
            return response()->json(DefaultResponse::parse('failed', $this->language->get(Language::common['notFound']), null), 404);
        }

        $image = storage_path($result->path);
        return response()->file($image, ['Access-Control-Allow-Origin' => '*']);
    }
}
