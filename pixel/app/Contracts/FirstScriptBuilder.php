<?php

namespace App\Contracts;

interface FirstScriptBuilder
{
    public function setApiData(array $token);
    public function build(): string;
}
