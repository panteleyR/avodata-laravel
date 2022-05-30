<?php

namespace App\Http\Controllers;

use App\Exceptions\Base\AuthorizationException;
use App\Exceptions\NoRoleException;
use App\Models\Cabinet;
use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    public function currentMember()
    {
        $user = auth()->user();
        $me = $user->currentMember();

        if (!$me) {
            throw new AuthorizationException();
        }

        return response()->json($me);
    }

    public function index(int $cabinetId)
    {
        $members = Member::where('cabinet_id', $cabinetId)->with('role', 'user')->get();

        return response()->json($members);
    }

    public function show(int $cabinetId, int $id)
    {
        $member = Member::where('cabinet_id', $cabinetId)->where('id', $id)->with('role', 'user')->first();

        return response()->json($member);
    }

    public function store(Request $request, int $cabinetId)
    {
        $user = auth()->user();
        $validateData = $request->validate([
            "email" => "required|email|exists:users,email|min:4",
            "roleId" => "required|integer|exists:roles,id"
        ]);

        if ($user->isSuperAdmin() || $user->isAdmin()) {
            $user = User::where('email', $validateData['email'])->whereNotNull('email_verified_at')->first();

            $memberIsAlreadyExist = Member::where('user_id', $user->id)->where('cabinet_id', $cabinetId)->first();
            if ($memberIsAlreadyExist) {
                throw new \Exception('member is already exist', 422);
            }

            $member = new Member();
            $member->user_id = $user->id;
            $member->cabinet_id = $cabinetId;
            $member->role_id = $validateData['roleId'];
            $member->save();
            $memberId = $member->id;

            return response()->json(['message' => 'ok', 'memberId' => $memberId], 201);
        }

        throw new AuthorizationException();
    }

    public function update(Request $request, int $cabinetId, int $id)
    {
        $user = auth()->user();
        $validateData = $request->validate([
            'roleId' => 'required|integer|exists:roles,id'
        ]);

        if ($user->id === $id) {
            throw new AuthorizationException('You are can\'t delete yourself from cabinet, please, ask another admin to do it');
        }

        if ($user->isSuperAdmin() || $user->isAdmin()) {
            $member = Member::where('cabinet_id', $cabinetId)->where('id', $id)->first();

            $member->role_id = $validateData["roleId"];
            $member->save();

            return response()->json($member);
        }

        throw new AuthorizationException();
    }

    public function destroy(int $cabinetId, int $id)
    {
        $user = auth()->user();
        if ($user->id === $id) {
            throw new AuthenticationException('You are can\'t delete yourself from cabinet, please, ask another admin to do it');
        }

        if ($user->isSuperAdmin() || $user->isAdmin()) {
            $member = Member::where('cabinet_id', $cabinetId)->where('id', $id)->first();
            $member->delete();

            return response()->json($member);
        }

        throw new AuthorizationException();
    }
}
