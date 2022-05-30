<?php
namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\PaymentDocument;
use App\Exceptions\Base\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PaymentController extends Controller
{
    public function indexAll(Request $request)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            throw new AuthorizationException();
        }



        $from = $request->input('from', Carbon::now()->startOfMonth()->format('Y-m-d H:i:s'));
        $to = $request->input('to', Carbon::now()->endOfMonth()->format('Y-m-d H:i:s'));
        $payments = PaymentDocument::with(['campaign.tariff', 'campaign.domain.cabinet'])->whereHas('campaign.domain.cabinet', function ($q) use ($request) {
            if ($cabinetId = $request->input('cabinetId')) {
                $q->where('domains.cabinet_id', $cabinetId);
            }

            if ($domainId = $request->input('domainId')) {
                $q->where('domains.id', $domainId);
            }

            if ($campaignId = $request->input('campaignId')) {
                $q->where('campaigns.id', $campaignId);
            }
        });
        $payments = $payments->where('created_at', '>=', $from)->where('created_at', '<=', $to);
        $payments = $payments->get();
        return response()->json($payments);
    }

    public function index(Request $request, int $cabinetId, int $domainId, int $campaignId)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            throw new AuthorizationException();
        }
        $payments = PaymentDocument::where('camaign_id', $campaignId)->get();
        return response()->json($payments);
    }

    public function show(Request $request, int $cabinetId, int $domainId, int $campaignId, int $paymentId)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            throw new AuthorizationException();
        }
        $payment = PaymentDocument::where('camaign_id', $campaignId)->where('id', $paymentId)->first();
        return response()->json($payment);
    }

    public function store(Request $request, int $cabinetId, int $domainId, int $campaignId)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            throw new AuthorizationException();
        }
        $validateData = $request->validate([
            'arrival' => 'required',
            'date' => 'required'
        ]);
        $campaign = Campaign::where('id', $campaignId)->first();
        if (!$campaign) {
            throw new AuthorizationException();
        }

        $payment = new PaymentDocument();
        $payment->campaign_id = $campaignId;
        $payment->arrival = $validateData['arrival'];
        $payment->arrival_at = $validateData['date'];
        $payment->save();

        return response()->json(['id' => $payment->id]);
    }
}
