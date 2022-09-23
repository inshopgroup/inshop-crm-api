<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait IsActive
{
    #[ORM\Column(type: 'boolean')]
    #[Groups([
        "is_active_read",
        "is_active_write",
    ])]
    protected bool $isActive = true;

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}
