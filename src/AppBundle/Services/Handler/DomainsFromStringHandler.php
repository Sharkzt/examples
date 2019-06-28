<?php

namespace AppBundle\Services\Handler;

/**
 * Class DomainsFromStringHandler
 */
class DomainsFromStringHandler
{
    /**
     * @var iterable
     */
    private $domains = [];

    /**
     * Method do not perform emails validation
     *
     * @param string $domains
     *
     * @return DomainsFromStringHandler
     */
    public function parse(string $domains): DomainsFromStringHandler
    {
        $this->setDomains(preg_split('/\,+/', trim($domains)));

        return $this;
    }

    /**'
     * @param iterable|array $domains
     *
     * @return DomainsFromStringHandler
     */
    public function setDomains(iterable $domains): DomainsFromStringHandler
    {
        $this->domains = $domains;

        return $this;
    }

    /**
     * @param string $domain
     *
     * @return DomainsFromStringHandler
     */
    public function addDomain(string $domain): DomainsFromStringHandler
    {
        $this->domains[] = $domain;

        return $this;
    }

    /**
     * @return iterable
     */
    public function getDomains(): iterable
    {
        if (0 === sizeof($this->domains)) {
            throw new \LogicException(sprintf("No emails parsed from input string"));
        }

        return $this->domains;
    }
}
