<?php

namespace App\Console\Commands;

use App\Models\Domain;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class UpdateDomainScriptState extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:update_script_state';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $client;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $domains = Domain::all();

        foreach ($domains as $domain) {
            $scriptState = $this->getScriptStateByDomain($domain->domain);
            $domain->script_state = $scriptState;
            $domain->save();
        }

        return 0;
    }

    public function getScriptStateByDomain(string $domain)
    {
        try {
            $result = $this->client->get($domain);
            $html = $result->getBody()->getContents();
            $need = env('APP_URL');
    //        $need = 'https://avodata.io';
            $need .= '/px/pixel.js?token=';
            $need = addcslashes($need, '/?');
            $isSetScript = preg_match('/"' . $need . '[0-9a-z]{4,90}"/', $html);

            if ($isSetScript === false || $isSetScript === 0) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
