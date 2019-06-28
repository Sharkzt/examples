<?php

namespace AppBundle\Services\Handler;

use AppBundle\Entity\PlusAllowedDomain;
use AppBundle\Services\App\AbstractManager;
use Doctrine\ORM\ORMException;

/**
 * Class AllowedDomainsHandler
 */
class AllowedDomainsHandler extends AbstractManager
{
    /**
     * @var DomainsFromStringHandler
     */
    private $domainsFromString;

    /**
     * @return DomainsFromStringHandler
     */
    public function getDomainsFromString(): DomainsFromStringHandler
    {
        return $this->domainsFromString;
    }

    /**
     * @param DomainsFromStringHandler $domainsFromString
     *
     * @return AllowedDomainsHandler
     */
    public function setDomainsFromString(DomainsFromStringHandler $domainsFromString): AllowedDomainsHandler
    {
        $this->domainsFromString = $domainsFromString;

        return $this;
    }

    /**
     * @param string $domains
     *
     * @return AllowedDomainsHandler
     *
     * @throws ORMException
     */
    public function handle(string $domains): AllowedDomainsHandler
    {
        $domains = $this->getDomainsFromString()
            ->parse($domains)
            ->getDomains()
        ;

        foreach ($domains as $domain) {
            if (!$this->getEntityManager()->getRepository(PlusAllowedDomain::class)->findOneBy(['domain' => $domain])) {
                $domain = (new PlusAllowedDomain())
                    ->setDomain(trim($domain));
                $this->getEntityManager()->persist($domain);
            }
        }
        $this->getEntityManager()->flush();

        return $this;
    }
}
