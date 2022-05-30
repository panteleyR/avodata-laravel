<?php

namespace App\Services;

use App\Contracts\ProviderScriptBuilder as ScriptBuilderContract;

class ProviderScriptBuilder implements ScriptBuilderContract
{
    private string $jsContainerAfter = '';
    private string $jsContainerBefore = '';
    private bool $providersStart = true;
    private string $token;
    private array $providers;
    private int $seconds = 10;
    private int $session;

    public function setJsContainerBefore(string $script)
    {
        $this->jsContainerBefore = $script;
    }

    public function setJsContainerAfter(string $script)
    {
        $this->jsContainerAfter = $script;
    }

    private function jsContainerBefore(): ?string
    {
        return '
        (function() {
            ' . $this->jsContainerBefore . '
        })();
        ';
    }

    private function jsContainerAfter(): ?string
    {
        return '
        (function() {
            ' . $this->jsContainerAfter . '
        })();
        ';
    }

    public function setToken(string $token)
    {
        $this->token = $token;
    }

    public function setSession(int $id)
    {
        $this->session = $id;
    }

    public function setProvidersStart(bool $on) {
        $this->providersStart = $on;
    }

    public function setProviders(array $providers)
    {
//        @todo фильтровать по включенному значению
        $this->providers = $providers;
    }

    private function startProvidersScript()
    {
        $script = 'function() {
            let checkProvider = false;
        ';
        foreach($this->providers as $key => $provider) {
            if ($provider->name !== 'BASE' && $provider->on) {
                $providerCode = $provider->code;
                $providerCode = str_replace('{sid}',$this->session,$providerCode);
                $providerCode = str_replace('{pid}',$provider->id,$providerCode);
                $script = $script . '
                    setTimeout(function() {
                        if (checkProvider) {
                            return;
                        }
                        fetch("'.env('APP_URL').'/px/provider/run?provider_id='.$provider->id.'&session_id='.$this->session.'").then((res) => {
                            if(res.json().status !== "done") {
                                '.$providerCode.'
                            } else {
                                checkProvider = true;
                            }
                        });
                    }, 1000 * '.$key*$this->seconds.');
                ';
            }
        }
        return $script . '}';
    }

    private function setCookieScript()
    {
        return '
            function(document) {
                "use strict";
                let r, o, i, u, c, a;
                r = "'.env('PXL_COOKIE_NAME_VISIT').'";
                o = 1;
                i = "Mon, 01-Feb-2029 00:00:00 GMT";
                u = "/";
                document.cookie = r + "=" + escape(o) + (i ? "; expires=" + i : "") + (u ? "; path=" + u : "") + (c ? "; domain=" + c : "") + (a ? "; secure" : "");
            }';
    }

    private function mainScript()
    {
        $script = '
            window.ism_pxl = {
                "add_phone": function() {
                },
                "session_id": "'.$this->session.'",
                "start_providers": '.$this->startProvidersScript().',
                "set_cookie_visited": '.$this->setCookieScript().',
            };

            window.ism_pxl.set_cookie_visited(document);
            if('.($this->providersStart ? 1 : 0).') {
                window.ism_pxl.start_providers();
            }';

        return $script;
    }

    public function build(): string
    {
        return $this->jsContainerBefore() . $this->mainScript() . $this->jsContainerAfter();
    }
}
