<?php

namespace App\Entity;

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

/**
 * OrderLine
 *
 * @ORM\Table(name="order_line")
 * @ORM\Entity(repositoryClass="App\Repository\OrderLineRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\Loggable
 * @ApiResource(attributes={
 *     "normalization_context"={"groups"={"order_line_read", "read"}},
 *     "denormalization_context"={"groups"={"order_line_write"}},
 *     "order"={"id": "DESC"}
 * },
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_ORDER_LINE_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_ORDER_LINE_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_ORDER_LINE_SHOW')"
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_ORDER_LINE_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_ORDER_LINE_DELETE')"
 *          }
 *     })
 */
class OrderLine
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
     * @Groups({
     *     "order_line_read",
     *     "order_header_read",
     *     "order_header_write"
     * })
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\OrderHeader", inversedBy="lines")
     * @Gedmo\Versioned
     * @Groups({
     *     "order_line_write"
     * })
     * @Assert\NotNull()
     */
    private $header;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\OrderLineStatus")
     * @Gedmo\Versioned
     * @Groups({
     *     "order_line_read",
     *     "order_line_write",
     *     "order_header_read",
     *     "order_header_write"
     * })
     * @Assert\NotNull()
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductSellPrice")
     * @Gedmo\Versioned
     * @Groups({
     *     "order_line_read",
     *     "order_line_write",
     *     "order_header_read",
     *     "order_header_write"
     * })
     * @Assert\NotNull()
     */
    private $productSellPrice;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\StockLine", inversedBy="orderLine")
     * @Gedmo\Versioned
     * @Groups({
     *     "order_line_read",
     *     "order_line_write",
     *     "order_header_read",
     *     "order_header_write"
     * })
     */
    private $stockLine;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PurchaseOrderLine", mappedBy="orderLine")
     * @Groups({
     *     "order_line_read",
     *     "order_line_write",
     *     "order_header_read",
     *     "order_header_write"
     * })
     */
    private $purchaseOrderLines;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Gedmo\Versioned
     * @Groups({
     *     "order_line_read",
     *     "order_line_write",
     *     "order_header_read",
     *     "order_header_write"
     * })
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="float", nullable=false)
     * @Gedmo\Versioned
     * @Groups({
     *     "order_line_read",
     *     "order_line_write",
     *     "order_header_read",
     *     "order_header_write"
     * })
     * @Assert\NotBlank()
     */
    private $priceSellBrutto;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Vat")
     * @Gedmo\Versioned
     * @Groups({
     *     "order_line_read",
     *     "order_line_write",
     *     "order_header_read",
     *     "order_header_write"
     * })
     * @Assert\NotNull()
     */
    private $vat;

    public function __construct()
    {
        $this->purchaseOrderLines = new ArrayCollection();
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

    public function getHeader(): ?OrderHeader
    {
        return $this->header;
    }

    public function setHeader(?OrderHeader $header): self
    {
        $this->header = $header;

        return $this;
    }

    public function getStatus(): ?OrderLineStatus
    {
        return $this->status;
    }

    public function setStatus(?OrderLineStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStockLine(): ?StockLine
    {
        return $this->stockLine;
    }

    public function setStockLine(?StockLine $stockLine): self
    {
        $this->stockLine = $stockLine;

        return $this;
    }

    /**
     * @return Collection|PurchaseOrderLine[]
     */
    public function getPurchaseOrderLines(): Collection
    {
        return $this->purchaseOrderLines;
    }

    public function addPurchaseOrderLine(PurchaseOrderLine $purchaseOrderLine): self
    {
        if (!$this->purchaseOrderLines->contains($purchaseOrderLine)) {
            $this->purchaseOrderLines[] = $purchaseOrderLine;
            $purchaseOrderLine->setOrderLine($this);
        }

        return $this;
    }

    public function removePurchaseOrderLine(PurchaseOrderLine $purchaseOrderLine): self
    {
        if ($this->purchaseOrderLines->contains($purchaseOrderLine)) {
            $this->purchaseOrderLines->removeElement($purchaseOrderLine);
            // set the owning side to null (unless already changed)
            if ($purchaseOrderLine->getOrderLine() === $this) {
                $purchaseOrderLine->setOrderLine(null);
            }
        }

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

    public function getProductSellPrice(): ProductSellPrice
    {
        return $this->productSellPrice;
    }

    public function setProductSellPrice(ProductSellPrice $productSellPrice): self
    {
        $this->productSellPrice = $productSellPrice;

        return $this;
    }
}
