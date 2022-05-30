<?php
namespace App\Http\Controllers;

use App\Models\Tariff;
use App\Exceptions\Base\AuthorizationException;
use Illuminate\Http\Request;

class TariffController extends Controller
{
    public function index()
    {
        $tariffs = Tariff::where('code', '!=', 'custom')->get();
        return response()->json($tariffs);
    }

    public function show(int $id)
    {
        $tariff = Tariff::where('id', $id)->first();
        return response()->json($tariff);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            throw new AuthorizationException();
        }
        $validateData = $request->validate([
            'name' => 'required|min:2',
            'price' => 'required',
            'code' => 'required',
            'contacts' => 'required'
        ]);

        $tariff = new Tariff();
        $tariff->name = $validateData['name'];
        $tariff->code = $validateData['code'];
        $tariff->price = $validateData['price'];
        $tariff->contacts = $validateData['contacts'];
        $tariff->save();

        return response()->json(['id' => $tariff->id]);
    }

    public function update(Request $request, int $id)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            throw new AuthorizationException();
        }
        $validateData = $request->validate([
            'name' => 'required|min:2',
            'code' => 'required',
            'price' => 'required',
            'contacts' => 'required'
        ]);

        $tariff = Tariff::where('id', $id)->first();
        $tariff->name = $validateData['name'];
        $tariff->code = $validateData['code'];
        $tariff->price = $validateData['price'];
        $tariff->contacts = $validateData['contacts'];
        $tariff->save();

        return response()->json(['message' => 'ok']);
    }

    public function destroy(int $id)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            throw new AuthorizationException();
        }
        $tariff = Tariff::where('id', $id)->first();
        $tariff->delete();

        return response()->json(['message' => 'ok']);
    }
}
