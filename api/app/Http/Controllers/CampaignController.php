<?php
namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Exceptions\Base\AuthorizationException;
use App\Models\Tariff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CampaignController extends Controller
{
    public function indexAdmin(Request $request)
    {
        $from = $request->input('from', Carbon::now()->startOfMonth()->format('Y-m-d H:i:s'));
        $to = $request->input('to', Carbon::now()->endOfMonth()->format('Y-m-d H:i:s'));
        $from = $this->standartFrom($from);
        $to = $this->standartTo($to);

        $payments = Campaign::with(['tariff', 'domain.cabinet'])->whereHas('domain.cabinet', function ($q) use ($request) {
            if ($cabinetId = $request->input('cabinetId')) {
                $q->where('domains.cabinet_id', $cabinetId);
            }

            if ($domainId = $request->input('domainId')) {
                $q->where('domains.id', $domainId);
            }

            if ($campaignId = $request->input('campaignId')) {
                $q->where('id', $campaignId);
            }
        });
        $payments = $payments->where('from', '>=', $from)->where('to', '<=', $to);
        $payments = $payments->get();

        return $payments;
    }

    public function index(Request $request, int $cabinetId, int $domainId)
    {
       $campaigns = Campaign::where('domain_id', $domainId)->with('payments')->get();
       return $campaigns;
    }

    public function show(Request $request, int $cabinetId, int $domainId, int $campaignId)
    {
        $campaign = Campaign::where('domain_id', $domainId)->where('id', $campaignId)->first();
        return $campaign;
    }

    public function store(Request $request, int $cabinetId, int $domainId)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            throw new AuthorizationException();
        }
        $validateData = $request->validate([
            'discount' => 'required',
            'tariffId' => 'required',
            'from' => 'required',
            'to' => 'required'
        ]);

        $tariff = Tariff::where('id', $validateData['tariffId'])->first();
        if (!$tariff) {
            throw new \Exception('tariff is doesnt exist', 403);
        }

        $validateData['from'] = $this->standartFrom($validateData['from']);
        $validateData['to'] = $this->standartTo($validateData['to']);

        $campaign = Campaign::where('domain_id', $domainId)
            ->where(function ($q) use ($validateData) {
                $q->where(function ($q) use ($validateData) {
                    $q->where('from', '>=', $validateData['from']);
                    $q->where('to', '<=', $validateData['to']);
                });
                $q->orWhere(function ($q) use ($validateData) {
                    $q->where('from', '<=', $validateData['from']);
                    $q->where('to', '>=', $validateData['to']);
                });
                $q->orWhere(function ($q) use ($validateData) {
                    $q->where('from', '<=', $validateData['from']);
                    $q->where('to', '>=', $validateData['from']);
                    $q->where('to', '<=', $validateData['to']);
                });
                $q->orWhere(function ($q) use ($validateData) {
                    $q->where('from', '>=', $validateData['from']);
                    $q->where('from', '<=', $validateData['to']);
                    $q->where('to', '>=', $validateData['to']);
                });
            })
            ->first();

        if ($campaign) {
            throw new \Exception('campaign in the period is already exist');
        }

        if ($tariff->code === 'personal') {
            $validateData = $request->validate([
                'discount' => 'required',
                'tariffId' => 'required',
                'from' => 'required',
                'to' => 'required',
                'price' => 'required',
                'contacts' => 'required'
            ]);

            $tariff = new Tariff();
            $tariff->name = 'Персонал '. $domainId . '-' . date('Y-m-d H:i:s');
            $tariff->code = 'custom';
            $tariff->price = $validateData['price'];
            $tariff->contacts = $validateData['contacts'];
            $tariff->save();
        }

        $campaign = new Campaign();
        $campaign->domain_id = $domainId;
        $campaign->balance = $tariff->contacts;
        $campaign->discount = $validateData['discount'];
        $campaign->tariff_id = $tariff->id;
        $campaign->from = $validateData['from'];
        $campaign->to = $validateData['to'];
        $campaign->save();

        return response()->json(['id' => $campaign->id]);
    }

    private function standartFrom(string $date)
    {
        $date = explode(' ', $date);
        return $date[0] . ' 00:00:00';
    }

    private function standartTo(string $date)
    {
        $date = explode(' ', $date);
        return $date[0] . ' 23:59:59';
    }

    public function update(Request $request, int $cabinetId, int $domainId, int $campaignId)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            throw new AuthorizationException();
        }
        $validateData = $request->validate([
            'balance' => 'required',
            'discount' => 'required',
            'tariffId' => 'required',
            'from' => 'required',
            'to' => 'required'
        ]);

        $validateData['from'] = $this->standartFrom($validateData['from']);
        $validateData['to'] = $this->standartTo($validateData['to']);

        $campaignCheck = Campaign::where('domain_id', $domainId)
            ->where('id', '!=', $campaignId)
            ->where(function ($q) use ($validateData) {
                $q->where(function ($q) use ($validateData) {
                    $q->where('from', '>=', $validateData['from']);
                    $q->where('to', '<=', $validateData['to']);
                });
                $q->orWhere(function ($q) use ($validateData) {
                    $q->where('from', '<=', $validateData['from']);
                    $q->where('to', '>=', $validateData['to']);
                });
                $q->orWhere(function ($q) use ($validateData) {
                    $q->where('from', '<=', $validateData['from']);
                    $q->where('to', '>=', $validateData['from']);
                    $q->where('to', '<=', $validateData['to']);
                });
                $q->orWhere(function ($q) use ($validateData) {
                    $q->where('from', '>=', $validateData['from']);
                    $q->where('from', '<=', $validateData['to']);
                    $q->where('to', '>=', $validateData['to']);
                });
            })
            ->first();

        if ($campaignCheck) {
            throw new \Exception('campaign in the period is already exist');
        }

        $campaign = Campaign::where('domain_id', $domainId)->where('id', $campaignId)->first();
        $campaign->balance = $validateData['balance'];
        $campaign->discount = $validateData['discount'];
        $campaign->tariff_id = $validateData['tariffId'];
        $campaign->from = $validateData['from'];
        $campaign->to = $validateData['to'];
        $campaign->save();

        return response()->json(['message' => 'ok']);
    }

    public function destroy(Request $request, int $cabinetId, int $domainId, int $campaignId)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            throw new AuthorizationException();
        }
        $tariff = Campaign::where('domain_id', $domainId)->where('id', $campaignId)->first();
        $tariff->delete();

        return response()->json(['message' => 'ok']);
    }
}
