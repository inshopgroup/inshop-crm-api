<?php

namespace App\Entity;


use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * Group
 *
 * @ORM\Table(name="`group`")
 * @ORM\Entity(repositoryClass="App\Repository\GroupRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"group_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"group_write", "is_active_write"}},
 *          "order"={"id": "ASC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_GROUP_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_GROUP_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_GROUP_SHOW')"
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_GROUP_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_GROUP_DELETE')"
 *          }
 *     })
 * @ApiFilter(DateFilter::class, properties={"createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "name": "ipartial"
 * })
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
class Group
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"group_read", "user_read", "user_write"})
     */
    private ?int $id = null;
/**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"group_read", "group_write", "user_read"})
     * @Assert\NotBlank()
     */
    private string $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Role")
     * @Groups({"group_read", "group_write"})
     */
    private Collection $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function __sleep()
    {
        return [];
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection|Role[]
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getRolesArray(): ?array
    {
        $roles = [];

        /** @var Role $role */
        foreach ($this->getRoles() as $role) {
            $roles[] = $role->getRole();
        }

        return $roles;
    }
}
