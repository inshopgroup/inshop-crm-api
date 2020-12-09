<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * InvoiceLine
 *
 * @ORM\Table(name="invoice_line")
 * @ORM\Entity(repositoryClass="App\Repository\InvoiceLineRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"invoice_line_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"invoice_line_write", "is_active_write"}},
 *          "order"={"id": "DESC"}
 *     },
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
     * @var int|null
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
    private ?int $id = null;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\InvoiceHeader", inversedBy="lines")
     * @Groups({
     *     "invoice_line_write"
     * })
     * @Assert\NotBlank()
     */
    private ?InvoiceHeader $header = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product")
     * @Groups({
     *     "invoice_line_read",
     *     "invoice_line_write",
     *     "invoice_header_read",
     *     "invoice_header_write"
     * })
     * @Assert\NotBlank()
     */
    private ?Product $product = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Groups({
     *     "invoice_line_read",
     *     "invoice_line_write",
     *     "invoice_header_read",
     *     "invoice_header_write"
     * })
     * @Assert\NotBlank()
     */
    private string $name;

    /**
     * @ORM\Column(type="float", nullable=false)
     * @Groups({
     *     "invoice_line_read",
     *     "invoice_line_write",
     *     "invoice_header_read",
     *     "invoice_header_write"
     * })
     * @Assert\NotBlank()
     */
    private float $unitPriceNetto;

    /**
     * @ORM\Column(type="float", nullable=false)
     * @Groups({
     *     "invoice_line_read",
     *     "invoice_line_write",
     *     "invoice_header_read",
     *     "invoice_header_write"
     * })
     * @Assert\NotBlank()
     */
    private float $unitsCount;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Vat")
     * @Groups({
     *     "invoice_line_read",
     *     "invoice_header_read",
     *     "invoice_header_write"
     * })
     * @Assert\NotBlank()
     */
    private ?Vat $vat = null;

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
