<?php

namespace App\Http\Controllers;

use App\Models\Cabinet;
use App\Models\Member;
use App\Models\Provider;
use App\Models\Role;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CabinetController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            $cabinets = Cabinet::with(['domains.currentCampaign', 'domains.campaigns'])->with('providers')->get();
            return response()->json($cabinets);
        }

        return response()->json($user->cabinets());
    }

    public function show(int $cabinetId): JsonResponse
    {
        $cabinet = Cabinet::where('id', $cabinetId)->with('members')->with('providers')->first();

        return response()->json($cabinet);
    }

    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();
        $cabinetId = null;

        $validateData = $request->validate([
            "name" => "required|min:4|unique:cabinets"
        ]);

        $cabinetId = DB::transaction(function () use ($validateData, $user) {
            $role = Role::where('name', 'Admin')->first();

            $cabinet = new Cabinet();
            $cabinet->name = $validateData["name"];
            $cabinet->save();

            $providers = Provider::all();
            $cabinet->providers()->attach($providers->pluck('id'));

            $member = new Member();
            $member->user_id = $user->id;
            $member->cabinet_id = $cabinet->id;
            $member->role_id = $role->id;
            $member->save();
            return $cabinet->id;
        });

        return response()->json(['message' => 'ok', 'cabinetId' => $cabinetId], 201);
    }

    public function update(Request $request, int $cabinetId): JsonResponse
    {
        $validateData = $request->validate([
            'name' => 'required|min:2'
        ]);

        $cabinet = Cabinet::where('id', $cabinetId)->with('members')->with('providers')->first();
        $cabinet->name = $validateData['name'];

        $cabinet->save();

        return response()->json($cabinet);
    }

    public function attachProvider(int $cabinetId, int $id)
    {
        $provider = Provider::find($id);
        if (!empty($provider)) {
            $cabinet = Cabinet::find($cabinetId);
            $cabinet->providers()->attach($provider->id);
            $cabinet->save();
            return response()->json(['message' => 'ok']);
        }

        throw new \Exception('something went wrong');
    }

    public function detachProvider(int $cabinetId, int $id)
    {
        $provider = Provider::find($id);
        if (!empty($provider)) {
            $cabinet = Cabinet::find($cabinetId);
            $cabinet->providers()->detach($provider->id);
            $cabinet->save();
            return response()->json(['message' => 'ok']);
        }

        throw new \Exception('something went wrong');
    }

    public function destroy(int $cabinetId)
    {
        DB::transaction(function () use ($cabinetId) {
            $cabinet = Cabinet::find($cabinetId);
            $cabinet->delete();

            $cabinet->providers()->detach();

            $members = Member::where('cabinet_id', $cabinetId)->get()->map(function ($member) {
                return $member->id;
            });

            Member::destroy($members);
        });

        return response()->json(['message' => 'ok']);
    }
}
