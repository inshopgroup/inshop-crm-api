<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use App\Interfaces\SearchInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use Gedmo\Mapping\Annotation as Gedmo;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * Company
 *
 * @ORM\Table(name="product_sell_price")
 * @ORM\Entity(repositoryClass="App\Repository\ProductSellPriceRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\Loggable
 * @ApiResource(attributes={
 *     "normalization_context"={"groups"={"product_sell_price_read", "read"}},
 *     "denormalization_context"={"groups"={"product_sell_price_write"}},
 *     "order"={"id": "DESC"}
 * },
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_PRODUCT_SELL_PRICE_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_PRODUCT_SELL_PRICE_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_PRODUCT_SELL_PRICE_SHOW')"
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_PRODUCT_SELL_PRICE_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_PRODUCT_SELL_PRICE_DELETE')"
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
     * @var integer
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
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="productSellPrices")
     * @Gedmo\Versioned
     * @Groups({
     *     "product_sell_price_read",
     *     "product_sell_price_write",
     *     "order_header_read"
     * })
     * @Assert\NotNull()
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Channel")
     * @Gedmo\Versioned
     * @Groups({
     *     "product_sell_price_read",
     *     "product_sell_price_write"
     * })
     * @Assert\NotNull()
     */
    private $channel;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Vat")
     * @Gedmo\Versioned
     * @Groups({
     *     "product_sell_price_read",
     *     "product_sell_price_write"
     * })
     * @Assert\NotBlank()
     */
    private $vat;

    /**
     * @ORM\Column(type="float", nullable=false)
     * @Gedmo\Versioned
     * @Groups({
     *     "product_sell_price_read",
     *     "product_sell_price_write"
     * })
     * @Assert\NotBlank()
     */
    private $priceSellBrutto;

    /**
     * @ORM\Column(type="float", nullable=false)
     * @Gedmo\Versioned
     * @Groups({
     *     "product_sell_price_read",
     *     "product_sell_price_write"
     * })
     * @Assert\NotBlank()
     */
    private $priceOldSellBrutto;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @Gedmo\Versioned
     * @Groups({
     *     "product_sell_price_read",
     *     "product_sell_price_write"
     * })
     * @Assert\NotBlank()
     */
    private $activeFrom;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @Gedmo\Versioned
     * @Groups({
     *     "product_sell_price_read",
     *     "product_sell_price_write"
     * })
     * @Assert\NotBlank()
     */
    private $activeTo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CompanyProduct")
     * @Gedmo\Versioned
     * @Groups({
     *     "product_sell_price_read",
     *     "product_sell_price_write"
     * })
     * @Assert\NotNull()
     */
    private $companyProduct;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActiveFrom(): ?\DateTimeInterface
    {
        return $this->activeFrom;
    }

    public function setActiveFrom(\DateTimeInterface $activeFrom): self
    {
        $this->activeFrom = $activeFrom;

        return $this;
    }

    public function getActiveTo(): ?\DateTimeInterface
    {
        return $this->activeTo;
    }

    public function setActiveTo(\DateTimeInterface $activeTo): self
    {
        $this->activeTo = $activeTo;

        return $this;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getChannel(): Channel
    {
        return $this->channel;
    }

    public function setChannel(Channel $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    public function getCompanyProduct(): CompanyProduct
    {
        return $this->companyProduct;
    }

    public function setCompanyProduct(CompanyProduct $companyProduct): self
    {
        $this->companyProduct = $companyProduct;

        return $this;
    }

    public function getVat(): Vat
    {
        return $this->vat;
    }

    public function setVat(Vat $vat): self
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
    public function getName()
    {
        return sprintf(
            '%s - %s %s',
            $this->getCompanyProduct()->getCompanyName(),
            $this->getPriceSellBrutto(),
            $this->getChannel()->getCurrency()->getCode()
        );
    }
}
