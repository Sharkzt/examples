<?php

namespace AppBundle\Tests\Services\Reaction;

use AppBundle\Entity\PlusThanksReaction;
use AppBundle\Services\Reaction\ThanksProducer;
use PHPUnit\Framework\TestCase;

/**
 * Class ThanksProducerTest
 *
 * @group main
 */
class ThanksProducerTest extends TestCase
{
    /**
     * @var ThanksProducer
     */
    private $producer;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->setProducer(new ThanksProducer());
    }

    /**
     * @return void
     */
    public function testCreateReturnNewInspiringReaction(): void
    {
        $reaction = $this->getProducer()->create();

        $this->assertInstanceOf(PlusThanksReaction::class, $reaction);
        $this->assertEquals(PlusThanksReaction::SLUG, $reaction->getSlug());
    }

    /**
     * @return iterable
     */
    public function getSlugAndExpectedValue(): iterable
    {
        return [
            ['foo', false],
            ['1', false],
            [PlusThanksReaction::SLUG, true],
        ];
    }

    /**
     * @dataProvider getSlugAndExpectedValue
     *
     * @param string $slug
     * @param bool   $value
     *
     * @return void
     */
    public function testIsSupportedWithStringReturnBoolean(string $slug, bool $value): void
    {
        $this->assertEquals($value, $this->getProducer()->isSupported($slug));
    }

    /**
     * @return ThanksProducer
     */
    protected function getProducer(): ThanksProducer
    {
        return $this->producer;
    }

    /**
     * @param ThanksProducer $producer
     *
     * @return ThanksProducerTest
     */
    protected function setProducer(ThanksProducer $producer): ThanksProducerTest
    {
        $this->producer = $producer;

        return $this;
    }
}
