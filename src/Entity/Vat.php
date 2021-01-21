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
 * Vat
 *
 * @ORM\Table(name="vat")
 * @ORM\Entity(repositoryClass="App\Repository\VatRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"vat_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"vat_write", "is_active_write"}},
 *          "order"={"id": "DESC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_VAT_LIST')"
 *          },
 *          "post"={
 *              "security"="is_granted('ROLE_VAT_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_VAT_SHOW')"
 *          },
 *          "put"={
 *              "security"="is_granted('ROLE_VAT_UPDATE')"
 *          },
 *          "delete"={
 *              "security"="is_granted('ROLE_VAT_DELETE')"
 *          }
 *     })
 * @ApiFilter(DateFilter::class, properties={"createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "name": "ipartial",
 *     "value": "exact",
 * })
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "name",
 *          "value",
 *          "createdAt",
 *          "updatedAt"
 *     }
 * )
 */
class Vat
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @Groups({
     *     "vat_read",
     *     "product_read",
     *     "product_write",
     *     "invoice_header_read",
     *     "invoice_header_write",
     *     "order_header_read",
     *     "order_header_write",
     *     "product_sell_price_read",
     *     "product_sell_price_write"
     * })
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({
     *     "vat_read",
     *     "vat_write",
     *     "product_read",
     *     "invoice_header_read",
     *     "order_header_read",
     *     "product_sell_price_read"
     * })
     * @Assert\NotBlank()
     */
    private string $name;

    /**
     * @ORM\Column(type="float")
     * @Groups({"vat_read", "vat_write"})
     * @Assert\NotBlank()
     */
    private float $value;

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

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }
}
