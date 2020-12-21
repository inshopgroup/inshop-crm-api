<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * Company
 *
 * @ORM\Table(name="product_sell_price")
 * @ORM\Entity(repositoryClass="App\Repository\ProductSellPriceRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"product_sell_price_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"product_sell_price_write", "is_active_write"}},
 *          "order"={"id": "DESC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_PRODUCT_SELL_PRICE_LIST')"
 *          },
 *          "post"={
 *              "security"="is_granted('ROLE_PRODUCT_SELL_PRICE_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_PRODUCT_SELL_PRICE_SHOW')"
 *          },
 *          "put"={
 *              "security"="is_granted('ROLE_PRODUCT_SELL_PRICE_UPDATE')"
 *          },
 *          "delete"={
 *              "security"="is_granted('ROLE_PRODUCT_SELL_PRICE_DELETE')"
 *          }
 *     })
 * @ApiFilter(DateFilter::class, properties={"createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 * })
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "createdAt",
 *          "updatedAt"
 *     }
 * )
 */
class ProductSellPrice
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
     *     "product_sell_price_read",
     *     "order_header_read",
     *     "order_header_write"
     * })
     */
    private ?int $id = null;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="productSellPrices")
     * @Groups({
     *     "product_sell_price_read",
     *     "product_sell_price_write",
     *     "order_header_read"
     * })
     * @Assert\NotNull()
     */
    private ?Product $product = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Channel")
     * @Groups({
     *     "product_sell_price_read",
     *     "product_sell_price_write"
     * })
     * @Assert\NotNull()
     */
    private ?Channel $channel = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Vat")
     * @Groups({
     *     "product_sell_price_read",
     *     "product_sell_price_write"
     * })
     * @Assert\NotBlank()
     */
    private ?Vat $vat = null;

    /**
     * @ORM\Column(type="float", nullable=false)
     * @Groups({
     *     "product_sell_price_read",
     *     "product_sell_price_write"
     * })
     * @Assert\NotBlank()
     */
    private float $priceSellBrutto;

    /**
     * @ORM\Column(type="float", nullable=false)
     * @Groups({
     *     "product_sell_price_read",
     *     "product_sell_price_write"
     * })
     * @Assert\NotBlank()
     */
    private float $priceOldSellBrutto;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @Groups({
     *     "product_sell_price_read",
     *     "product_sell_price_write"
     * })
     * @Assert\NotBlank()
     */
    private DateTimeInterface $activeFrom;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @Groups({
     *     "product_sell_price_read",
     *     "product_sell_price_write"
     * })
     * @Assert\NotBlank()
     */
    private DateTimeInterface $activeTo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CompanyProduct")
     * @Groups({
     *     "product_sell_price_read",
     *     "product_sell_price_write"
     * })
     * @Assert\NotNull()
     */
    private ?CompanyProduct $companyProduct = null;

    public function __sleep()
    {
        return [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActiveFrom(): ?DateTimeInterface
    {
        return $this->activeFrom;
    }

    public function setActiveFrom(DateTimeInterface $activeFrom): self
    {
        $this->activeFrom = $activeFrom;

        return $this;
    }

    public function getActiveTo(): ?DateTimeInterface
    {
        return $this->activeTo;
    }

    public function setActiveTo(DateTimeInterface $activeTo): self
    {
        $this->activeTo = $activeTo;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getChannel(): ?Channel
    {
        return $this->channel;
    }

    public function setChannel(?Channel $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    public function getCompanyProduct(): ?CompanyProduct
    {
        return $this->companyProduct;
    }

    public function setCompanyProduct(?CompanyProduct $companyProduct): self
    {
        $this->companyProduct = $companyProduct;

        return $this;
    }

    public function getVat(): ?Vat
    {
        return $this->vat;
    }

    public function setVat(?Vat $vat): self
    {
        $this->vat = $vat;

        return $this;
    }

    public function getPriceSellBrutto(): ?float
    {
        return $this->priceSellBrutto;
    }

    public function setPriceSellBrutto(float $priceSellBrutto): self
    {
        $this->priceSellBrutto = $priceSellBrutto;

        return $this;
    }

    public function getPriceOldSellBrutto(): ?float
    {
        return $this->priceOldSellBrutto;
    }

    public function setPriceOldSellBrutto(float $priceOldSellBrutto): self
    {
        $this->priceOldSellBrutto = $priceOldSellBrutto;

        return $this;
    }

    /**
     * @Groups({
     *     "product_sell_price_read",
     *     "order_header_read"
     * })
     */
    public function getName(): ?string
    {
        return sprintf(
            '%s - %s %s',
            $this->getCompanyProduct() ?  $this->getCompanyProduct()->getCompanyName() : null,
            $this->getPriceSellBrutto(),
            $this->getChannel() ? $this->getChannel()->getCurrency()->getCode() : null
        );
    }
}
