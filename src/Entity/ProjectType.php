<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use App\Repository\ProjectTypeRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"project_type_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"project_type_write", "is_active_write"}},
 *          "order"={"id": "ASC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_PROJECT_TYPE_LIST')"
 *          },
 *          "post"={
 *              "security"="is_granted('ROLE_PROJECT_TYPE_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_PROJECT_TYPE_SHOW')"
 *          },
 *          "put"={
 *              "security"="is_granted('ROLE_PROJECT_TYPE_UPDATE')"
 *          },
 *          "delete"={
 *              "security"="is_granted('ROLE_PROJECT_TYPE_DELETE')"
 *          }
 *     })
 * @ApiFilter(DateFilter::class, properties={"createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "name": "ipartial",
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
#[ORM\Entity(repositoryClass: ProjectTypeRepository::class)]
class ProjectType
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @Groups({"project_type_read", "project_read", "project_write", "client_read", "client_write"})
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @Groups({"project_type_read", "project_type_write", "project_read", "client_read"})
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
