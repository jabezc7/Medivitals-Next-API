<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function index(): JsonResponse
    {
        $return = [];

        foreach (Setting::all() as $setting){
            $return[$setting->key] = $setting->value;
        }

        return response()->json($return);
    }

    public function store(Request $request): JsonResponse
    {
        foreach($request->all() as $key => $value){
            Setting::query()->updateOrCreate([
                'key' => $key,
            ],[
                'value' => $value
            ]);
        }

        return response()->json($request->all());
    }
}
