<?php

namespace AppBundle\Serializer\Handler;

use AppBundle\Entity\PlusEmployee;
use AppBundle\Repository\EmployeeRepository;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\Context;

/**
 * Class FakeTypePlusEmployeeDeserializationHandler
 */
class FakeTypePlusEmployeeDeserializationHandler
{
    /**
     * @var EmployeeRepository
     */
    private $repository;

    /**
     * @param EmployeeRepository $repository
     * @return FakeTypePlusEmployeeDeserializationHandler
     */
    public function setRepository(EmployeeRepository $repository): FakeTypePlusEmployeeDeserializationHandler
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * @param JsonDeserializationVisitor $visitor
     * @param array                      $parameters
     * @param array                      $type
     * @param Context                    $context
     *
     * @return null|object|PlusEmployee
     */
    public function deserializeByUserAndDepartment(JsonDeserializationVisitor $visitor, array $parameters, array $type, Context $context)
    {
        if (!isset($parameters['user']) || !isset($parameters['department'])) {
            throw new \RuntimeException("User and department should be set for deserializeByUserAndDepartment method");
        }

        return $this->repository->findOneBy(['user' => $parameters['user'], 'department' => $parameters['department']]);
    }

    /**
     * @param JsonDeserializationVisitor $visitor
     * @param int                        $id
     * @param array                      $type
     * @param Context                    $context
     *
     * @return null|object
     */
    public function deserializeById(JsonDeserializationVisitor $visitor, $id, array $type, Context $context)
    {
        return $this->repository->find($id);
    }
}
