<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use Gedmo\Mapping\Annotation as Gedmo;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PurchaseOrderLine
 *
 * @ORM\Table(name="purchase_order_line")
 * @ORM\Entity(repositoryClass="App\Repository\PurchaseOrderLineRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\Loggable
 * @ApiResource(attributes={
 *     "normalization_context"={"groups"={"purchase_order_line_read", "read"}},
 *     "denormalization_context"={"groups"={"purchase_order_line_write"}},
 *     "order"={"id": "DESC"}
 * },
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_PURCHASE_ORDER_LINE_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_PURCHASE_ORDER_LINE_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_PURCHASE_ORDER_LINE_SHOW')"
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_PURCHASE_ORDER_LINE_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_PURCHASE_ORDER_LINE_DELETE')"
 *          }
 *     })
 */
class PurchaseOrderLine
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
     *     "purchase_order_line_read",
     *     "purchase_order_read",
     *     "purchase_order_write"
     * })
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PurchaseOrderHeader", inversedBy="lines")
     * @Gedmo\Versioned
     * @Groups({
     *     "purchase_order_line_write"
     * })
     * @Assert\NotBlank()
     */
    private $header;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PurchaseOrderLineStatus")
     * @Gedmo\Versioned
     * @Groups({
     *     "purchase_order_line_read",
     *     "purchase_order_line_write",
     *     "purchase_order_header_read",
     *     "purchase_order_header_write"
     * })
     * @Assert\NotBlank()
     */
    private $status;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ShippingNoticeLine", mappedBy="purchaseOrderLine")
     * @Gedmo\Versioned
     * @Groups({
     *     "purchase_order_line_read",
     *     "purchase_order_line_write",
     *     "purchase_order_header_read",
     *     "purchase_order_header_write"
     * })
     * @Assert\NotBlank()
     */
    private $shippingNoticeLine;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\OrderLine", inversedBy="purchaseOrderLines")
     * @Groups({
     *     "purchase_order_line_read",
     *     "purchase_order_line_write",
     *     "purchase_order_header_read",
     *     "purchase_order_header_write"
     * })
     * @Assert\NotBlank()
     */
    private $orderLine;

    /**
     * @ORM\Column(type="float", nullable=false)
     * @Gedmo\Versioned
     * @Groups({
     *     "purchase_order_line_read",
     *     "purchase_order_line_write",
     *     "purchase_order_header_read",
     *     "purchase_order_header_write"
     * })
     * @Assert\NotBlank()
     */
    private $priceBuyNetto;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Vat")
     * @Gedmo\Versioned
     * @Groups({
     *     "purchase_order_line_read",
     *     "purchase_order_line_write",
     *     "purchase_order_header_read",
     *     "purchase_order_header_write"
     * })
     * @Assert\NotBlank()
     */
    private $vat;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPriceBuyNetto(): ?float
    {
        return $this->priceBuyNetto;
    }

    public function setPriceBuyNetto(float $priceBuyNetto): self
    {
        $this->priceBuyNetto = $priceBuyNetto;

        return $this;
    }

    public function getHeader(): ?PurchaseOrderHeader
    {
        return $this->header;
    }

    public function setHeader(?PurchaseOrderHeader $header): self
    {
        $this->header = $header;

        return $this;
    }

    public function getStatus(): ?PurchaseOrderLineStatus
    {
        return $this->status;
    }

    public function setStatus(?PurchaseOrderLineStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getShippingNoticeLine(): ?ShippingNoticeLine
    {
        return $this->shippingNoticeLine;
    }

    public function setShippingNoticeLine(?ShippingNoticeLine $shippingNoticeLine): self
    {
        $this->shippingNoticeLine = $shippingNoticeLine;

        // set (or unset) the owning side of the relation if necessary
        $newPurchaseOrderLine = $shippingNoticeLine === null ? null : $this;
        if ($newPurchaseOrderLine !== $shippingNoticeLine->getPurchaseOrderLine()) {
            $shippingNoticeLine->setPurchaseOrderLine($newPurchaseOrderLine);
        }

        return $this;
    }

    public function getOrderLine(): ?OrderLine
    {
        return $this->orderLine;
    }

    public function setOrderLine(?OrderLine $orderLine): self
    {
        $this->orderLine = $orderLine;

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
}
