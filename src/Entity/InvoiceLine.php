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
 * InvoiceLine
 *
 * @ORM\Table(name="invoice_line")
 * @ORM\Entity(repositoryClass="App\Repository\InvoiceLineRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\Loggable
 * @ApiResource(attributes={
 *     "normalization_context"={"groups"={"invoice_line_read", "read", "is_active_read"}},
 *     "denormalization_context"={"groups"={"invoice_line_write", "is_active_write"}},
 *     "order"={"id": "DESC"}
 * },
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_INVOICE_LINE_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_INVOICE_LINE_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_INVOICE_LINE_SHOW')"
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_INVOICE_LINE_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_INVOICE_LINE_DELETE')"
 *          }
 *     })
 */
class InvoiceLine
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
     *     "invoice_line_read",
     *     "invoice_header_read",
     *     "invoice_header_write"
     * })
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\InvoiceHeader", inversedBy="lines")
     * @Gedmo\Versioned
     * @Groups({
     *     "invoice_line_write"
     * })
     * @Assert\NotBlank()
     */
    private $header;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product")
     * @Gedmo\Versioned
     * @Groups({
     *     "invoice_line_read",
     *     "invoice_line_write",
     *     "invoice_header_read",
     *     "invoice_header_write"
     * })
     * @Assert\NotBlank()
     */
    private $product;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Gedmo\Versioned
     * @Groups({
     *     "invoice_line_read",
     *     "invoice_line_write",
     *     "invoice_header_read",
     *     "invoice_header_write"
     * })
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="float", nullable=false)
     * @Gedmo\Versioned
     * @Groups({
     *     "invoice_line_read",
     *     "invoice_line_write",
     *     "invoice_header_read",
     *     "invoice_header_write"
     * })
     * @Assert\NotBlank()
     */
    private $unitPriceNetto;

    /**
     * @ORM\Column(type="float", nullable=false)
     * @Gedmo\Versioned
     * @Groups({
     *     "invoice_line_read",
     *     "invoice_line_write",
     *     "invoice_header_read",
     *     "invoice_header_write"
     * })
     * @Assert\NotBlank()
     */
    private $unitsCount;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Vat")
     * @Gedmo\Versioned
     * @Groups({
     *     "invoice_line_read",
     *     "invoice_header_read",
     *     "invoice_header_write"
     * })
     * @Assert\NotBlank()
     */
    private $vat;

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

    public function getUnitPriceNetto(): ?float
    {
        return $this->unitPriceNetto;
    }

    public function setUnitPriceNetto(float $unitPriceNetto): self
    {
        $this->unitPriceNetto = $unitPriceNetto;

        return $this;
    }

    public function getUnitsCount(): ?float
    {
        return $this->unitsCount;
    }

    public function setUnitsCount(float $unitsCount): self
    {
        $this->unitsCount = $unitsCount;

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

    public function getVat(): ?Vat
    {
        return $this->vat;
    }

    public function setVat(?Vat $vat): self
    {
        $this->vat = $vat;

        return $this;
    }

    public function getHeader(): ?InvoiceHeader
    {
        return $this->header;
    }

    public function setHeader(?InvoiceHeader $header): self
    {
        $this->header = $header;

        return $this;
    }
}
