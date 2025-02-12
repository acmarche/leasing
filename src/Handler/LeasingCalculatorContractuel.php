<?php

namespace AcMarche\Leasing\Handler;

use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AsTaggedItem(index: 'contractuel')]
#[AutoconfigureTag('app.leasing_calculator')]
class LeasingCalculatorContractuel implements LeasingCalculatorInterface
{
    use CalculatorCommonTrait;

    /**
     * Disponible annuel de l'Allocation de Fin d'Année
     * =((F11+(C7*(C11/100)+C9*12)*0.025)*(C13/38))*1.2899
     * @return void
     */
    public function annualYearEndAllowanceAvailable(): void
    {
        $F11 = $this->leasingData->fixedAmountAFA;
        $C7 = $this->leasingData->grossAnnualFull;
        $C11 = $this->leasingData->indexValue;
        $C9 = $this->leasingData->monthlyHouseholdResidency;
        $C13 = $this->leasingData->workRegime;
        $this->leasingData->annualYearEndAllowanceAvailable =
            (($F11 + ($C7 * ($C11 / 100) + $C9 * 12) * 0.025) * ($C13 / 38)) * 1.2899;
    }

    /**
     * Disponible AFA
     * =F17
     * @return void
     */
    public function availableAFA(): void
    {
        $this->leasingData->availableAFA = $this->leasingData->annualYearEndAllowanceAvailable;
    }

    /**
     * Apport personnel nécessaire
     * =MAX((F2+500)*I3+MAX((F2+500)*H3+MAX(((F2+500)*G3+F3)-F9,0)-F9,0)-F9,0)
     */
    public function personalContribution(): void
    {
        $F2 = $this->leasingData->leasingValue;
        $F3 = $this->leasingData->accessoryValue;
        $F9 = $this->leasingData->availableAFA;
        $G3 = $this->indicesByYears[1];
        $H3 = $this->indicesByYears[2];
        $I3 = $this->indicesByYears[3];

        $this->leasingData->personalContribution = max(
            (($F2 + 500) * $I3 + max(
                    (($F2 + 500) * $H3 + max(
                            (($F2 + 500) * $G3 + $F3) - $F9,
                            0,
                        )) - $F9,
                    0,
                )) - $F9,
            0,
        );
    }

    /**
     * Valeur d'achat du leasing (Vélo et accessoires)
     * =F2+F3
     */
    public function leasingTotalAmount(): void
    {
        $this->leasingData->leasingTotalAmount = $this->leasingData->leasingValue + $this->leasingData->accessoryValue;;
    }

    /**
     * Pourcentage précompte
     * =IF(F7<10115,0%,IF(F7<12930,23.22%,IF(F7<16460,25.23%,IF(F7<19740,30.28%,IF(F7<22330,35.33%,IF(F7<24940,38.36%,IF(F7<30150,40.38%,IF(F7<32800,43.41%,IF(F7<43440,46.44%,IF(F7<56730,51.48%,53.5%))))))))))
     */
    public function prepaymentPercentage(): void
    {
        $F7 = $this->leasingData->taxableAnnualAFA;

        if ($F7 < 10115) {
            $result = 0;
        } elseif ($F7 < 12930) {
            $result = 23.22;
        } elseif ($F7 < 16460) {
            $result = 25.23;
        } elseif ($F7 < 19740) {
            $result = 30.28;
        } elseif ($F7 < 22330) {
            $result = 35.33;
        } elseif ($F7 < 24940) {
            $result = 38.36;
        } elseif ($F7 < 30150) {
            $result = 40.38;
        } elseif ($F7 < 32800) {
            $result = 43.41;
        } elseif ($F7 < 43440) {
            $result = 46.44;
        } elseif ($F7 < 56730) {
            $result = 51.48;
        } else {
            $result = 53.5;
        }

        $this->leasingData->prepaymentPercentage = $result;
    }

    /**
     * Estimation du net Allocation de Fin d'Année complète
     * =((F11+(C7*(C11/100)+C9*12)*0.025)*(C13/38))*(1-0.1307)*(1-F13)
     */
    public function estimateNetFullYearEndAllowance(): void
    {
        $F11 = $this->leasingData->fixedAmountAFA;
        $C7 = $this->leasingData->grossAnnualFull;
        $C11 = $this->leasingData->indexValue;
        $C9 = $this->leasingData->monthlyHouseholdResidency;
        $C13 = $this->leasingData->workRegime;
        $F13 = $this->leasingData->prepaymentPercentage / 100;

        $this->leasingData->estimateNetFullYearEndAllowance = ((($F11 + ($C7 * ($C11 / 100) + $C9 * 12) * 0.025) * ($C13 / 38)) * (1 - 0.1307) * (1 - $F13));
    }

