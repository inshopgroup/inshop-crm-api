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
 * ShippingNoticeLine
 *
 * @ORM\Table(name="shipping_notice_line")
 * @ORM\Entity(repositoryClass="App\Repository\ShippingNoticeLineRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\Loggable
 * @ApiResource(attributes={
 *     "normalization_context"={"groups"={"shipping_notice_line_read", "read"}},
 *     "denormalization_context"={"groups"={"shipping_notice_line_write"}},
 *     "order"={"id": "DESC"}
 * },
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_SHIPPING_NOTICE_LINE_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_SHIPPING_NOTICE_LINE_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_SHIPPING_NOTICE_LINE_SHOW')"
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_SHIPPING_NOTICE_LINE_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_SHIPPING_NOTICE_LINE_DELETE')"
 *          }
 *     })
 */
class ShippingNoticeLine
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
     *     "shipping_notice_line_read",
     *     "shipping_notice_read",
     *     "shipping_notice_write"
     * })
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ShippingNoticeHeader", inversedBy="lines")
     * @Gedmo\Versioned
     * @Groups({
     *     "shipping_notice_line_write"
     * })
     * @Assert\NotBlank()
     */
    private $header;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ShippingNoticeLineStatus")
     * @Gedmo\Versioned
     * @Groups({
     *     "shipping_notice_line_read",
     *     "shipping_notice_line_write",
     *     "shipping_notice_header_read",
     *     "shipping_notice_header_write"
     * })
     * @Assert\NotBlank()
     */
    private $status;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\PurchaseOrderLine", inversedBy="shippingNoticeLine")
     * @Gedmo\Versioned
     * @Groups({
     *     "shipping_notice_line_read",
     *     "shipping_notice_line_write",
     *     "shipping_notice_header_read",
     *     "shipping_notice_header_write"
     * })
     * @Assert\NotBlank()
     */
    private $purchaseOrderLine;

    /**
     * @ORM\Column(type="float", nullable=false)
     * @Gedmo\Versioned
     * @Groups({
     *     "shipping_notice_line_read",
     *     "shipping_notice_line_write",
     *     "shipping_notice_header_read",
     *     "shipping_notice_header_write"
     * })
     * @Assert\NotBlank()
     */
    private $priceBuyNetto;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Vat")
     * @Gedmo\Versioned
     * @Groups({
     *     "shipping_notice_line_read",
     *     "shipping_notice_line_write",
     *     "shipping_notice_header_read",
     *     "shipping_notice_header_write"
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

    public function getHeader(): ?ShippingNoticeHeader
    {
        return $this->header;
    }

    public function setHeader(?ShippingNoticeHeader $header): self
    {
        $this->header = $header;

        return $this;
    }

    public function getStatus(): ?ShippingNoticeLineStatus
    {
        return $this->status;
    }

    public function setStatus(?ShippingNoticeLineStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPurchaseOrderLine(): ?PurchaseOrderLine
    {
        return $this->purchaseOrderLine;
    }

    public function setPurchaseOrderLine(?PurchaseOrderLine $purchaseOrderLine): self
    {
        $this->purchaseOrderLine = $purchaseOrderLine;

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
