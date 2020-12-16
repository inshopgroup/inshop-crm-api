<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Trait Blameable
 * @package App\Traits
 */
trait Blameable
{
    /**
     * @var string
     * @Gedmo\Blameable(on="create")
     * @ORM\Column(nullable=true)
     * @Groups({"read"})
     */
    protected ?string $createdBy = null;

    /**
     * @var string|null
     * @Gedmo\Blameable(on="update")
     * @ORM\Column(nullable=true)
     * @Groups({"read"})
     */
    protected ?string $updatedBy = null;

    /**
     * Sets createdBy.
     *
     * @param string|null $createdBy
     * @return $this
     */
    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    /**
     * Sets updatedBy.
     *
     * @param string|null $updatedBy
     * @return $this
     */
    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Returns updatedBy.
     *
     * @return string|null
     */
    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }
}
