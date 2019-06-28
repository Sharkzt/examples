<?php

namespace AppBundle\Tests\Services\Reaction;

use AppBundle\Entity\PlusKeepItUpReaction;
use AppBundle\Services\Reaction\KeepItUpProducer;
use PHPUnit\Framework\TestCase;

/**
 * Class KeepItUpProducerTest
 *
 * @group main
 */
class KeepItUpProducerTest extends TestCase
{
    /**
     * @var KeepItUpProducer
     */
    private $producer;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->setProducer(new KeepItUpProducer());
    }

    /**
     * @return void
     */
    public function testCreateReturnNewInspiringReaction(): void
    {
        $reaction = $this->getProducer()->create();

        $this->assertInstanceOf(PlusKeepItUpReaction::class, $reaction);
        $this->assertEquals(PlusKeepItUpReaction::SLUG, $reaction->getSlug());
    }

    /**
     * @return iterable
     */
    public function getSlugAndExpectedValue(): iterable
    {
        return [
            ['foo', false],
            ['1', false],
            [PlusKeepItUpReaction::SLUG, true],
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
     * @return KeepItUpProducer
     */
    protected function getProducer(): KeepItUpProducer
    {
        return $this->producer;
    }

    /**
     * @param KeepItUpProducer $producer
     *
     * @return KeepItUpProducerTest
     */
    protected function setProducer(KeepItUpProducer $producer): KeepItUpProducerTest
    {
        $this->producer = $producer;

        return $this;
    }
}
