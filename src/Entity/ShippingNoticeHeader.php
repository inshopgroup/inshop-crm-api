<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiSubresource;
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
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * ShippingNoticeHeader
 *
 * @ORM\Table(name="shipping_notice_header")
 * @ORM\Entity(repositoryClass="App\Repository\ShippingNoticeHeaderRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\Loggable
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"shipping_notice_header_read", "read"}},
 *         "denormalization_context"={"groups"={"shipping_notice_header_write"}},
 *         "order"={"id": "DESC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "normalization_context"={
 *                  "groups"={"shipping_notice_header_read_collection", "read"}
 *              },
 *              "access_control"="is_granted('ROLE_SHIPPING_NOTICE_HEADER_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_SHIPPING_NOTICE_HEADER_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_SHIPPING_NOTICE_HEADER_SHOW')"
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_SHIPPING_NOTICE_HEADER_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_SHIPPING_NOTICE_HEADER_DELETE')"
 *          }
 *     }
 * )
 * @ApiFilter(DateFilter::class, properties={"createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "number": "exact",
 *     "company.name": "ipartial",
 *     "status.name": "ipartial",
 *     "currency.name": "ipartial",
 * })
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "number",
 *          "company.name",
 *          "status.name",
 *          "currency.name",
 *          "createdAt",
 *          "updatedAt"
 *     }
 * )
 */
class ShippingNoticeHeader
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
     *     "shipping_notice_header_read",
     *     "shipping_notice_header_read_collection"
     * })
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Gedmo\Versioned
     * @Groups({
     *     "shipping_notice_header_read",
     *     "shipping_notice_header_write",
     *     "shipping_notice_header_read_collection"
     * })
     * @Assert\NotBlank()
     */
    private $number;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ShippingNoticeStatus")
     * @Gedmo\Versioned
     * @Groups({
     *     "shipping_notice_header_read",
     *     "shipping_notice_header_write",
     *     "shipping_notice_header_read_collection"
     * })
     * @Assert\NotNull()
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company")
     * @Gedmo\Versioned
     * @Groups({
     *     "shipping_notice_header_read",
     *     "shipping_notice_header_write",
     *     "shipping_notice_header_read_collection"
     * })
     * @Assert\NotNull()
     */
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Currency")
     * @Gedmo\Versioned
     * @Groups({
     *     "shipping_notice_header_read",
     *     "shipping_notice_header_write",
     *     "shipping_notice_header_read_collection"
     * })
     * @Assert\NotNull()
     */
    private $currency;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PaymentType")
     * @Gedmo\Versioned
     * @Groups({
     *     "shipping_notice_header_read",
     *     "shipping_notice_header_write",
     *     "shipping_notice_header_read_collection"
     * })
     * @Assert\NotNull()
     */
    private $paymentType;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ShipmentMethod")
     * @Gedmo\Versioned
     * @Groups({
     *     "shipping_notice_header_read",
     *     "shipping_notice_header_write",
     *     "shipping_notice_header_read_collection"
     * })
     * @Assert\NotNull()
     */
    private $shipmentMethod;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderLine", mappedBy="header", cascade={"persist"})
     * @Groups({
     *     "shipping_notice_header_write",
     *     "shipping_notice_header_read"
     * })
     * @Assert\Valid()
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $lines;

    public function __construct()
    {
        $this->lines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getStatus(): ?ShippingNoticeStatus
    {
        return $this->status;
    }

    public function setStatus(?ShippingNoticeStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getPaymentType(): ?PaymentType
    {
        return $this->paymentType;
    }

    public function setPaymentType(?PaymentType $paymentType): self
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    public function getShipmentMethod(): ?ShipmentMethod
    {
        return $this->shipmentMethod;
    }

    public function setShipmentMethod(?ShipmentMethod $shipmentMethod): self
    {
        $this->shipmentMethod = $shipmentMethod;

        return $this;
    }

    /**
     * @return Collection|OrderLine[]
     */
    public function getLines(): Collection
    {
        return $this->lines;
    }

    public function addLine(OrderLine $line): self
    {
        if (!$this->lines->contains($line)) {
            $this->lines[] = $line;
            $line->setHeader($this);
        }

        return $this;
    }

    public function removeLine(OrderLine $line): self
    {
        if ($this->lines->contains($line)) {
            $this->lines->removeElement($line);
            // set the owning side to null (unless already changed)
            if ($line->getHeader() === $this) {
                $line->setHeader(null);
            }
        }

        return $this;
    }
}
