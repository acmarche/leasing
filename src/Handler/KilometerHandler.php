<?php

namespace AcMarche\Leasing\Handler;

use AcMarche\Leasing\Entity\KilometerDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class KilometerHandler
{
    public function __construct(
        #[Autowire(env: 'BICYCLE_ALLOWANCE')]
        private readonly string $allowance,
    ) {
    }

    /**
     * Calculate bicycle mileage allowance.
     * @return array Returns an array with monthly and yearly compensation.
     */
    function  calculateBicycleAllowance(KilometerDto $kilometerDto

    ): array {
        $monthlyCompensation = $this->allowance * $kilometerDto->number_kilometers * $kilometerDto->number_trips;
        $yearlyCompensation = $monthlyCompensation * $kilometerDto->number_months;

        return [
            'monthly' => round($monthlyCompensation, 2),
            'yearly' => round($yearlyCompensation, 2),
        ];
    }


}