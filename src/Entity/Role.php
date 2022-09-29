<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Repository\RoleRepository;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    collectionOperations: [
        'get' => ['security' => "is_granted('ROLE_ROLE_LIST')"],
        'post' => ['security' => "is_granted('ROLE_ROLE_CREATE')"],
    ],
    itemOperations: [
        'get' => ['security' => "is_granted('ROLE_ROLE_SHOW')"],
        'put' => ['security' => "is_granted('ROLE_ROLE_UPDATE')"],
        'delete' => ['security' => "is_granted('ROLE_ROLE_DELETE')"],
    ],
    attributes: [
        'order' => ['id' => "DESC"],
        'normalization_context' => ['groups' => ["role_read", "read", "is_active_read"]],
        'denormalization_context' => ['groups' => ["role_write", "is_active_write"]],
    ]
)]
#[ApiFilter(
    DateFilter::class,
    properties: [
        "createdAt",
        "updatedAt",
    ]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        "id",
        "name",
        "createdAt",
        "updatedAt"
    ]
)]
#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role
{
    use Timestampable;
    use Blameable;
    use IsActive;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        "role_read",
        "group_read",
        "module_read",
    ])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups([
        "role_read",
        "role_write",
        "group_read",
        "module_read",
    ])]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Groups([
        "role_read",
        "role_write",
        "group_read",
        "module_read",
    ])]
    private string $role;

    #[ORM\ManyToOne(targetEntity: Module::class, inversedBy: 'roles')]
    #[Assert\NotBlank]
    #[Groups([
        "role_read",
        "role_write",
        "group_read",
    ])]
    private ?Module $module = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): self
    {
        $this->module = $module;

        return $this;
    }
}
