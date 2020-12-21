<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
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
class Role
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({
     *     "role_read",
     *     "group_read",
     *     "module_read"
     * })
     */
    private ?int $id = null;


    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({
     *     "role_read",
     *     "role_write",
     *     "group_read",
     *     "module_read"
     * })
     * @Assert\NotBlank()
     */
    private string $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({
     *     "role_read",
     *     "role_write",
     *     "group_read",
     *     "module_read"
     * })
     * @Assert\NotBlank()
     */
    private string $role;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Module", inversedBy="roles")
     * @Groups({
     *     "role_read",
     *     "role_write",
     *     "group_read"
     * })
     * @Assert\NotBlank()
     */
    private ?Module $module = null;

    public function __sleep()
    {
        return [];
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRole(): ?string
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
