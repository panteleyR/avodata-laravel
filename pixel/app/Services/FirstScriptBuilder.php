<?php

namespace App\Services;

use App\Contracts\FirstScriptBuilder as ScriptBuilderContract;

class FirstScriptBuilder implements ScriptBuilderContract
{
    private string $urlData;

    public function setApiData(array $data)
    {
        $this->urlData = http_build_query($data);
    }

    private function mainScript()
    {
        $host = env('APP_URL') . '/px/pixel2.js';
        return '
            (function (d, w) {
                var n = d.getElementsByTagName("script")[0],
                        s = d.createElement("script"),
                        f = function () { n.parentNode.insertBefore(s, n); };
                        s.type = "text/javascript";
                        s.async = true;
                s.src = "'. $host .'" + "?domain="+window.location.origin+window.location.pathname+"&title="+encodeURIComponent(document.getElementsByTagName("title")[0]? document.getElementsByTagName("title")[0].innerHTML : "default")+"&url="+location.href+"&ref="+d.referrer+"&cookie="+encodeURIComponent(document.cookie)+"&'.$this->urlData.'";
                if (w.opera == "[object Opera]") {
                    d.addEventListener("DOMContentLoaded", f, false);
                } else { f(); }
            })(document, window);
        ';
    }

    public function build(): string
    {
        return $this->mainScript();
    }
}
