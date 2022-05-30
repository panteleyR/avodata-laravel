<?php

namespace App\Http\Controllers;

use App\Contracts\MainMicroserviceConnector;
use App\Contracts\UserService;
use App\Models\AdjustmentContacts;
use App\Models\Contact;
use App\Models\Session;
use App\Models\User;
use App\Models\UserDomain;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataController extends Controller
{
    public function adminUsers(
        MainMicroserviceConnector $mainMicroserviceConnector,
        UserService $user,
        Request $request
    ) {
        if (!$user->get()->is_super_admin) {
            throw new Exception('you doesnt have enough rights');
        }

        $from = $request->input('from', Carbon::now()->startOfMonth()->format('Y-m-d H:i:s'));
        $to   = $request->input('to', Carbon::now()->endOfMonth()->format('Y-m-d H:i:s'));

        if ($cabinetId = $request->input('cabinetId')) {
            $domains = $mainMicroserviceConnector->getCabinetDomains($request->bearerToken(), (int) $cabinetId);
            if (!$domains) {
//                throw new \Exception('domains dont exist', 404);
                return response()->json([]);
            }
            $domains = array_map(function ($val) {
                return $val->id;
            }, $domains);

            if ($searchDomainId = $request->input('domainId')) {
                $domains = array_filter($domains, function ($val) use ($searchDomainId) {
                    return $val === (int) $searchDomainId;
                });
            }

            $users = UserDomain::with('contacts')
                ->whereIn('domain_id', $domains)
                ->where('created_at', '>', $from)
                ->where('created_at', '<=', $to)
                ->orderByDesc(
                    Contact::select('created_at')
                        ->whereColumn('contacts.user_id', 'user_domain.user_id')
                        ->latest()
                        ->take(1)
                );
        } else {
            $users = UserDomain::with('contacts')
                ->where('created_at', '>', $from)
                ->where('created_at', '<=', $to)
                ->orderByDesc(
                    Contact::select('created_at')
                        ->whereColumn('contacts.user_id', 'user_domain.user_id')
                        ->latest()
                        ->take(1)
                );
        }

        return $this->respondPaginate($users);
    }

    public function users(
        MainMicroserviceConnector $mainMicroserviceConnector,
        UserService $user,
        Request $request,
        ?int $cabinet_id
    ) {
        if ($cabinet_id) {
            $domains = $mainMicroserviceConnector->getCabinetDomains($request->bearerToken(), (int) $cabinet_id);

            if (!$domains) {
//                throw new \Exception('domains dont exist', 404);
                return response()->json([]);
            }

            $domains = array_map(function ($val) {
                return $val->id;
            }, $domains);

            if ($searchDomainId = $request->input('domainId')) {
                $domains = array_filter($domains, function ($val) use ($searchDomainId) {
                    return $val === (int) $searchDomainId;
                });
            }

            $from = $request->input('from', Carbon::now()->startOfMonth()->format('Y-m-d H:i:s'));
            $to   = $request->input('to', Carbon::now()->endOfMonth()->format('Y-m-d H:i:s'));

            $users = UserDomain::with('contacts')
                ->whereIn('domain_id', $domains)
                ->where('created_at', '>', $from)
                ->where('created_at', '<=', $to)
                ->orderByDesc(
                    Contact::select('created_at')
                        ->whereColumn('contacts.user_id', 'user_domain.user_id')
                        ->latest()
                        ->take(1)
                );
        } else {
            if (!$user->get()->is_super_admin) {
                throw new Exception('you doesnt have enough rights');
            }
            $users = DB::table('user_domain')->get();
            $users = User::with('contacts')->whereIn('id', $users->pluck('user_id'));
        }

        return $this->respondPaginate($users);
    }

    public function statistics(
        MainMicroserviceConnector $mainMicroserviceConnector,
        Request $request,
        UserService $user,
        ?int $cabinet_id
    ) {
        $now                     = Carbon::now();
        $days                    = $now->daysInMonth;
        $sessionsMonthStatistics = [];
        $usersMonthStatistics    = [];
        $sessions                = 0;
        $users                   = 0;
        $domains                 = null;
        $from                    = $request->input('from', Carbon::now()->startOfMonth()->format('Y-m-d H:i:s'));
        $to                      = $request->input('to', Carbon::now()->endOfMonth()->format('Y-m-d H:i:s'));

        if (!$cabinet_id && $user->get()->is_super_admin) {
            $sessions = DB::table('sessions')->count();
            $users    = DB::table('user_domain')->count();
        }

        $cabinet_id && $domains = $mainMicroserviceConnector->getCabinetDomains($request->bearerToken(), $cabinet_id);
        if (!$domains) {
            for ($i = 1; $i <= $days; $i++) {
                $sessionsMonthStatistics[$i] = 0;
                $usersMonthStatistics[$i]    = 0;
            }
        } else {
            $domains = array_map(function ($val) {
                return $val->id;
            }, $domains);
            if ($searchDomainId = $request->input('domainId')) {
                $domains = array_filter($domains, function ($val) use ($searchDomainId) {
                    return $val === (int) $searchDomainId;
                });
            }

            for ($i = 1; $i <= $days; $i++) {
                $dayZ                        = Carbon::parse($now->year . '-' . $now->month . '-' . $i);
                $timeFrom                    = $dayZ->format('Y-m-d');
                $timeTo                      = $dayZ->addDay()->format('Y-m-d');
                $sessionsMonthStatistics[$i] = Session::whereIn('domain_id', $domains)->where('created_at', '>',
                    $timeFrom)->where('created_at', '<=', $timeTo)->count();
                $usersMonthStatistics[$i]    = DB::table('user_domain')->whereIn('domain_id',
                    $domains)->where('created_at', '>', $timeFrom)->where('created_at', '<=', $timeTo)->count();
            }

            $sessions = Session::whereIn('domain_id', $domains)
                ->where('created_at', '>', $from)
                ->where('created_at', '<=', $to)
                ->count();
            $users    = DB::table('user_domain')
                ->whereIn('domain_id', $domains)
                ->where('created_at', '>', $from)
                ->where('created_at', '<=', $to)
                ->count();
        }

        return response([
            'sessions'                => $sessions,
            'users'                   => $users,
            'sessionsMonthStatistics' => $sessionsMonthStatistics,
            'usersMonthStatistics'    => $usersMonthStatistics,
        ]);
    }

    public function adminStatistics(UserService $user)
    {
        $now                     = Carbon::now();
        $days                    = $now->daysInMonth;
        $sessionsMonthStatistics = [];
        $usersMonthStatistics    = [];
        $sessions                = 0;
        $users                   = 0;
        $domains                 = null;
        for ($i = 1; $i <= $days; $i++) {
            $sessionsMonthStatistics[$i] = 0;
            $usersMonthStatistics[$i]    = 0;
        }

        if ($user->get()->is_super_admin) {
            $sessions = DB::table('sessions')->count();
            $users    = DB::table('user_domain')->count();
        } else {
            throw new Exception('not rights');
        }

        return response([
            'sessions'                => $sessions,
            'users'                   => $users,
            'sessionsMonthStatistics' => $sessionsMonthStatistics,
            'usersMonthStatistics'    => $usersMonthStatistics,
        ]);
    }

    public function adminProviderStatistic(UserService $user, Request $request)
    {
        if (!$user->get()->is_super_admin) {
            throw new Exception('you doesnt have enough rights');
        }

        $from = $request->input('from', Carbon::now()->startOfMonth()->format('Y-m-d H:i:s'));
        $to   = $request->input('to', Carbon::now()->endOfMonth()->format('Y-m-d H:i:s'));

        $providers = DB::select('select count(id) as count, provider_id from providers_run where created_at > "' . $from . '" and created_at < "' . $to . '" group by provider_id');
        $contacts  = DB::select('select count(id) as count, provider_id from contacts where created_at > "' . $from . '" and created_at <= "' . $to . '" group by provider_id');

        return response()->json(['providers' => $providers, 'contacts' => $contacts]);
    }

    public function adminSessions(UserService $user, ?int $user_id)
    {
        if (!$user->get()->is_super_admin) {
            throw new Exception('you doesnt have enough rights');
        }

        $sessions = Session::where('user_id', $user_id);

        return $this->respondPaginate($sessions);
    }

    public function sessions(
        MainMicroserviceConnector $mainMicroserviceConnector,
        Request $request,
        int $cabinet_id,
        int $user_id
    ) {
        $domains = $mainMicroserviceConnector->getCabinetDomains($request->bearerToken(), $cabinet_id);
        if (!$domains) {
            return response()->json([]);
        }
        $domains = array_map(function ($val) {
            return $val->id;
        }, $domains);

        $sessions = Session::whereIn('domain_id', $domains)->where('user_id', $user_id);

        return $this->respondPaginate($sessions);
    }

    public function userCsv(
        MainMicroserviceConnector $mainMicroserviceConnector,
        UserService $user,
        Request $request,
        int $cabinet_id
    ) {
        $domains = $mainMicroserviceConnector->getCabinetDomains($request->bearerToken(), (int) $cabinet_id);
        if (!$domains) {
            throw new Exception('domains dont exist', 404);
        }

        if ($searchDomainId = (int) $request->input('domainId')) {
            $domains = array_filter($domains, function ($val) use ($searchDomainId) {
                return $val->id === $searchDomainId;
            });

            if (!$domains) {
                throw new Exception('domain doesnt exist', 404);
            }
        }

        $domainsIds = array_map(function ($val) {
            return $val->id;
        }, $domains);

        $from = $request->input('from', Carbon::now()->startOfMonth()->format('Y-m-d H:i:s'));
        $to   = $request->input('to', Carbon::now()->endOfMonth()->format('Y-m-d H:i:s'));

        $users = UserDomain::with('contacts')
            ->whereIn('domain_id', $domainsIds)
            ->where('created_at', '>', $from)
            ->where('created_at', '<=', $to)
            ->orderByDesc(
                Contact::select('created_at')
                    ->whereColumn('contacts.user_id', 'user_domain.user_id')
                    ->latest()
                    ->take(1)
            )->get();

        /**
         * @TODO Create helper for processing data (functions: userCsv, adminUserCsv)
         */
        $fp = fopen('php://temp', 'r+b');

        fputs($fp, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM

        // Headers of CSV
        fputcsv($fp, ["Последнее определение", "Контакты", "Сессии", "Домен", "id"], ';');

        foreach ($users as $keyUsers => $item) {
            if (isset($item->contacts[0])) {
                $fpItem = [$item->contacts[0]->created_at];

                if (count($item->contacts) > 1) {
                    $fpContacts = [];

                    foreach ($item->contacts as $key => $contact) {
                        if ($key !== ($item->contacts->keys()->last())) {
                            $fpContacts[] = $contact->value . "\n";
                        } else {
                            $fpContacts[] = $contact->value;
                        }
                    }

                    $fpItem[] = implode("\n", $fpContacts);
                } else {
                    $fpItem[] = $item->contacts[0]->value;
                }

                // Берем последнюю сессию, дубликатов не должно быть
                $lastSession = Session::where('domain_id', $item->domain_id)->where('user_id', $item->user_id)->first();

                $fpItem[] = implode("\n", [
                    $lastSession->url,
                    $lastSession->title,
                    'ref=' . $lastSession->ref,
                ]);

                $domainId   = $lastSession->domain_id;
                $domainName = array_filter($domains, function ($val) use ($domainId) {
                    return $val->id === $domainId;
                });

                $domainName = array_shift($domainName);
                $domainName = $domainName->name;

                $fpItem[] = $domainName;
                $fpItem[] = $item->user_id;

                fputcsv($fp, $fpItem, ';');
            }
        }

        // Reset cursor
        rewind($fp);

        // Get content from stream and removes spaces at the start and end of a string
        $output = stream_get_contents($fp);
        fclose($fp);

        return response($output)
            ->header('Content-Type', 'application/csv; charset=utf-8')
            ->header('Content-Encoding', 'utf-8')
            ->header('Content-disposition', 'attachment; filename="' . date('Y-m-d H:i:s') . '-phones.csv"');
    }

    public function adminUserCsv(
        MainMicroserviceConnector $mainMicroserviceConnector,
        UserService $user,
        Request $request
    ) {

        if (!$user->get()->is_super_admin) {
            throw new Exception('you doesnt have enough rights');
        }

        if ($cabinetId = $request->input('cabinetId')) {
            $domains = $mainMicroserviceConnector->getCabinetDomains($request->bearerToken(), (int) $cabinetId);
        } else {
            $domains = $mainMicroserviceConnector->getDomains($request->bearerToken());
        }

        if (!$domains) {
            throw new Exception('domains dont exist', 404);
        }

        if ($searchDomainId = (int) $request->input('domainId')) {
            $domains = array_filter($domains, function ($val) use ($searchDomainId) {
                return $val->id === $searchDomainId;
            });

            if (!$domains) {
                throw new Exception('domain doesnt exist', 404);
            }
        }

        $domainsIds = array_map(function ($val) {
            return $val->id;
        }, $domains);

        $from = $request->input('from', Carbon::now()->startOfMonth()->format('Y-m-d H:i:s'));
        $to   = $request->input('to', Carbon::now()->endOfMonth()->format('Y-m-d H:i:s'));

//            сортируем по последнему контакту
        $users = UserDomain::with('contacts')
            ->whereIn('domain_id', $domainsIds)
            ->where('created_at', '>', $from)
            ->where('created_at', '<=', $to)
            ->orderByDesc(
                Contact::select('created_at')
                    ->whereColumn('contacts.user_id', 'user_domain.user_id')
                    ->latest()
                    ->take(1)
            )->get();

        /**
         * @TODO Create helper for processing data (functions: userCsv, adminUserCsv)
         */
        $fp = fopen('php://temp', 'r+b');

        fputs($fp, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM

        // Headers of CSV
        fputcsv($fp, ["Последнее определение", "Контакты", "Сессии", "Домен", "id"], ';');

        foreach ($users as $keyUsers => $item) {
            $fpItem = [$item->contacts[0]->created_at];

            if (count($item->contacts) > 1) {
                $fpContacts = [];

                foreach ($item->contacts as $key => $contact) {
                    if ($key !== ($item->contacts->keys()->last())) {
                        $fpContacts[] = $contact->value . "\n";
                    } else {
                        $fpContacts[] = $contact->value;
                    }
                }

                $fpItem[] = implode("\n", $fpContacts);
            } else {
                $fpItem[] = $item->contacts[0]->value;
            }

            $lastSession = Session::where('domain_id', $item->domain_id)->where('user_id', $item->user_id)->first();

            $fpItem[] = implode("\n", [
                $lastSession->url,
                $lastSession->title,
                'ref=' . $lastSession->ref,
            ]);

            $domainId   = $lastSession->domain_id;
            $domainName = array_filter($domains, function ($val) use ($domainId) {
                return $val->id === $domainId;
            });
            $domainName = array_shift($domainName);
            $domainName = $domainName->name;

            $fpItem[] = $domainName;
            $fpItem[] = $item->user_id;

            fputcsv($fp, $fpItem, ';');
        }

        // Reset cursor
        rewind($fp);

        // Get content from stream and removes spaces at the start and end of a string
        $output = stream_get_contents($fp);
        fclose($fp);

        return response($output)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Encoding', 'utf-8')
            ->header('Content-disposition', 'attachment; filename="' . date('Y-m-d H:i:s') . '-phones.csv"');
    }

    public function adjustment(MainMicroserviceConnector $mainMicroserviceConnector, Request $request, $cabinet_id)
    {
        $domains = $mainMicroserviceConnector->getCabinetDomains($request->bearerToken(), (int) $cabinet_id);
        if (!$domains) {
            throw new Exception('domains dont exist', 422);
        }

        $domainsIds = array_map(function ($val) {
            return $val->id;
        }, $domains);

        $usersPost = $request->input('users');
        if (!$usersPost) {
            throw new Exception('Должны быть переданны контакты', 422);
        }

        $userIds = array_map(function ($val) {
            return $val['id'];
        }, $usersPost);

        if (!$userIds) {
            throw new Exception('Должны быть переданны контакты', 422);
        }

        foreach ($domainsIds as $domainId) {
            $countDeleted = UserDomain::where('domain_id', $domainId)
                ->whereIn('user_id', $userIds)->delete();

            $mainMicroserviceConnector->increaseDomainBalance($domainId, $countDeleted);
        }

        AdjustmentContacts::create(['data' => $request->getContent(), 'cabinet_id' => $cabinet_id]);

        Log::info('app.adjustment', ['request' => $request->all(), 'cabinet_id' => $cabinet_id]);

        return response()->json(['message' => 'ok']);
    }
}
