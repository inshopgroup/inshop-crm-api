<?php

namespace App\EventListener\Logger;

class EntityLogger
{
    protected $entity;

    protected string $action;

    protected array $changes = [];

    public function getEntity()
    {
        return $this->entity;
    }

    public function setEntity($entity): void
    {
        $this->entity = $entity;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    public function getChanges(): array
    {
        return $this->changes;
    }

    public function addChange(string $attributeName, string $value): void
    {
        $this->changes[$attributeName] = $value;
    }
}
