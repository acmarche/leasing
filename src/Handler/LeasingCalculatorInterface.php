<?php

namespace AcMarche\Leasing\Handler;

use AcMarche\Leasing\Entity\LeasingData;

interface LeasingCalculatorInterface
{
    /**
     * @param LeasingData $data
     * @return void
     * @throws \Exception
     */
    public function calculate(LeasingData $data): void;

    /**
     * Forfais AFA
     * @return void
     */
    public function fixedAmountAFA(): void;

    /**
     * Disponible annuel de l'Allocation de Fin d'Année
     * @return void
     */
    public function annualYearEndAllowanceAvailable(): void;

    /**
     * Disponible AFA
     * =F17
     * @return void
     */
    public function availableAFA(): void;

    /**
     * Apport personnel nécessaire
     */
    public function personalContribution(): void;

    /**
     * Valeur d'achat du leasing (Vélo et accessoires)
     */
    public function leasingTotalAmount(): void;

    /**
     * Pourcentage précompte
     */
    public function prepaymentPercentage(): void;

    /**
     * Estimation du net Allocation de Fin d'Année complète
     */
    public function estimateNetFullYearEndAllowance(): void;

    /**
     * Leasing année 1
     */
    public function leasingByYear1(): void;

    /**
     * Leasing année 2
     */
    public function leasingByYear2(): void;

    /**
     * Leasing année 3
     */
    public function leasingByYear3(): void;

    /**
     * Contribution de l'agent en net année 1
     */
    public function agentNetContributionYear1(): void;

    /**
     * Contribution de l'agent en net année 2
     */
    public function agentNetContributionYear2(): void;

    /**
     * Contribution de l'agent en net année 3
     */
    public function agentNetContributionYear3(): void;

    /**
     * Valeur d'achat résiduel en dernière année (15,01% du Vélo)
     */
    public function residualPurchaseValueFinalYear(): void;

    /**
     * Coût du leasing pris en charge par l'agent
     */
    public function leasingCostCoveredByAgent(): void;

    /**
     * Estimation net du solde de l'Allocation de Fin d'année, année 1
     */
    public function estimateNetYearEndAllowanceBalanceYear1(): void;

    /**
     * Estimation net du solde de l'Allocation de Fin d'année, année 2
     */
    public function estimateNetYearEndAllowanceBalanceYear2(): void;

    /**
     * Estimation net du solde de l'Allocation de Fin d'année, année 3
     */
    public function estimateNetYearEndAllowanceBalanceYear3(): void;
}