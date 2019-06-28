<?php

namespace AppBundle\Tests\Services\Reaction;

use AppBundle\Entity\PlusLikeReaction;
use AppBundle\Services\Reaction\ProduceInterface;
use AppBundle\Services\Reaction\ReactionManager;
use PHPUnit\Framework\TestCase;
use Mockery as m;

/**
 * Class ReactionManagerTest
 *
 * @group main
 */
class ReactionManagerTest extends TestCase
{
    /**
     * @var ProduceInterface|m\MockInterface
     */
    private $producer;

    /**
     * @var ProduceInterface[]|m\MockInterface[]
     */
    private $producers;

    /**
     * @var ReactionManager
     */
    private $manager;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this
            ->setProducer()
            ->setManager(new ReactionManager());
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testCreateBySlugWithValidSlugReturnNewObject(): void
    {
        $this->producer
            ->shouldReceive('isSupported')
            ->atLeast()
            ->with('foo')
            ->andReturnTrue();
        $this->setProducers($this->producer);

        $reflected = new \ReflectionClass($this->getManager());
        $property = $reflected->getProperty('producers');
        $property->setAccessible(true);
        $property->setValue($this->getManager(), $this->getProducers());

        $this->assertInstanceOf(PlusLikeReaction::class, $this->getManager()->createBySlug('foo'));
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testCreateBySlugWithNotValidSlugThrowsException(): void
    {
        $this->producer
            ->shouldReceive('isSupported')
            ->atLeast()
            ->with('foo')
            ->andReturnFalse();
        $this->setProducers($this->producer);

        $reflected = new \ReflectionClass($this->getManager());
        $property = $reflected->getProperty('producers');
        $property->setAccessible(true);
        $property->setValue($this->getManager(), $this->getProducers());

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Not able to create reaction object using foo as slug');
        $this->getManager()->createBySlug('foo');
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        m::close();
    }

    /**
     * @return ProduceInterface[]
     */
    protected function getProducers(): array
    {
        return $this->producers;
    }

    /**
     * @param ProduceInterface $producer
     *
     * @return ReactionManagerTest
     */
    protected function setProducers($producer): ReactionManagerTest
    {
        $this->producers = [$producer];

        return $this;
    }

    /**
     * @return ProduceInterface
     */
    protected function getProducer(): ProduceInterface
    {
        return $this->producer;
    }

    /**
     * @return ReactionManagerTest
     */
    protected function setProducer(): ReactionManagerTest
    {
        $this->producer = m::mock(ProduceInterface::class);
        $this->producer
            ->shouldReceive('create')
            ->atLeast()
            ->withNoArgs()
            ->andReturn(m::spy(PlusLikeReaction::class));

        return $this;
    }

    /**
     * @return ReactionManager
     */
    protected function getManager(): ReactionManager
    {
        return $this->manager;
    }

    /**
     * @param ReactionManager $manager
     *
     * @return ReactionManagerTest
     */
    protected function setManager(ReactionManager $manager): ReactionManagerTest
    {
        $this->manager = $manager;

        return $this;
    }
}
