<?php

namespace App\Repositories;

use App\Entities\AbstractEntity;

/** @template T of AbstractEntity */
interface RepositoryInterface
{
    /**
     * @return T[]
     */
    public function all(): array;

    /**
     * @param int $id
     * @return T|null
     */
    public function findById(int $id);

    /**
     * @param string $field
     * @param mixed $value
     * @return T|null
     */
    public function findByField(string $field, $value);

    /**
     * @param string $field
     * @param mixed $value
     * @return T|null
     */
    public function getByField(string $field, $value);

    /**
     * @param T $entity
     * @return T
     */
    public function save($entity);

    /**
     * @param T $entity
     * @return void
     */
    public function delete($entity): void;
}
