<?php
namespace App\Http\Controllers;

use App\Events\VerifyUserDone;
use App\Exceptions\InvalidCreditionalException;
use App\Contracts\Confirm;
use App\Exceptions\NoAuthorizateException;
use App\Models\Role;
use App\Models\TemporaryUser;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    public function user()
    {
        if($user = auth()->user()) {
            return User::where('id', $user->id)->first();
        }

        throw new AuthenticationException();
    }

    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'required|email|min:8',
            'password' => 'required|min:8'
        ]);

        if (!auth()->attempt($loginData)) {
            throw new InvalidCreditionalException();
        }

        $token = auth()->user()->createToken('authToken');
        $token->token->expires_at = Carbon::now()->addDay();
        $token->token->save();

        return response()->json(['user' => auth()->user(), 'access_token' => $token->accessToken]);
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }

    public function register(Request $request, Confirm $confirm)
    {
        $validatedData = $request->validate([
            'name' => 'required|min:2',
            'email' => 'required|email|min:8|unique:users',
            'password' => 'required|min:8'
        ]);

        $validatedData['name'] = $request->name;
        $validatedData['email'] = $request->email;
        $validatedData['password'] = bcrypt($request->password);

        $user = TemporaryUser::create($validatedData);
        $confirm::sendCode('codes:auth:email:' . $user->id, $user);

        return response(['userId' => $user->id]);
    }

    public function confirmEmail(Request $request, Confirm $confirm)
    {
        $validatedData = $request->validate([
            'code' => 'required|min:5',
            'userId' => 'required',
        ]);

        $confirm::checkCode('codes:auth:email:' . $validatedData['userId'], $validatedData['code']);

        $temporaryUser = TemporaryUser::find($validatedData['userId']);

        $userData = $temporaryUser->toArray();
        $userData['email_verified_at'] = date('Y-m-d');
        $user = User::create($userData);

        event(new VerifyUserDone($temporaryUser));

        return response(['user' => $user]);
    }

    public function confirmEmailCodeAgain(Request $request, Confirm $confirm)
    {
        $user = TemporaryUser::find($request->userId);
        $confirm::sendCode('codes:auth:email:' . $user->id, $user);

        return response(['userId' => $user->id]);
    }

    public function restore(Request $request, Confirm $confirm)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|exists:users|min:8'
        ]);

        $user = User::where('email', $validatedData['email'])->first();
        $confirm::sendCode('codes:auth:restore:' . $user->id, $user);

        return response(['userId' => $user->id]);
    }

    public function confirmRestoreCodeAgain(Request $request, Confirm $confirm)
    {
        $user = User::find($request->userId);
        $confirm::sendCode('codes:auth:restore:' . $user->id, $user);

        return response(['userId' => $user->id]);
    }

    public function confirmRestore(Request $request, Confirm $confirm)
    {
        $validatedData = $request->validate([
            'userId' => 'required|exists:users,id',
            'password' => 'required|min:8',
            'code' => 'required|min:5'
        ]);
        $confirm::checkCode('codes:auth:restore:' . $validatedData['userId'], $validatedData['code']);

        $user = User::find($validatedData['userId']);
        $user->password = bcrypt($validatedData['password']);
        $user->save();

        return response(['user' => $user]);
    }
}
