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
 * Currency
 *
 * @ORM\Table(name="currency")
 * @ORM\Entity(repositoryClass="App\Repository\CurrencyRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"currency_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"currency_write", "is_active_write"}},
 *          "order"={"id": "DESC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_CURRENCY_LIST')"
 *          },
 *          "post"={
 *              "security"="is_granted('ROLE_CURRENCY_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_CURRENCY_SHOW')"
 *          },
 *          "put"={
 *              "security"="is_granted('ROLE_CURRENCY_UPDATE')"
 *          },
 *          "delete"={
 *              "security"="is_granted('ROLE_CURRENCY_DELETE')"
 *          }
 *     })
 * @ApiFilter(DateFilter::class, properties={"createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "name": "ipartial",
 *     "code": "ipartial",
 * })
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "name",
 *          "code",
 *          "createdAt",
 *          "updatedAt"
 *     }
 * )
 */
class Currency
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({
     *     "currency_read",
     *     "order_header_read",
     *     "invoice_header_read",
     *     "invoice_header_write",
     *     "order_header_read_collection",
     *     "order_header_write",
     *     "company_product_read",
     *     "company_product_write",
     *     "product_sell_price_read",
     *     "order_header_read",
     *     "order_header_read_collection",
     *     "channel_read",
     *     "channel_write"
     * })
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Groups({
     *     "currency_read",
     *     "currency_write",
     *     "order_header_read",
     *     "invoice_header_read",
     *     "order_header_read_collection",
     *     "company_product_read",
     *     "product_sell_price_read",
     *     "order_header_read",
     *     "order_header_read_collection",
     *     "channel_read"
     * })
     * @Assert\NotBlank()
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Groups({
     *     "currency_read",
     *     "currency_write"
     * })
     * @Assert\NotBlank()
     */
    private string $code;

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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
