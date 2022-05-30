<?php

namespace App\Contracts;

interface MainMicroserviceConnector
{
    public function getDomain(string $token): \stdClass;
    public function getProviders(?string $token): array;
    public function getCabinetDomains(string $token, int $cabinet_id): array;
    public function getDomains(string $token): array;
    public function reduceDomainBalance(int $domainId);
    public function increaseDomainBalance(int $domainId, int $count);
}

