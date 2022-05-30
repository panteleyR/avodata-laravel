<?php

namespace App\Contracts;

interface ProviderScriptBuilder
{
    public function setJsContainerAfter(string $script);
    public function setJsContainerBefore(string $script);
    public function setProvidersStart(bool $on);
    public function setToken(string $token);
    public function build(): string;
}
