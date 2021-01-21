<?php

namespace App\Entity;

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
 * ProjectStatus
 *
 * @ORM\Entity(repositoryClass="App\Repository\ProjectStatusRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"project_status_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"project_status_write", "is_active_write"}},
 *          "order"={"id": "ASC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_PROJECT_STATUS_LIST')"
 *          },
 *          "post"={
 *              "security"="is_granted('ROLE_PROJECT_STATUS_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_PROJECT_STATUS_SHOW')"
 *          },
 *          "put"={
 *              "security"="is_granted('ROLE_PROJECT_STATUS_UPDATE')"
 *          },
 *          "delete"={
 *              "security"="is_granted('ROLE_PROJECT_STATUS_DELETE')"
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
class ProjectStatus
{
    /**
     * @var int
     */
    public const STATUS_OPEN = 1;
    /**
     * @var int
     */
    public const STATUS_IN_PROGRESS = 2;
    /**
     * @var int
     */
    public const STATUS_CLOSED = 3;

    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @Groups({"project_status_read", "project_read", "document_read", "project_write", "client_read", "client_write"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"project_status_read", "project_status_write", "project_read", "document_read", "client_read"})
     * @Assert\NotBlank()
     */
    private string $name;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     */
    public function getName(): string
    {
        return $this->name;
    }
}
