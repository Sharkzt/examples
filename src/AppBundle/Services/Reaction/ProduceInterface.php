<?php

namespace AppBundle\Services\Reaction;
use AppBundle\Entity\PlusSpock;

/**
 * Interface ProduceInterface
 */
interface ProduceInterface
{
    /**
     * @return PlusSpock
     */
    public function create(): PlusSpock;

    /**
     * @param string $slug
     *
     * @return bool
     */
    public function isSupported(string $slug): bool;
}
