<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Http\Resources\SettingResource;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function index()
    {
        $perPage = request('per_page', 10);
        $search = request('search', '');
        $sortField = request('sort_field', 'updated_at');
        $sortDirection = request('sort_direction', 'desc');

        $query = Setting::query()
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return SettingResource::collection($query);
    }

    public function destroy(Setting $setting)
    {
        $setting->delete();

        return response()->noContent();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|unique:settings,key',
            'value' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $setting = Setting::create([
            'key' => $request->input('key'),
            'value' => $request->input('value'),
        ]);

        return new SettingResource($setting);
    }

    public function update(Request $request, Setting $setting)
    {

        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255',
            'value' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $setting->update([
            'key' => $request->input('key'),
            'value' => $request->input('value'),
        ]);

        return new SettingResource($setting);
    }
}
