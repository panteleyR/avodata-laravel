<?php

namespace App\Http\Controllers;

use App\Contracts\Client;
use App\Contracts\GeoGuard;
use App\Contracts\MainMicroserviceConnector;
use App\Contracts\FirstScriptBuilder;
use App\Contracts\ProviderScriptBuilder;
use App\Models\ContactType;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Session;
use App\Models\ProviderRun;
use App\Models\Contact;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ScriptController extends Controller
{
    public function index(Request $request, FirstScriptBuilder $builder, Client $client)
    {
        try {
            $builder->setApiData($request->all());
            $this->robotFilter($request, $client->ip());

            return response($builder->build())->header('Content-Type', 'application/javascript');
        } catch (\Throwable $e) {
            return response('console.log("' . $e->getMessage() . '")')->header('Content-Type', 'application/javascript');
        }
    }

    private function robotFilter(Request $request, string $ip)
    {
        $remoteHost = gethostbyaddr($ip);
        if (strpos($remoteHost, 'spider.yandex.com') !== false
            || strpos($remoteHost, 'googlebot.com') !== false
            || strpos($remoteHost, 'rate-limited-proxy') !== false) {
            die("console.log('bye');");
        }

        $robotAgents = [
            'YandexBot', 'YandexAccessibilityBot', 'YandexMobileBot','YandexDirectDyn',
            'YandexScreenshotBot', 'YandexImages', 'YandexVideo', 'YandexVideoParser',
            'YandexMedia', 'YandexBlogs', 'YandexFavicons', 'YandexWebmaster',
            'YandexPagechecker', 'YandexImageResizer','YandexAdNet', 'YandexDirect',
            'YaDirectFetcher', 'YandexCalendar', 'YandexSitelinks', 'YandexMetrika',
            'YandexNews', 'YandexNewslinks', 'YandexCatalog', 'YandexAntivirus',
            'YandexMarket', 'YandexVertis', 'YandexForDomain', 'YandexSpravBot',
            'YandexSearchShop', 'YandexMedianaBot', 'YandexOntoDB', 'YandexOntoDBAPI',
            'Googlebot', 'Googlebot-Image', 'Mediapartners-Google', 'AdsBot-Google',
            'Mail.RU_Bot', 'bingbot', 'Accoona', 'ia_archiver', 'Ask Jeeves',
            'OmniExplorer_Bot', 'W3C_Validator', 'WebAlta', 'YahooFeedSeeker', 'Yahoo!',
            'Ezooms', 'Tourlentabot', 'MJ12bot', 'AhrefsBot', 'SearchBot', 'SiteStatus',
            'Nigma.ru', 'Baiduspider', 'Statsbot', 'SISTRIX', 'AcoonBot', 'findlinks',
            'proximic', 'OpenindexSpider','statdom.ru', 'Exabot', 'Spider', 'SeznamBot',
            'oBot', 'C-T bot', 'Updownerbot', 'Snoopy', 'heritrix', 'Yeti',
            'DomainVader', 'DCPbot', 'PaperLiBot', 'APIs-Google', 'Mediapartners-Google',
            'AdsBot-Google-Mobile', 'AdsBot-Google', 'Googlebot-Image', 'Googlebot',
            'Googlebot-News', 'Googlebot-Video', 'AdsBot-Google-Mobile-Apps', 'FeedFetcher-Google',
            'Google-Read-Aloud', 'DuplexWeb-Google', 'Google Favicon', 'googleweblight'
        ];

        $userAgent = $request->header('User-Agent');
        foreach($robotAgents as $robotAgent) {
            if (strpos($userAgent, $robotAgent) !== false) {
                die("console.log('bye');");
            }
        }
    }

    public function providers(Request $request, MainMicroserviceConnector $mainMicroservice, Client $client, ProviderScriptBuilder $builder, GeoGuard $geo)
    {
        try {
            $data = $request->all();
            $dataToApi = array_filter($data, function ($key) {
                return $key !== 'domain' && $key !== 'token' && $key !== 'title' && $key !== 'url' && $key !== 'ref' && $key !== 'cookie';
            }, ARRAY_FILTER_USE_KEY);
            $token = $data['token'];
            $domain = $mainMicroservice->getDomain($token);
            $providers = $mainMicroservice->getProviders($token);
            $baseProvider = array_filter($providers, function ($val) {
                return $val->on && $val->name === 'BASE';
            });
            if (isset($baseProvider[0])) {
                $baseProvider = $baseProvider[0];
            }

//          Находим трекнутого пользователя в системе, если его нет, то создаем нового
            $userId = $client->backCookie(env('PXL_COOKIE_NAME_USER'));
            if (!$userId) {
                $user = new User();
                $user->save();
                $userId = $user->id;
                $client->setCookie(env('PXL_COOKIE_NAME_USER'), $user->id);
            }

            if ($domain->js_before) {
                $builder->setJsContainerBefore($domain->js_before);
            }

            if ($domain->js_after) {
                $builder->setJsContainerAfter($domain->js_after);
            }

            if ($domain->geo_on && $domain->geo) {
                $geo->setExpectationPositions($domain->geo);
                $geo->checkPosition($client->ip());
            }

            $isVisited = $client->frontCookie(env('PXL_COOKIE_NAME_VISIT'));

            if ($isVisited) {
                throw new \Exception('old visit');
            }

//            $mainMicroservice->getBalance($userId);
            if (!$domain->current_campaign) {
                throw new \Exception('campaign is doesnt exist');
            }
            $domainBalance = $domain->current_campaign->balance;
            if ($domainBalance > 0) {

//              Создаем новый визит
                $session = Session::create($data + ['user_id' => $userId, 'domain_id' => $domain->id, 'api_data' => http_build_query($dataToApi)]);

//              Ищем в своей базе контакты
                $contacts = Contact::where('user_id', $userId)->get();
                if ($baseProvider && $contacts->isNotEmpty()) {
                    $providerRun = new ProviderRun();
                    $providerRun->provider_id = $baseProvider->id;
                    $providerRun->session_id = $session->id;
                    $providerRun->save();

//                  Привязываем пользователя(его контакты) к еще одному домену
                    $this->attachUserToDomain($mainMicroservice, (int) $userId, $domain->id);
                    $builder->setProvidersStart(false);

//                @todo перевести в ивент отправки постбека
                    if ($api_url = $domain->api_url) {
                        $this->postback($api_url, $contacts, $session->api_data);
                    }
                }
            } else {
                $builder->setProvidersStart(false);
            }

            $builder->setToken($token);

            $builder->setProviders($providers);
            $builder->setSession($session->id);

            return response($builder->build())->header('Content-Type', 'application/javascript');
        } catch (\Throwable $e) {
            return response('console.log("' . $e->getMessage() . '")')->header('Content-Type', 'application/javascript');
        }
    }

    public function providerRun(Request $request)
    {
        try {
            if (!$request->has('provider_id') || !$request->has('session_id')) {
                return response()->json(['status' => 'error']);
            }
            $user_id = (int) $request->cookie(env('PXL_COOKIE_NAME_USER'));
            $phone = Contact::where('user_id', $user_id)->first();
            if ($phone) {
                return response()->json(['status' => 'done']);
            }

            $provider = new ProviderRun();
            $provider->provider_id = $request->provider_id;
            $provider->session_id = $request->session_id;
            $provider->save();
            return response()->json(['status' => 'processing']);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error']);
        }
    }

    public function webhook(Request $request, Client $client, MainMicroserviceConnector $mainMicroservice)
    {
        $sid = $request->input('sid') ?: $request->input('yid');
        $pid = $request->input('pid');
        $token = $request->input('token') ?: $request->input('stock_key');

        $contacts = [['value' => $request->input('phone'), 'type' => 'phone']];

        $session = Session::find($sid);
        if (!$session) {
            throw new \Exception('Session doesn\'t exist');
        }
        $domain = $mainMicroservice->getDomain($session->token);

//        Получаем провайдера, который прислал нам данные
       $providers = $mainMicroservice->getProviders($session->token);
        if($pid) {
           $providers = array_filter($providers, function ($val) use ($pid) {
               return $val->on && $val->id === $pid;
           });
        } else if ($token) {
            $providers = array_filter($providers, function ($val) use ($token) {
                return $val->on && $val->token === $token;
            });
        } else {
            $providers = array_filter($providers, function ($val) use ($client) {
                return $val->on && $val->ip === $client->ip();
            });
        }

        if (!$providers) {
            throw new \Exception('Unknown provider');
        }

        $pid = $providers[array_key_first($providers)]->id;

        foreach ($contacts as $contact) {
//           Проверяем на уникальность
            $sameContact = Contact::where('user_id', $session->user_id)->where('value', $contact['value'])->first();
            if (!$sameContact) {
                 $contactData = [
                     'session_id' => $session->id,
                     'domain_id' => $session->domain_id,
                     'user_id' => $session->user_id,
                     'provider_id' => $pid,
                     'value' => $contact['value'],
                     'type' => $contact['type']
                 ];
                $this->createContact($contactData);
                $this->attachUserToDomain($mainMicroservice, $session->user_id, $session->domain_id);
            }
        }

//                @todo перевести в ивент отправки постбека
        if ($api_url = $domain->api_url) {
            $this->postback($api_url, $contacts, $session->api_data);
        }

        return response(['message' => 'ok']);
    }

    public function aggregate(Request $request, Client $client, MainMicroserviceConnector $mainMicroservice)
    {
        $this->robotFilter($client->ip());

        //          Находим трекнутого пользователя в системе, если его нет, то создаем нового
        $userId = $client->backCookie(env('PXL_COOKIE_NAME_USER'));
        if (!$userId) {
            $user = new User();
            $user->save();
            $userId = $user->id;
            $client->setCookie(env('PXL_COOKIE_NAME_USER'), $user->id);
        }

        $providers = $mainMicroservice->getProviders(null);
        $baseProvider = array_filter($providers, function ($val) {
            return $val->on && $val->name === 'BASE';
        });
        if (isset($baseProvider[0])) {
            $baseProvider = $baseProvider[0];
        }
        $pid = $baseProvider->id;

        $phone = $request->input('phone');
        if (!$phone) {
            throw new \Exception('unknown phone');
        }

        $session = Session::create(['user_id' => $userId]);

        $contactData = [
            'session_id' => $session->id,
//            'domain_id' => $session->domain_id,
            'user_id' => $userId,
            'provider_id' => $pid,
            'value' => $phone,
            'type' => 'phone'
        ];
        $this->createContact($contactData);

        return response("location.replace('".$request->input('redirect')."')")->header('Content-Type', 'application/javascript');
    }

    private function postback($api_url, $contacts, $sessionData)
    {
        $http = new \GuzzleHttp\Client();
        $output = [];
        parse_str($sessionData, $output);
        $http->post($api_url, [
                'form_params' => [
                    'contacts' => $contacts,
                    'session' => $output
                ]
            ]
        );
    }

    private function createContact(array $data)
    {
        $type = ContactType::where('name', $data['type'])->first();

        $contact = new Contact();
        if (!empty($data['session_id'])) {
            $contact->session_id = $data['session_id'];
        }
        if (!empty($data['domain_id'])) {
            $contact->domain_id = $data['domain_id'];
        }
        $contact->user_id = $data['user_id'];
        $contact->provider_id = $data['provider_id'];
        $contact->contacts_type_id = $type->id;
        $contact->value = $data['value'];
        $contact->save();
    }

    private function attachUserToDomain(MainMicroserviceConnector $mainMicroservice, int $userId, int $domainId)
    {
        $alreadyExist = DB::table('user_domain')
            ->select('user_id')
            ->where('user_id', '=', $userId)
            ->where('domain_id', '=', $domainId)
            ->first();
        if (!$alreadyExist) {
            $mainMicroservice->reduceDomainBalance($domainId);
            DB::table('user_domain')->insert([
                'user_id' => $userId,
                'domain_id' => $domainId,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
    }
}
