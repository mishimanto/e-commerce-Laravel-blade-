<?php

namespace App\Services\Courier;

class CourierService
{
    protected $couriers = [
        'pathao' => PathaoService::class,
        'steadfast' => SteadfastService::class,
        'redx' => RedXService::class,
        'sundarban' => SundarbanService::class,
        'sa_poribohon' => SAPoribohonService::class,
    ];

    public function sendOrder($courierCode, $orderData)
    {
        if (!isset($this->couriers[$courierCode])) {
            throw new \Exception("Courier not found");
        }

        $courier = app($this->couriers[$courierCode]);
        return $courier->createShipment($orderData);
    }

    public function trackOrder($courierCode, $trackingId)
    {
        $courier = app($this->couriers[$courierCode]);
        return $courier->trackShipment($trackingId);
    }

    public function calculateRate($courierCode, $packageDetails)
    {
        $courier = app($this->couriers[$courierCode]);
        return $courier->calculateRate($packageDetails);
    }
}