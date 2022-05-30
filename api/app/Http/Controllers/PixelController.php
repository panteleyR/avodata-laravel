<?php
namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Domain;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Provider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class PixelController extends Controller
{
    public function showDomain(Request $request)
    {
        $token = $request->bearerToken();
        $domain = Domain::where('token', $token)->with('cabinet')->with('currentCampaign')->first();
        $domain->last_script_activity = Carbon::now();
        $domain->save();

        return response()->json($domain);
    }

    public function showProviders(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) {
            $providers = Provider::all();
            return $providers;
        }

        $domain = Domain::where('token', $token)->with('cabinet')->first();

        $providers = Provider::whereHas('cabinets', function (Builder $query) use ($domain) {
            $query->where('cabinet_id', $domain->cabinet_id);
        })->get();
        return $providers;
    }

    public function reduceCampaignBalance(Request $request, int $domainId)
    {

        $campaign = Campaign::where('domain_id', $domainId)
            ->where('from', '<=', Carbon::now())
            ->where('to', '>=', Carbon::now())
            ->first();
        if (!$campaign) {
            throw new \Exception('campaign doesnt exist', 403);
        }
        $campaign->balance = $campaign->balance - 1;
        $campaign->save();

        return response()->json(['message' => 'ok']);
    }

    public function increaseCampaignBalance(Request $request, int $domainId)
    {
        $campaign = Campaign::where('domain_id', $domainId)
            ->where('from', '<=', Carbon::now())
            ->where('to', '>=', Carbon::now())
            ->first();

        if ($campaign) {
            if ($count = $request->input('count')) {
                $campaign->balance = $campaign->balance + $count;
            } else {
                $campaign->balance = $campaign->balance + 1;
            }

            $campaign->save();

            Log::info('app.increaseCampaignBalance', [
                'count' => $count,
                'domain' => $domainId,
                'campaign' => $campaign->id
            ]);
        }

        return response()->json(['message' => 'ok']);
    }
}
