<?php

namespace AppBundle\Tests\Services\Reaction;

use AppBundle\Entity\PlusLikeReaction;
use AppBundle\Services\Reaction\LikeProducer;
use PHPUnit\Framework\TestCase;

/**
 * Class LikeProducerTest
 *
 * @group main
 */
class LikeProducerTest extends TestCase
{
    /**
     * @var LikeProducer
     */
    private $producer;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->setProducer(new LikeProducer());
    }

    /**
     * @return void
     */
    public function testCreateReturnNewInspiringReaction(): void
    {
        $reaction = $this->getProducer()->create();

        $this->assertInstanceOf(PlusLikeReaction::class, $reaction);
        $this->assertEquals(PlusLikeReaction::SLUG, $reaction->getSlug());
    }

    /**
     * @return iterable
     */
    public function getSlugAndExpectedValue(): iterable
    {
        return [
            ['foo', false],
            ['1', false],
            [PlusLikeReaction::SLUG, true],
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
     * @return LikeProducer
     */
    protected function getProducer(): LikeProducer
    {
        return $this->producer;
    }

    /**
     * @param LikeProducer $producer
     *
     * @return LikeProducerTest
     */
    protected function setProducer(LikeProducer $producer): LikeProducerTest
    {
        $this->producer = $producer;

        return $this;
    }
}
