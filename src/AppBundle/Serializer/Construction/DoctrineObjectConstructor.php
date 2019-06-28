<?php

namespace AppBundle\Serializer\Construction;

use Doctrine\Common\Persistence\ManagerRegistry;
use JMS\Serializer\Construction\DoctrineObjectConstructor as JMSDoctrineObjectConstructor;
use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\VisitorInterface;
use JMS\Serializer\DeserializationContext;

/**
 * @deprecated
 */
class DoctrineObjectConstructor extends JMSDoctrineObjectConstructor
{

    /**
     * @var string
     */
    private $fallbackStrategy;

    private $managerRegistry;
    private $fallbackConstructor;

    public function __construct(ManagerRegistry $managerRegistry, ObjectConstructorInterface $fallbackConstructor, $fallbackStrategy = self::ON_MISSING_NULL)
    {
        $this->managerRegistry = $managerRegistry;
        $this->fallbackConstructor = $fallbackConstructor;
        $this->fallbackStrategy = $fallbackStrategy;
        parent::__construct($managerRegistry, $fallbackConstructor, $fallbackStrategy);
    }

    /**
     * {@inheritdoc}
     */
    public function construct(VisitorInterface $visitor, ClassMetadata $metadata, $data, array $type, DeserializationContext $context)
    {
        $objectManager = $this->managerRegistry->getManagerForClass($metadata->name);

        if ($objectManager) {
            $classMetadata = $objectManager->getClassMetadata($metadata->name);
            foreach ($classMetadata->getIdentifierFieldNames() as $name) {
                if (!isset($metadata->propertyMetadata[$name])) {
                    $metadata->propertyMetadata[$name] = null;
                }
            }
        }

        return parent::construct($visitor, $metadata, $data, $type, $context);
    }

}