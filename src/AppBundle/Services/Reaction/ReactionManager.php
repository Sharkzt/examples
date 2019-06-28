<?php

namespace AppBundle\Services\Reaction;

use AppBundle\Entity\PlusSpock;

/**
 * Class ReactionManager
 */
class ReactionManager
{
    /**
     * @var ProduceInterface[]
     */
    private $producers;

    /**
     * @return iterable|ProduceInterface[]
     */
    public function getProducers(): iterable
    {
        return $this->producers;
    }

    /**
     * @param iterable|ProduceInterface[] $producers
     *
     * @return ReactionManager
     */
    public function setProducers(iterable $producers): ReactionManager
    {
        $this->producers = $producers;

        return $this;
    }

    /**
     * @param string $slug
     *
     * @return PlusSpock
     */
    public function createBySlug(string $slug): PlusSpock
    {
        foreach ($this->getProducers() as $producer) {
            if ($producer->isSupported($slug)) {
                return $producer->create();
                break;
            }
        }

        throw new \LogicException(sprintf('Not able to create reaction object using %s as slug', $slug));
    }
}
