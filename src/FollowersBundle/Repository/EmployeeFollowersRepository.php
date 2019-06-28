<?php

namespace FollowersBundle\Repository;

use AppBundle\Entity\PlusEmployeeFollower;
use AppBundle\Entity\PlusEmployee;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * Class EmployeeFollowersRepository
 */
class EmployeeFollowersRepository extends EntityRepository
{
    /**
     * @param PlusEmployee $employee
     *
     * @deprecated
     *
     * @return int
     */
    public function getFollowersCount(PlusEmployee $employee): int
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('COUNT(f.id)')
            ->from(PlusEmployeeFollower::class, 'f')
            ->where('f.employee = :employee')
            ->setParameters([
                'employee' => $employee,
            ]);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param PlusEmployee $employee
     * @param int          $batch
     *
     * @deprecated
     *
     * @return PlusEmployee[]|null
     */
    public function getFollowersList(PlusEmployee $employee, int $batch)
    {
        $qbFollowers = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('e, u')
            ->from(PlusEmployee::class, 'e')
            ->innerJoin('e.employeeAsFollowers', 'fl')
            ->innerJoin('e.user', 'u')
            ->where('fl.employee = :employee')
            ->setParameters([
                'employee' => $employee,
            ])
            ->setMaxResults($batch * 10);

        return $qbFollowers->getQuery()->getResult();
    }

    /**
     * @param PlusEmployee $employee
     * @param bool         $isMonthly
     *
     * @deprecated
     *
     * @return int
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function getFollowersNumberByEmployeeAndDateRange(PlusEmployee $employee, bool $isMonthly = false): int
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('COUNT(f.id)')
            ->from(PlusEmployeeFollower::class, 'f')
            ->where('f.employee = :employee')
            ->setParameter('employee', $employee);

        if ($isMonthly) {
            $qb
                ->andWhere('f.createdAt >= :dateFrom')
                ->andWhere('f.createdAt <= :dateTo')
                ->setParameter('dateFrom', (new \DateTime())->sub(new \DateInterval('P1M')))
                ->setParameter('dateTo', new \DateTime());
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
