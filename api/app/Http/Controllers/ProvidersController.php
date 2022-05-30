<?php
namespace App\Http\Controllers;

use App\Exceptions\Base\AuthorizationException;
use App\Models\Provider;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class ProvidersController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            throw new AuthorizationException('You are not the superAdmin');
        }

        $providers = Provider::all();
        return $providers;
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            throw new AuthorizationException('You are not the superAdmin');
        }

        $providerData = $request->validate([
            'name' => 'required|min:2',
            'code' => 'required',
            'ip' => 'nullable',
            'token' => 'nullable',
            'description' => 'nullable'
        ]);
        $providerId = Provider::create($providerData);

        return response()->json(['message' => 'ok', 'providerId' => $providerId], 201);
    }

    public function update(Request $request, int $id)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            throw new AuthorizationException('You are not the superAdmin');
        }

        $providerData = $request->validate([
            'name' => 'required|min:2',
            'description' => 'nullable',
            'code' => 'required',
            'ip' => 'nullable',
            'token' => 'nullable',
            'on' => 'required',
            'price' => 'required'
        ]);
        $provider = Provider::find($id);
        $provider->name = $providerData['name'];
        $provider->code = $providerData['code'];
        $provider->on = $providerData['on'];
        $provider->price = $providerData['price'];
        if ($providerData['description']) {
            $provider->description = $providerData['description'];
        }
        if ($providerData['ip']) {
            $provider->ip = $providerData['ip'];
        }
        if ($providerData['token']) {
            $provider->token = $providerData['token'];
        }
        $provider->save();

        return response()->json(['message' => 'ok']);
    }

    public function destroy(int $id)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            throw new AuthorizationException('You are not the superAdmin');
        }

        $provider = Provider::find($id);
        $provider->delete();
        return response()->json(['message' => 'ok']);
    }
}
