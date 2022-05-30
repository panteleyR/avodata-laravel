<?php

namespace App\Contracts;

interface GeoGuard
{
    public function checkPosition(string $ip);
    public function setExpectationPositions(\stdClass $positions);
}
