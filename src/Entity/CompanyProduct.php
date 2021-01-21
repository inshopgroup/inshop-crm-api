<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
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
 * @ORM\Table(name="company_product")
 * @ORM\Entity(repositoryClass="App\Repository\CompanyProductRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"company_product_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"company_product_write", "is_active_write"}},
 *          "order"={"id": "DESC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_COMPANY_PRODUCT_LIST')"
 *          },
 *          "post"={
 *              "security"="is_granted('ROLE_COMPANY_PRODUCT_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_COMPANY_PRODUCT_SHOW')"
 *          },
 *          "put"={
 *              "security"="is_granted('ROLE_COMPANY_PRODUCT_UPDATE')"
 *          },
 *          "delete"={
 *              "security"="is_granted('ROLE_COMPANY_PRODUCT_DELETE')"
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
class CompanyProduct
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @Groups({
     *     "company_product_read",
     *     "product_sell_price_read",
     *     "product_sell_price_write"
     * })
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="companyProducts")
     * @Groups({
     *     "company_product_read",
     *     "company_product_write",
     *     "product_sell_price_read"
     * })
     * @Assert\NotNull()
     */
    private ?Company $company = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="companyProducts")
     * @Groups({
     *     "company_product_read",
     *     "company_product_write",
     *     "product_sell_price_read"
     * })
     * @Assert\NotNull()
     */
    private ?Product $product = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Currency")
     * @Groups({
     *     "company_product_read",
     *     "company_product_write",
     *     "product_sell_price_read"
     * })
     * @Assert\NotNull()
     */
    private ?Currency $currency = null;

    /**
     * @ORM\Column(type="float")
     * @Groups({
     *     "company_product_read",
     *     "company_product_write",
     *     "product_sell_price_read"
     * })
     * @Assert\NotBlank()
     */
    private float $priceBuyNetto;

    /**
     * @ORM\Column(type="integer")
     * @Groups({
     *     "company_product_read",
     *     "company_product_write",
     *     "product_sell_price_read"
     * })
     * @Assert\NotBlank()
     */
    private int $availability;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPriceBuyNetto(): float
    {
        return $this->priceBuyNetto;
    }

    public function setPriceBuyNetto(float $priceBuyNetto): self
    {
        $this->priceBuyNetto = $priceBuyNetto;

        return $this;
    }

    public function getAvailability(): int
    {
        return $this->availability;
    }

    public function setAvailability(int $availability): self
    {
        $this->availability = $availability;

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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

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

    /**
     * @return null|string
     * @Groups({
     *     "company_product_read",
     *     "product_sell_price_read"
     * })
     */
    public function getCompanyName(): ?string
    {
        return $this->getCompany() ? $this->getCompany()->getName() : null;
    }
}
