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
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * Contact
 *
 * @ORM\Table(name="label")
 * @ORM\Entity(repositoryClass="App\Repository\LabelRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"label_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"label_write", "is_active_write"}},
 *          "order"={"id": "DESC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_LABEL_LIST')"
 *          },
 *          "post"={
 *              "security"="is_granted('ROLE_LABEL_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_LABEL_SHOW')"
 *          },
 *          "put"={
 *              "security"="is_granted('ROLE_LABEL_UPDATE')"
 *          },
 *          "delete"={
 *              "security"="is_granted('ROLE_LABEL_DELETE')"
 *          }
 *     })
 * @ApiFilter(DateFilter::class, properties={"createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "name": "ipartial",
 *     "country.name": "ipartial",
 * })
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "name",
 *          "country.name",
 *          "createdAt",
 *          "updatedAt"
 *     }
 * )
 */
class Label
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({
     *     "label_read",
     *     "product_read",
     *     "company_read",
     *     "company_write",
     *     "company_read_collection",
     *     "client_read",
     *     "client_read_collection",
     *     "client_write",
     * })
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Groups({
     *     "label_read",
     *     "label_write",
     *     "product_read",
     *     "company_read",
     *     "company_read_collection",
     *     "client_read",
     *     "client_read_collection",
     * })
     * @Assert\NotBlank()
     */
    private string $name;

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
}
