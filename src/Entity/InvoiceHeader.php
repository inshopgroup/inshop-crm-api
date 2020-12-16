<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiSubresource;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
 * InvoiceHeader
 *
 * @ORM\Table(name="invoice_header")
 * @ORM\Entity(repositoryClass="App\Repository\InvoiceHeaderRepository")
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"invoice_header_read", "read", "is_active_read"}},
 *         "denormalization_context"={"groups"={"invoice_header_write", "is_active_write"}},
 *         "order"={"id": "DESC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "normalization_context"={
 *                  "groups"={"invoice_header_read_collection", "read"}
 *              },
 *              "access_control"="is_granted('ROLE_INVOICE_HEADER_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_INVOICE_HEADER_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_INVOICE_HEADER_SHOW')"
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_INVOICE_HEADER_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_INVOICE_HEADER_DELETE')"
 *          }
 *     }
 * )
 * @ApiFilter(DateFilter::class, properties={"createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "name": "ipartial",
 * })
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "name",
 *          "createdAt",
 *          "updatedAt"
 *     }
 * )
 */
class InvoiceHeader
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
     *     "invoice_header_read",
     *     "invoice_header_read_collection"
     * })
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Groups({
     *     "invoice_header_read",
     *     "invoice_header_read_collection",
     *     "invoice_header_write"
     * })
     * @Assert\NotBlank()
     */
    private string $number;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\InvoiceStatus")
     * @Groups({
     *     "invoice_header_read",
     *     "invoice_header_read_collection",
     *     "invoice_header_write"
     * })
     * @Assert\NotBlank()
     */
    private ?InvoiceStatus $status = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\InvoiceType")
     * @Groups({
     *     "invoice_header_read",
     *     "invoice_header_read_collection",
     *     "invoice_header_write"
     * })
     * @Assert\NotBlank()
     */
    private ?InvoiceType $type = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\OrderHeader")
     * @Groups({
     *     "invoice_header_read",
     *     "invoice_header_write"
     * })
     */
    private ?OrderHeader $orderHeader = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company")
     * @Groups({
     *     "invoice_header_read",
     *     "invoice_header_read_collection",
     *     "invoice_header_write"
     * })
     * @Assert\NotNull()
     */
    private ?Company $companyFrom = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company")
     * @Groups({
     *     "invoice_header_read",
     *     "invoice_header_read_collection",
     *     "invoice_header_write"
     * })
     */
    private ?Company $companyTo = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Document")
     * @Groups({
     *     "invoice_header_read",
     *     "invoice_header_write"
     * })
     * @Assert\NotBlank()
     */
    private ?Document $agreement = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Currency")
     * @Groups({
     *     "invoice_header_read",
     *     "invoice_header_write"
     * })
     * @Assert\NotBlank()
     */
    private ?Currency $currency = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language")
     * @Groups({
     *     "invoice_header_read",
     *     "invoice_header_write"
     * })
     * @Assert\NotBlank()
     */
    private ?Language $language = null;

    /**
     * @ORM\Column(type="date", nullable=false)
     * @Groups({
     *     "invoice_header_read",
     *     "invoice_header_write"
     * })
     * @Assert\NotBlank()
     */
    private DateTimeInterface $dateOfInvoice;

    /**
     * @ORM\Column(type="date", nullable=false)
     * @Groups({
     *     "invoice_header_read",
     *     "invoice_header_write"
     * })
     * @Assert\NotBlank()
     */
    private DateTimeInterface $dateOfSale;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({
     *     "invoice_header_read",
     *     "invoice_header_write"
     * })
     */
    private ?string $maturity = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\InvoiceLine", mappedBy="header", cascade={"persist"})
     * @Groups({
     *     "invoice_header_read",
     *     "invoice_header_write"
     * })
     * @Assert\Valid()
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private Collection $lines;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\File")
     * @ORM\JoinColumn(nullable=true)
     * @ApiProperty(iri="http://schema.org/image")
     * @ApiSubresource()
     * @Groups({
     *     "document_read",
     *     "document_write",
     *     "project_read"
     * })
     * @ORM\OrderBy({"id" = "DESC"})
     */
    public Collection $files;

    public function __construct()
    {
        $this->lines = new ArrayCollection();
        $this->files = new ArrayCollection();
    }

    public function __sleep()
    {
        return [];
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

    public function getDateOfInvoice(): ?DateTimeInterface
    {
        return $this->dateOfInvoice;
    }

    public function setDateOfInvoice(DateTimeInterface $dateOfInvoice): self
    {
        $this->dateOfInvoice = $dateOfInvoice;

        return $this;
    }

    public function getDateOfSale(): ?DateTimeInterface
    {
        return $this->dateOfSale;
    }

    public function setDateOfSale(DateTimeInterface $dateOfSale): self
    {
        $this->dateOfSale = $dateOfSale;

        return $this;
    }

    public function getMaturity(): ?string
    {
        return $this->maturity;
    }

    public function setMaturity(?string $maturity): self
    {
        $this->maturity = $maturity;

        return $this;
    }

    public function getCompanyFrom(): ?Company
    {
        return $this->companyFrom;
    }

    public function setCompanyFrom(?Company $companyFrom): self
    {
        $this->companyFrom = $companyFrom;

        return $this;
    }

    public function getCompanyTo(): ?Company
    {
        return $this->companyTo;
    }

    public function setCompanyTo(?Company $companyTo): self
    {
        $this->companyTo = $companyTo;

        return $this;
    }

    public function getAgreement(): ?Document
    {
        return $this->agreement;
    }

    public function setAgreement(?Document $agreement): self
    {
        $this->agreement = $agreement;

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

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return Collection|InvoiceLine[]
     */
    public function getLines(): Collection
    {
        return $this->lines;
    }

    public function addLine(InvoiceLine $line): self
    {
        if (!$this->lines->contains($line)) {
            $this->lines[] = $line;
            $line->setHeader($this);
        }

        return $this;
    }

    public function removeLine(InvoiceLine $line): self
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

    /**
     * @return Collection|File[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
        }

        return $this;
    }

    public function getStatus(): ?InvoiceStatus
    {
        return $this->status;
    }

    public function setStatus(?InvoiceStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getOrderHeader(): ?OrderHeader
    {
        return $this->orderHeader;
    }

    public function setOrderHeader(?OrderHeader $orderHeader): self
    {
        $this->orderHeader = $orderHeader;

        return $this;
    }

    public function getType(): ?InvoiceType
    {
        return $this->type;
    }

    public function setType(?InvoiceType $type): self
    {
        $this->type = $type;

        return $this;
    }
}
