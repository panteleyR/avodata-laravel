<?php

namespace App\Http\Controllers;

use App\Exceptions\Base\AuthorizationException;
use App\Exceptions\InvalidCreditionalException;
use App\Models\Campaign;
use App\Models\Domain;
use App\Models\GeoCity;
use App\Models\Tariff;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DomainController extends Controller
{
    private function transliterate($input){
        $gost = array(
            "a"=>"а","b"=>"б","v"=>"в","g"=>"г","d"=>"д","e"=>"е","yo"=>"ё",
            "j"=>"ж","z"=>"з","i"=>"и","i"=>"й","k"=>"к",
            "l"=>"л","m"=>"м","n"=>"н","o"=>"о","p"=>"п","r"=>"р","s"=>"с","t"=>"т",
            "y"=>"у","f"=>"ф","h"=>"х","c"=>"ц",
            "ch"=>"ч","sh"=>"ш","sh"=>"щ","i"=>"ы","e"=>"е","u"=>"у","ya"=>"я","A"=>"А","B"=>"Б",
            "V"=>"В","G"=>"Г","D"=>"Д", "E"=>"Е","Yo"=>"Ё","J"=>"Ж","Z"=>"З","I"=>"И","I"=>"Й","K"=>"К","L"=>"Л","M"=>"М",
            "N"=>"Н","O"=>"О","P"=>"П",
            "R"=>"Р","S"=>"С","T"=>"Т","Y"=>"Ю","F"=>"Ф","H"=>"Х","C"=>"Ц","Ch"=>"Ч","Sh"=>"Ш",
            "Sh"=>"Щ","I"=>"Ы","E"=>"Е", "U"=>"У","Ya"=>"Я","'"=>"ь","'"=>"Ь","''"=>"ъ","''"=>"Ъ","j"=>"ї","i"=>"и","g"=>"ґ",
            "ye"=>"є","J"=>"Ї","I"=>"І",
            "G"=>"Ґ","YE"=>"Є"
        );
        return strtr($input, $gost);
    }

    public function index(int $cabinetId)
    {
        $domains = Domain::where('cabinet_id', $cabinetId)->with('currentCampaign')->get();
        return response()->json($domains);
    }

    public function rootIndex()
    {
        $domains = Domain::with('currentCampaign')->get();
        return response()->json($domains);
    }

    private function getRussianName(string $str)
    {
        if (!$str) {
            return '';
        }

        $strArray = explode(",", $str);
        for ($i = (count($strArray) - 1); $i > 0; $i--) {
            if (preg_match('/[А-Я][а-я]/u', $strArray[$i])) {
                return $strArray[$i];
            }
        }
        return '';
    }

    public function show(int $cabinetId, int $id)
    {
        $domain = Domain::where('id', $id)->where('cabinet_id', $cabinetId)->with('geo')->with('currentCampaign')->first();
        $domain->geo->map(function ($el) {
            if ($el->alternate_names) {
//                $el->name = $this->getRussianName($el->alternate_names);
            }
            return $el;
        });
        return response()->json($domain);
    }

    public function store(int $cabinetId, Request $request)
    {
        $user = auth()->user();
        if ($user->isAnalytic()) {
            throw new AuthorizationException('You are not the Admin');
        }

        $domainData = $request->validate([
            'name' => 'required|min:2',
            'domain' => 'required'
        ]);

        $domainCheck = Domain::where('cabinet_id', $cabinetId)
            ->where('name', $domainData['name'])
            ->where('domain', $domainData['domain'])
            ->first();

        if ($domainCheck) {
            throw new InvalidCreditionalException(422);
        }

        $token = bin2hex(openssl_random_pseudo_bytes(16));

        $domainData['token'] = $token;
        $domainData['cabinet_id'] = $cabinetId;

        $startTariff = Tariff::where('code', 'quick start')->first();
        if (!$startTariff) {
            throw new \Exception('start tariff is doesnt exist');
        }

        $domainId = DB::transaction(function () use ($domainData, $user, $startTariff) {
            $domain = Domain::create($domainData);

            $campaign = new Campaign();
            $campaign->balance = $startTariff->contacts;
            $campaign->discount = 0;
            $campaign->tariff_id = $startTariff->id;
            $campaign->domain_id = $domain->id;
            $campaign->from = Carbon::now()->format('Y-m-d H:i:s');
            $campaign->to = Carbon::now()->addWeek()->format('Y-m-d H:i:s');
            $campaign->save();

            return $domain->id;
        });

        return response()->json(["message" => "ok", 'id' => $domainId], 201);
    }

    public function update(int $cabinetId, Request $request, int $id)
    {
        $user = auth()->user();

        if ($user->isAnalytic()) {
            throw new AuthorizationException('You are not the Admin');
        }

        $domainData = $request->validate([
            'name' => 'required|min:2',
            'domain' => 'nullable',
            'jsBefore' => 'nullable',
            'jsAfter' => 'nullable',
            'api_url' => 'nullable',
            'on' => 'required',
            'token' => 'nullable|min:20',
            'geo_on' => 'required',
            'geo_cities' => 'nullable|array'
        ]);


        if ($user->isSuperAdmin() && key_exists('domain', $domainData)) {
            $domainCheck = Domain::where('cabinet_id', $cabinetId)
                ->where('id', $id)
                ->first();

            if (!$domainCheck) {
                throw new InvalidCreditionalException(422);
            }
        }

        $domain = Domain::where('id', $id)->where('cabinet_id', $cabinetId)->first();
        $domain->name = $domainData['name'];
        $domain->on = $domainData['on'];
        $domain->geo_on = $domainData['geo_on'];
        if ($user->isSuperAdmin()) {
            $domain->domain = $domainData['domain'];
            $domain->token = $domainData['token'];
        }
        $domain->api_url = $domainData['api_url'];
        $domain->js_before = $domainData['jsBefore'];
        $domain->js_after = $domainData['jsAfter'];

        if (
            isset($domainData['geo_cities'])
            && is_array($domainData['geo_cities'])
            && (GeoCity::whereIn('id', $domainData['geo_cities'])->exists() || $domainData['geo_cities'] === [])
        ) {
            $domain->geo()->sync($domainData['geo_cities']);
        }

        $domain->save();

        return response()->json(['message' => 'ok']);
    }

    public function destroy(int $cabinetId, int $id)
    {
        $user = auth()->user();

        if ($user->isAnalytic()) {
            throw new AuthorizationException('You are not the Admin');
        }

        $domain = Domain::where('id', $id)->where('cabinet_id', $cabinetId)->first();
        $domain->delete();

        return response()->json(['message' => 'ok']);
    }
}
