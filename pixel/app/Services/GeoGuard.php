<?php

namespace App\Services;

use GeoIp2\Database\Reader;
use App\Contracts\GeoGuard as GeoGuardContract;

class GeoGuard implements GeoGuardContract
{
    private Reader $geo2;
    private \stdClass $positions;

    public function __construct()
    {
//        $this->geo2 = new Reader('../resources/GeoLite2-City.mmdb');
    }

    public function setExpectationPositions(\stdClass $positions)
    {
        $this->positions = $positions;
    }

    public function checkPosition(string $ip)
    {
        $geoInfos = $this->geo2->city($ip);
        $city = $geoInfos->city->name;
        foreach ($this->positions as $position) {
            if ($city === $position->name) {
                return;
            }
        }

        throw new \Exception('oh no, we can\'t find your city');
    }

}
