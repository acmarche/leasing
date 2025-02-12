<?php

namespace AcMarche\Leasing\Repository;

use AcMarche\Leasing\Entity\LeasingData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LeasingData|null   find($id, $lockMode = null, $lockVersion = null)
 * @method LeasingData|null   findOneBy(array $criteria, array $orderBy = null)
 * @method LeasingData[]|null findAll()
 * @method LeasingData[]      findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LeasingRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, LeasingData::class);
    }

    public function findByUsername(string $userIdentifier): ?LeasingData
    {
        return $this
            ->createQueryBuilder('leasing_data')
            ->andWhere('leasing_data.username = :username')
            ->setParameter('username', $userIdentifier)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Use MapEntity url
     * @param string $uuid
     * @return LeasingData|null
     */
    public function findOneByUuid(string $uuid): ?LeasingData
    {
        return $this
            ->createQueryBuilder('leasing')
            ->andWhere('leasing.uuid = :uuid')
            ->setParameter('uuid', $uuid, ParameterType::STRING)
            ->getQuery()
            ->getOneOrNullResult();
    }

}