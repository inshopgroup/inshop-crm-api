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
 * InvoiceType
 *
 * @ORM\Table(name="invoice_type")
 * @ORM\Entity(repositoryClass="App\Repository\InvoiceTypeRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"invoice_type_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"invoice_type_write", "is_active_write"}},
 *          "order"={"id": "ASC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_INVOICE_TYPE_LIST')"
 *          },
 *          "post"={
 *              "security"="is_granted('ROLE_INVOICE_TYPE_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_INVOICE_TYPE_SHOW')"
 *          },
 *          "put"={
 *              "security"="is_granted('ROLE_INVOICE_TYPE_UPDATE')"
 *          },
 *          "delete"={
 *              "security"="is_granted('ROLE_INVOICE_TYPE_DELETE')"
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
class InvoiceType
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @Groups({
     *     "invoice_type_read",
     *     "invoice_header_read",
     *     "invoice_header_write",
     *     "invoice_header_read_collection"
     * })
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({
     *     "invoice_type_read",
     *     "invoice_type_write",
     *     "invoice_header_read",
     *     "invoice_header_read_collection"
     * })
     * @Assert\NotBlank()
     */
    private string $name;

    public function getId(): ?int
    {
        return $this->id;
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
}
