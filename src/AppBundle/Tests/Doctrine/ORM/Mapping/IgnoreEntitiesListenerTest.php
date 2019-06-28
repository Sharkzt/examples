<?php

namespace AppBundle\Tests\Doctrine\ORM\Mapping;

use AppBundle\Listener\IgnoreEntitiesListener;
use AppBundle\TestHelpers\Model\ModelIgnoredBySchemaGenerator;
use AppBundle\TestHelpers\Model\ModelUsedBySchemaGenerator;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\DBAL\Schema\Schema;
use Mockery as m;

class IgnoreEntitiesListenerTest extends TestCase
{

    /**
     * @var EntityManager|m\MockInterface
     */
    private $em;

    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    /**
     * @var IgnoreEntitiesListener
     */
    private $listener;

    public function setUp()
    {
        $this->em = m::mock(EntityManager::class);

        $this->annotationReader = new AnnotationReader();

        $this->listener = new IgnoreEntitiesListener();
        $this->listener->setAnnotationReader($this->annotationReader);
    }

    public function testPostGenerateSchemaWithAnnotations()
    {
        $this->em->shouldReceive('getMetadataFactory->getAllMetadata')
            ->andReturn([
                m::mock(ClassMetadata::class)
                    ->shouldReceive('getReflectionClass')
                    ->andReturn(new \ReflectionClass(ModelIgnoredBySchemaGenerator::class))
                    ->getMock()
                    ->shouldReceive('getTableName')
                    ->andReturn('ignored_table')
                    ->getMock(),
                m::mock(ClassMetadata::class)
                    ->shouldReceive('getReflectionClass')
                    ->andReturn(new \ReflectionClass(ModelUsedBySchemaGenerator::class))
                    ->getMock()
                    ->shouldReceive('getTableName')
                    ->andReturn('used_table')
                    ->getMock()
            ]);

        /**
         * @var Schema|m\MockInterface $schema
         */
        $schema = m::mock(Schema::class);
        $schema->shouldReceive('dropTable')
            ->once()
            ->with('ignored_table')
            ->andReturnTrue();

        /**
         * @var GenerateSchemaEventArgs|m\MockInterface $args
         */
        $args = m::mock(new GenerateSchemaEventArgs($this->em, $schema));

        $this->assertTrue($this->listener->postGenerateSchema($args));
    }

    public function tearDown()
    {
        m::close();
    }

}
