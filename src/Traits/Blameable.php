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
    protected $createdBy;

    /**
     * @var string
     * @Gedmo\Blameable(on="update")
     * @ORM\Column(nullable=true)
     * @Groups({"read"})
     */
    protected $updatedBy;

    /**
     * Sets createdBy.
     *
     * @param  string $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Returns createdBy.
     *
     * @return string
     */
    public function getCreatedBy(): string
    {
        return $this->createdBy;
    }

    /**
     * Sets updatedBy.
     *
     * @param  string $updatedBy
     * @return $this
     */
    public function setUpdatedBy($updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Returns updatedBy.
     *
     * @return string
     */
    public function getUpdatedBy(): string
    {
        return $this->updatedBy;
    }
}
