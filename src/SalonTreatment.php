<?php

namespace Lakuuu\SalonMiddleware;

use Illuminate\Support\Facades\Http;

class SalonTreatment
{
    private $url;

    public function __construct()
    {
        $this->url = env(
            'LAKUUU_SALON_TREATMENT',
            'https://staging-salon-api.lakuuu.click/api/v1/treatment-service'
        );
    }

    public function getAllTreatment($salonId)
    {
        return Http::withHeaders(['salon_id' => $salonId])->get($this->url.'/treatment');
    }
}
