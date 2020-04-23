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
 * ProjectType
 *
 * @ORM\Entity(repositoryClass="App\Repository\ProjectTypeRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"project_type_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"project_type_write", "is_active_write"}},
 *          "order"={"id": "ASC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_PROJECT_TYPE_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_PROJECT_TYPE_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_PROJECT_TYPE_SHOW')"
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_PROJECT_TYPE_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_PROJECT_TYPE_DELETE')"
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
class ProjectType
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"project_type_read", "project_read", "project_write", "client_read", "client_write"})
     */
    private ?int $id = null;

    /**
     * @var integer
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Groups({"project_type_read", "project_type_write", "project_read", "client_read"})
     * @Assert\NotBlank()
     */
    private int $name;

    public function __sleep()
    {
        return [];
    }

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
     *
     * @return ProjectType
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }
}
