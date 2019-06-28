<?php

namespace AppBundle\Tests\Services\Reaction;

use AppBundle\Entity\PlusWowReaction;
use AppBundle\Services\Reaction\WowProducer;
use PHPUnit\Framework\TestCase;

/**
 * Class WowProducerTest
 *
 * @group main
 */
class WowProducerTest extends TestCase
{
    /**
     * @var WowProducer
     */
    private $producer;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->setProducer(new WowProducer());
    }

    /**
     * @return void
     */
    public function testCreateReturnNewInspiringReaction(): void
    {
        $reaction = $this->getProducer()->create();

        $this->assertInstanceOf(PlusWowReaction::class, $reaction);
        $this->assertEquals(PlusWowReaction::SLUG, $reaction->getSlug());
    }

    /**
     * @return iterable
     */
    public function getSlugAndExpectedValue(): iterable
    {
        return [
            ['foo', false],
            ['1', false],
            [PlusWowReaction::SLUG, true],
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
     * @return WowProducer
     */
    protected function getProducer(): WowProducer
    {
        return $this->producer;
    }

    /**
     * @param WowProducer $producer
     *
     * @return WowProducerTest
     */
    protected function setProducer(WowProducer $producer): WowProducerTest
    {
        $this->producer = $producer;

        return $this;
    }
}
