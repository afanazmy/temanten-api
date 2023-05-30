<?php

namespace App\Http\Controllers;

use App\Http\Responses\DefaultResponse;
use App\Locales\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public $language;

    public function __construct()
    {
        $this->language = new Language(Auth::user());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = DB::table('settings')->get();
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting->name] = $setting->value;
        }

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::common['success']), $result));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $settings = [];
        $settingNames = [];

        foreach ($request->all() as $key => $value) {
            array_push($settingNames, $key);
            array_push($settings, [
                'name' => $key,
                'value' => $value,
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ]);
        }

        DB::beginTransaction();

        DB::table('settings')->whereIn('name', $settingNames)->delete();
        DB::table('settings')->insert($settings);

        DB::commit();

        return response()->json(DefaultResponse::parse('success', $this->language->get(Language::setting['update']), null));
    }
}
