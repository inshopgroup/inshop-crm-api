<?php

namespace App\EventListener\Logger;

/**
 * Class EntityLogger
 * @package App\EventListener\Logger
 */
class EntityLogger
{
    protected $entity;

    /**
     * @var string
     */
    protected string $action;

    /**
     * @var array
     */
    protected array $changes = [];

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity): void
    {
        $this->entity = $entity;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    /**
     * @return array
     */
    public function getChanges(): array
    {
        return $this->changes;
    }

    /**
     * @param string $attributeName
     * @param string $value
     */
    public function addChange(string $attributeName, string $value): void
    {
        $this->changes[$attributeName] = $value;
    }
}
