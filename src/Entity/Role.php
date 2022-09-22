<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use App\Repository\GroupRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * Role
 *
 * @ORM\Table(name="role")
 * @ORM\Entity(repositoryClass="App\Repository\RoleRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"role_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"role_write", "is_active_write"}},
 *          "order"={"id": "ASC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_ROLE_LIST')"
 *          },
 *          "post"={
 *              "security"="is_granted('ROLE_ROLE_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_ROLE_SHOW')"
 *          },
 *          "put"={
 *              "security"="is_granted('ROLE_ROLE_UPDATE')"
 *          },
 *          "delete"={
 *              "security"="is_granted('ROLE_ROLE_DELETE')"
 *          }
 *     })
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "name",
 *          "createdAt",
 *          "updatedAt"
 *     }
 * )
 */
#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @Groups({
     *     "role_read",
     *     "group_read",
     *     "module_read"
     * })
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @Groups({
     *     "role_read",
     *     "role_write",
     *     "group_read",
     *     "module_read"
     * })
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $name;

    /**
     * @Groups({
     *     "role_read",
     *     "role_write",
     *     "group_read",
     *     "module_read"
     * })
     */
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\NotBlank]
    private string $role;

    /**
     * @Groups({
     *     "role_read",
     *     "role_write",
     *     "group_read"
     * })
     */
    #[ORM\ManyToOne(targetEntity: Module::class, inversedBy: 'roles')]
    #[Assert\NotBlank]
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
