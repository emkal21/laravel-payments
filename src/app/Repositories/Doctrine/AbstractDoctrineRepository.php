<?php

namespace App\Repositories\Doctrine;

use App\Entities\AbstractEntity;
use App\Repositories\RepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;

/**
 * @template T of AbstractEntity
 * @implements RepositoryInterface<T>
 */
abstract class AbstractDoctrineRepository implements RepositoryInterface
{
    protected $entityManager;
    protected $repository;

    public function __construct(ManagerRegistry $registry)
    {
        /** @var class-string<T> $entityClass */
        $entityClass = $this->getEntityClass();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $registry->getManagerForClass($entityClass);

        /** @var ObjectRepository $repository */
        $repository = $entityManager->getRepository($entityClass);

        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        $queryBuilder = $this->getQueryBuilderForEntity();

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $id
     * @return T|null
     */
    public function findById(int $id)
    {
        return $this->findByField('id', $id);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return T|null
     */
    public function findByField(string $field, $value)
    {
        $queryBuilder = $this
            ->getQueryBuilderForEntity()
            ->andWhere('entity.' . $field . ' = :value')
            ->setMaxResults(1)
            ->setParameter('value', $value);

        $results = $queryBuilder
            ->getQuery()
            ->getResult();

        if (count($results) === 0) {
            return null;
        }

        return $results[0];
    }

    /**
     * @param T $entity
     * @return T
     */
    public function save($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    /**
     * @param T $entity
     * @return void
     */
    public function delete($entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /**
     * @return QueryBuilder
     */
    private function getQueryBuilderForEntity(): QueryBuilder
    {
        return $this->repository->createQueryBuilder('entity');
    }

    /**
     * @return class-string<T>
     */
    abstract protected function getEntityClass(): string;
}
