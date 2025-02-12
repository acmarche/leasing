<?php

namespace AcMarche\Leasing\Entity;

use AcMarche\Leasing\Repository\LeasingRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Stringable;

#[ORM\Table(name: 'leasing')]
#[ORM\Entity(repositoryClass: LeasingRepository::class)]
class LeasingData implements Stringable, TimestampableInterface
{
    use TimestampableTrait, UuidTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    public ?int $id = null;

    #[ORM\Column(nullable: false)]
    public string $statut = '';

    /**
     * Valeur du leasing
     */
    #[ORM\Column(precision: 4, scale: 4, nullable: false)]
    public float $leasingValue = 0;

    /**
     * Valeur des accessoires
     */
    #[ORM\Column(precision: 4, scale: 4, nullable: false)]
    public float $accessoryValue = 0;

    /**
     * Brut annuel à 100%
     */
    #[ORM\Column(precision: 4, scale: 4, nullable: false)]
    public float $grossAnnualFull = 0;

    /**
     * Foyer-Résid. mensuelle
     */
    #[ORM\Column(precision: 4, scale: 4, nullable: false)]
    public float $monthlyHouseholdResidency = 0;

    /**
     * Index
     */
    #[ORM\Column(precision: 4, scale: 4, nullable: false)]
    public float $indexValue = 0;

    /**
     * Régime de travail
     */
    #[ORM\Column(precision: 4, scale: 4, nullable: false)]
    public float $workRegime = 0;

    /**
     * Imposable annuel AFA
     */
    #[ORM\Column(precision: 4, scale: 4, nullable: false)]
    public float $taxableAnnualAFA = 0;

    public bool $calculated = false;

    public function __construct($leasingValue, $accessoryValue)
    {
        $this->leasingValue = $leasingValue;
        $this->accessoryValue = $accessoryValue;
    }

    public function __toString()
    {
        return 'Leasing de x';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Valeur totale du Leasing
     */
    public float $leasingTotalAmount = 0;

    /**
     * Forfais AFA
     */
    public float $fixedAmountAFA = 0;

    /**
     * Disponible AFA
     */
    public float $availableAFA = 0;

    /**
     * Pourcentage précompte
     */
    public float $prepaymentPercentage = 0;

    /**
     * Disponible annuel de l'Allocation de Fin d'Année
     */
    public float $annualYearEndAllowanceAvailable = 0;

    /**
     *  Estimation du net Allocation de Fin d'Année complète
     */
    public float $estimateNetFullYearEndAllowance = 0;

    /**
     * Contribution de l'agent en net année 1
     */
    public float $agentNetContributionYear1 = 0;

    /**
     * Contribution de l'agent en net année 2
     */
    public float $agentNetContributionYear2 = 0;

    /**
     * Contribution de l'agent en net année 3
     */
    public float $agentNetContributionYear3 = 0;

    /**
     * Valeur d'achat résiduel en dernière année (15,01% du Vélo)
     */
    public float $residualPurchaseValueFinalYear = 0;

    /**
     * Coût du leasing pris en charge par l'agent
     */
    public float $leasingCostCoveredByAgent = 0;

    /**
     *  Estimation net du solde de l'Allocation de Fin d'année, année 1
     */
    public float $estimateNetYearEndAllowanceBalanceYear1 = 0;

    /**
     *  Estimation net du solde de l'Allocation de Fin d'année, année 2
     */
    public float $estimateNetYearEndAllowanceBalanceYear2 = 0;

    /**
     *  Estimation net du solde de l'Allocation de Fin d'année, année 3
     */
    public float $estimateNetYearEndAllowanceBalanceYear3 = 0;

    /**
     * Leasing année 1 :
     */
    public float $leasingByYear1 = 0;

    /**
     * Leasing année 2 :
     */
    public float $leasingByYear2 = 0;

    /**
     * Leasing année 3 :
     */
    public float $leasingByYear3 = 0;

    /**
     *
     */
    public float $personalContribution = 0;
}