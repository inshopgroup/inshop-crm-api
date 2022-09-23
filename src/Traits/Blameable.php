<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

trait Blameable
{
    /**
     * @Gedmo\Blameable(on="create")
     */
    #[ORM\Column(nullable: true)]
    #[Groups([
        "read",
    ])]
    protected ?string $createdBy = null;

    /**
     * @Gedmo\Blameable(on="update")
     */
    #[ORM\Column(nullable: true)]
    #[Groups([
        "read",
    ])]
    protected ?string $updatedBy = null;

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }
}