    /**
     * Leasing année 1
     * =MIN(((F2+500)*G3+F3),F9)
     */
    public function leasingByYear1(): void
    {
        $F2 = $this->leasingData->leasingValue;
        $F3 = $this->leasingData->accessoryValue;
        $F9 = $this->leasingData->availableAFA;
        $G3 = $this->indicesByYears[1];

        $this->leasingData->leasingByYear1 = min((($F2 + 500) * $G3 + $F3), $F9);
    }

    /**
     * Leasing année 2
     * =MIN((F2+500)*H3+MAX(((F2+500)*G3+F3)-F9,0),F9)
     */
    public function leasingByYear2(): void
    {
        $F2 = $this->leasingData->leasingValue;
        $F3 = $this->leasingData->accessoryValue;
        $F9 = $this->leasingData->availableAFA;
        $G3 = $this->indicesByYears[1];
        $H3 = $this->indicesByYears[2];

        $this->leasingData->leasingByYear2 = min((($F2 + 500) * $H3 + max((($F2 + 500) * $G3 + $F3) - $F9, 0)), $F9);
    }

    /**
     * Leasing année 3
     * =MIN((F2+500)*I3+MAX((F2+500)*H3+MAX(((F2+500)*G3+F3)-F9,0)-F9,0),F9)
     */
    public function leasingByYear3(): void
    {
        $F2 = $this->leasingData->leasingValue;
        $F3 = $this->leasingData->accessoryValue;
        $F9 = $this->leasingData->availableAFA;
        $G3 = $this->indicesByYears[1];
        $H3 = $this->indicesByYears[2];
        $I3 = $this->indicesByYears[3];

        $this->leasingData->leasingByYear3 = min(
            (($F2 + 500) * $I3 + max(
                    (($F2 + 500) * $H3 + max(
                            (($F2 + 500) * $G3 + $F3) - $F9,
                            0,
                        )) - $F9,
                    0,
                )),
            $F9,
        );
    }

    /**
     * Contribution de l'agent en net année 1
     * =F19-(((F11+(C7*(C11/100)+C9*12)*0.025)*(C13/38))-I5/1.2899)*(1-0.1307)*(1-F13)
     */
    public function agentNetContributionYear1(): void
    {
        $F11 = $this->leasingData->fixedAmountAFA;
        $C7 = $this->leasingData->grossAnnualFull;
        $C11 = $this->leasingData->indexValue;
        $C9 = $this->leasingData->monthlyHouseholdResidency;
        $C13 = $this->leasingData->workRegime;
        $F13 = $this->leasingData->prepaymentPercentage / 100;
        $F19 = $this->leasingData->estimateNetFullYearEndAllowance;
        $I5 = $this->leasingData->leasingByYear1;

        $this->leasingData->agentNetContributionYear1 = $F19 - ((($F11 + ($C7 * ($C11 / 100) + $C9 * 12) * 0.025) * ($C13 / 38)) - ($I5 / 1.2899)) * (1 - 0.1307) * (1 - $F13);
    }

    /**
     * Contribution de l'agent en net année 2
     * =F19-(((F11+(C7*(C11/100)+C9*12)*0.025)*(C13/38))-I6/1.2899)*(1-0.1307)*(1-F13)
     */
    public function agentNetContributionYear2(): void
    {
        $F11 = $this->leasingData->fixedAmountAFA;
        $C7 = $this->leasingData->grossAnnualFull;
        $C11 = $this->leasingData->indexValue;
        $C9 = $this->leasingData->monthlyHouseholdResidency;
        $C13 = $this->leasingData->workRegime;
        $F13 = $this->leasingData->prepaymentPercentage / 100;
        $F19 = $this->leasingData->estimateNetFullYearEndAllowance;
        $I6 = $this->leasingData->leasingByYear2;

        $this->leasingData->agentNetContributionYear2 = $F19 - ((($F11 + ($C7 * ($C11 / 100) + $C9 * 12) * 0.025) * ($C13 / 38)) - ($I6 / 1.2899)) * (1 - 0.1307) * (1 - $F13);
    }

