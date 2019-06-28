<?php

namespace AppBundle\Tests\Services\Reaction;

use AppBundle\Entity\PlusInspiringReaction;
use AppBundle\Services\Reaction\InspiringProducer;
use PHPUnit\Framework\TestCase;

/**
 * Class InspiringProducerTest
 *
 * @group main
 */
class InspiringProducerTest extends TestCase
{
    /**
     * @var InspiringProducer
     */
    private $producer;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->setProducer(new InspiringProducer());
    }

    /**
     * @return void
     */
    public function testCreateReturnNewInspiringReaction(): void
    {
        $reaction = $this->getProducer()->create();

        $this->assertInstanceOf(PlusInspiringReaction::class, $reaction);
        $this->assertEquals(PlusInspiringReaction::SLUG, $reaction->getSlug());
    }

    /**
     * @return iterable
     */
    public function getSlugAndExpectedValue(): iterable
    {
        return [
            ['foo', false],
            ['1', false],
            [PlusInspiringReaction::SLUG, true],
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
     * @return InspiringProducer
     */
    protected function getProducer(): InspiringProducer
    {
        return $this->producer;
    }

    /**
     * @param InspiringProducer $producer
     *
     * @return InspiringProducerTest
     */
    protected function setProducer(InspiringProducer $producer): InspiringProducerTest
    {
        $this->producer = $producer;

        return $this;
    }
}
