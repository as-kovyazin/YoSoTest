<?php

namespace App\Repository;

use App\Entity\Promotion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Throwable;

/**
 * @extends ServiceEntityRepository<Promotion>
 *
 * @method Promotion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Promotion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Promotion[]    findAll()
 * @method Promotion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromotionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Promotion::class);
    }

    public function add(Promotion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Promotion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function isTickerExist(int $briefcaseId, string $ticker): bool
    {
        $res = $this->findAllByBriefcaseAndTickers($briefcaseId, $ticker);
        return count($res) > 0;
    }

    public function quantityOfTickers(int $briefcaseId, string $ticker): int
    {
        try {
            $res = $this->createQueryBuilder('p')
                ->select('SUM(p.quantity) as sum')
                ->andWhere("p.briefcase = :briefcaseId")
                ->setParameter('briefcaseId', $briefcaseId)
                ->andWhere('p.ticker = :ticker')
                ->setParameter('ticker', $ticker)
                ->addGroupBy('p.ticker')
                ->getQuery()
                ->getResult();
        } catch (Throwable $err) {
            return 0;
        }
        return $res[0]['sum'] ?? 0;
    }

    public function getFirstByTicker(int $briefcaseId, string $ticker): ?Promotion
    {
        $res = $this->findAllByBriefcaseAndTickers($briefcaseId, $ticker);
        return $res[0] ?? null;
    }

    /**
     * @return Promotion[]
     * */
    public function findAllByBriefcaseAndTickers(int $briefcaseId, string $ticker): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere("p.briefcase = :briefcaseId")
            ->setParameter('briefcaseId', $briefcaseId)
            ->andWhere('p.ticker = :ticker')
            ->setParameter('ticker', $ticker)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Promotion[]
     * */
    public function findAllByBriefcase(int $briefcaseId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere("p.briefcase = :briefcaseId")
            ->setParameter('briefcaseId', $briefcaseId)
            ->getQuery()
            ->getResult();
    }

    public function getSumByBriefcase(int $briefcaseId): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.ticker, SUM(p.quantity) as quantity')
            ->andWhere("p.briefcase = :briefcaseId")
            ->setParameter('briefcaseId', $briefcaseId)
            ->addGroupBy('p.ticker')
            ->getQuery()
            ->getResult();
    }

    public function startTransaction()
    {
        $this->_em->getConnection()->beginTransaction();
    }

    public function commitTransaction()
    {
        $this->_em->getConnection()->commit();
    }

    public function rollbackTransaction()
    {
        $this->_em->getConnection()->rollBack();
    }
}