    /**
     * Contribution de l'agent en net année 3
     * =F19-(((F11+(C7*(C11/100)+C9*12)*0.025)*(C13/38))-I7/1.2899)*(1-0.1307)*(1-F13)
     */
    public function agentNetContributionYear3(): void
    {
        $F11 = $this->leasingData->fixedAmountAFA;
        $C7 = $this->leasingData->grossAnnualFull;
        $C11 = $this->leasingData->indexValue;
        $C9 = $this->leasingData->monthlyHouseholdResidency;
        $C13 = $this->leasingData->workRegime;
        $F13 = $this->leasingData->prepaymentPercentage / 100;
        $F19 = $this->leasingData->estimateNetFullYearEndAllowance;
        $I7 = $this->leasingData->leasingByYear3;

        $this->leasingData->agentNetContributionYear3 = $F19 - ((($F11 + ($C7 * ($C11 / 100) + $C9 * 12) * 0.025) * ($C13 / 38)) - ($I7 / 1.2899)) * (1 - 0.1307) * (1 - $F13);
    }

    /**
     * Valeur d'achat résiduel en dernière année (15,01% du Vélo)
     * =F2*0.1501
     */
    public function residualPurchaseValueFinalYear(): void
    {
        $this->leasingData->residualPurchaseValueFinalYear = $this->leasingData->leasingValue * 0.1501;
    }

    /**
     * Coût du leasing pris en charge par l'agent
     * =SUM(F23:F26)
     */
    public function leasingCostCoveredByAgent(): void
    {
        $this->leasingData->leasingCostCoveredByAgent = $this->leasingData->agentNetContributionYear1 + $this->leasingData->agentNetContributionYear2 + $this->leasingData->agentNetContributionYear3 + $this->leasingData->residualPurchaseValueFinalYear;
    }

    /**
     * Estimation net du solde de l'Allocation de Fin d'année, année 1
     * =(((F11+(C7*(C11/100)+C9*12)*0.025)*(C13/38))-I5/1.2899)*(1-0.1307)*(1-F13)
     */
    public function estimateNetYearEndAllowanceBalanceYear1(): void
    {
        $F11 = $this->leasingData->fixedAmountAFA;
        $C7 = $this->leasingData->grossAnnualFull;
        $C11 = $this->leasingData->indexValue;
        $C9 = $this->leasingData->monthlyHouseholdResidency;
        $C13 = $this->leasingData->workRegime;
        $F13 = $this->leasingData->prepaymentPercentage / 100;
        $I5 = $this->leasingData->leasingByYear1;

        $this->leasingData->estimateNetYearEndAllowanceBalanceYear1 = ((($F11 + ($C7 * ($C11 / 100) + $C9 * 12) * 0.025) * ($C13 / 38)) - ($I5 / 1.2899)) * (1 - 0.1307) * (1 - $F13);
    }

    /**
     * Estimation net du solde de l'Allocation de Fin d'année, année 2
     * =(((F11+(C7*(C11/100)+C9*12)*0.025)*(C13/38))-I6/1.2899)*(1-0.1307)*(1-F13)
     */
    public function estimateNetYearEndAllowanceBalanceYear2(): void
    {
        $F11 = $this->leasingData->fixedAmountAFA;
        $C7 = $this->leasingData->grossAnnualFull;
        $C11 = $this->leasingData->indexValue;
        $C9 = $this->leasingData->monthlyHouseholdResidency;
        $C13 = $this->leasingData->workRegime;
        $F13 = $this->leasingData->prepaymentPercentage / 100;
        $I6 = $this->leasingData->leasingByYear2;

        $this->leasingData->estimateNetYearEndAllowanceBalanceYear2 = ((($F11 + ($C7 * ($C11 / 100) + $C9 * 12) * 0.025) * ($C13 / 38)) - ($I6 / 1.2899)) * (1 - 0.1307) * (1 - $F13);
    }

    /**
     * Estimation net du solde de l'Allocation de Fin d'année, année 3
     * =(((F11+(C7*(C11/100)+C9*12)*0.025)*(C13/38))-I7/1.2899)*(1-0.1307)*(1-F13)
     */
    public function estimateNetYearEndAllowanceBalanceYear3(): void
    {
        $F11 = $this->leasingData->fixedAmountAFA;
        $C7 = $this->leasingData->grossAnnualFull;
        $C11 = $this->leasingData->indexValue;
        $C9 = $this->leasingData->monthlyHouseholdResidency;
        $C13 = $this->leasingData->workRegime;
        $F13 = $this->leasingData->prepaymentPercentage / 100;
        $I7 = $this->leasingData->leasingByYear3;

        $this->leasingData->estimateNetYearEndAllowanceBalanceYear3 = ((($F11 + ($C7 * ($C11 / 100) + $C9 * 12) * 0.025) * ($C13 / 38)) - ($I7 / 1.2899)) * (1 - 0.1307) * (1 - $F13);
    }

}