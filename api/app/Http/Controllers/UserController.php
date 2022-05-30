<?php

namespace App\Http\Controllers;

use App\Exceptions\Base\AuthorizationException;
use App\Exceptions\InvalidCreditionalException;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use PHPUnit\Util\Exception;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $currentUser = auth()->user();
        if ($currentUser->isAdmin()) {
            $users = User::where('parrent_id', $currentUser->id)->with('roles')->get();
            return response()->json($users);
        }

        if ($currentUser->isSuperAdmin()) {
            $users = User::with('roles')->get();
            return response()->json($users);
        }

        throw new AuthenticationException('Doesnt have enough rights');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            throw new AuthenticationException('You are not the Admin');
        }

        $userData = $request->validate([
            'name' => 'required|min:2',
            'email' => 'required|email|min:8|unique:users',
            'password' => 'required|min:8'
        ]);

        $userData['password'] = bcrypt($userData['password']);
        $userData['parrent_id'] = auth()->user()->id;
        $userData['email_verified_at'] = date('Y-m-d');
        $role = Role::where('name', 'Employee')->first();
        $userData['role_id'] = $role->id;
        $userId = User::create($userData);

        return response()->json(["message" => "ok", 'userId' => $userId], 201);
    }

    public function destroy(int $id)
    {
        $admin = auth()->user();
        if ($admin->isSuperAdmin()) {
            $user = User::find($id);
            $user->delete();
            return response()->json(['message' => 'ok']);
        }

        if ($admin->isAdmin()) {
            $user = User::where('id', $id)->where('parrent_id', $admin->id)->first();
            $user->delete();
            return response()->json(['message' => 'ok']);
        }

        throw new AuthenticationException('You are not the Admin');
    }

    /**
     * @OA\Post (
     *     path="/api/users/me/change-password",
     *     summary="Список кабинетов",
     *     @OA\Parameter(
     *       name="passwordOld",
     *       in="query",
     *       required=true,
     *       @OA\Schema(
     *           type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *       name="passwordNew",
     *       in="query",
     *       required=true,
     *       @OA\Schema(
     *           type="string"
     *       )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Invalid creditionals",
     *     ),
     * )
     */
    public function changePassword(Request $request)
    {
        $user = auth()->user();

        $validateData = $request->validate([
            'passwordOld' => 'required|min:8',
            'passwordNew' => 'required|min:8'
        ]);

        if (!Hash::check($user->password, bcrypt($validateData['passwordOld']))) {
            throw new AuthorizationException('Dont correct password');
        }

        $user->password = $validateData['passwordNew'];
        $user->save();

        return response()->json(['message' => 'ok']);
    }
}
