<?php

namespace AcMarche\Leasing\Handler;

use AcMarche\Leasing\Entity\LeasingData;

trait CalculatorCommonTrait {

    const float FIXED_AMOUNT_AFA = 470.2018;

    private array $indicesByYears = [1 => 0.5, 0.35, 0.12];

    public LeasingData $leasingData;

    public function calculate(LeasingData $data): void
    {
        $this->leasingData = $data;
        if (!$this->canBeCalculated()) {
            return;
        }
        $this->leasingTotalAmount();
        $this->prepaymentPercentage();
        $this->fixedAmountAFA();
        $this->annualYearEndAllowanceAvailable();
        $this->availableAFA();
        $this->personalContribution();
        $this->estimateNetFullYearEndAllowance();
        $this->prepaymentPercentage();
        $this->leasingByYear1();
        $this->leasingByYear2();
        $this->leasingByYear3();
        $this->agentNetContributionYear1();
        $this->agentNetContributionYear2();
        $this->agentNetContributionYear3();
        $this->residualPurchaseValueFinalYear();
        $this->leasingCostCoveredByAgent();
        $this->estimateNetYearEndAllowanceBalanceYear1();
        $this->estimateNetYearEndAllowanceBalanceYear2();
        $this->estimateNetYearEndAllowanceBalanceYear3();
    }

    /**
     * Forfais AFA
     * @return void
     */
    public function fixedAmountAFA(): void
    {
        $this->leasingData->fixedAmountAFA = self::FIXED_AMOUNT_AFA;
    }

    private function canBeCalculated(): bool
    {
        return $this->leasingData->leasingValue > 0 &&
            $this->leasingData->grossAnnualFull > 0 &&
            $this->leasingData->indexValue > 0 &&
            $this->leasingData->workRegime > 0 &&
            $this->leasingData->taxableAnnualAFA > 0;
    }

}