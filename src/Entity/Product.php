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
use App\Controller\Product\ProductFrontendGetItemAction;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"product_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"product_write", "is_active_write"}},
 *          "order"={"id": "DESC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_PRODUCT_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_PRODUCT_CREATE')"
 *          },
 *          "frontendGet"={
 *              "method"="GET",
 *              "path"="/frontend/products",
 *              "normalization_context"={
 *                  "groups"={"Default"}
 *              },
 *          },
 *          "searchGet"={
 *              "access_control"="is_granted('ROLE_OTHER_SEARCH')",
 *              "method"="GET",
 *              "path"="/search",
 *              "normalization_context"={
 *                  "groups"={"Default"}
 *              },
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_PRODUCT_SHOW')"
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_PRODUCT_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_PRODUCT_DELETE')"
 *          },
 *          "frontendGet"={
 *              "method"="GET",
 *              "path"="/frontend/products/{slug}",
 *              "normalization_context"={
 *                  "groups"={"Default"}
 *              },
 *              "controller"=ProductFrontendGetItemAction::class,
 *              "defaults"={"_api_receive"=false},
 *          },
 *     })
 * @ApiFilter(DateFilter::class, properties={"createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "name": "ipartial",
 *     "ean": "ipartial",
 *     "category.id": "exact",
 *     "category.name": "ipartial",
 *     "brand.name": "ipartial",
 *     "translations.name": "ipartial",
 * })
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "translations.name",
 *          "ean",
 *          "category.translations.name",
 *          "brand.name",
 *          "createdAt",
 *          "updatedAt"
 *     }
 * )
 */
class Product
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({
     *     "product_read",
     *     "invoice_header_read",
     *     "invoice_header_write",
     *     "order_header_write",
     *     "company_product_read",
     *     "company_product_write",
     *     "product_sell_price_read",
     *     "product_sell_price_write",
     *     "order_header_read",
     *     "product_read_frontend",
     *     "product_read_frontend_item"
     * })
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category")
     * @Gedmo\Versioned
     * @Groups({
     *     "product_read",
     *     "product_write",
     *     "product_read_frontend_item"
     * })
     * @Assert\NotBlank()
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Brand")
     * @Gedmo\Versioned
     * @Groups({
     *     "product_read",
     *     "product_write",
     * })
     * @Assert\NotBlank()
     */
    private $brand;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Gedmo\Versioned
     * @Groups({
     *     "product_read",
     *     "product_write",
     *     "product_read_frontend",
     *     "product_read_frontend_item"
     * })
     * @Assert\NotBlank()
     */
    private $ean;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CompanyProduct", mappedBy="product")
     * @ORM\OrderBy({"id" = "DESC"})
     * @Groups({
     *     "product_write"
     * })
     * @ApiSubresource()
     */
    private $companyProducts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductSellPrice", mappedBy="product")
     * @ORM\OrderBy({"id" = "DESC"})
     * @ApiSubresource()
     */
    private $productSellPrices;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductTranslation", mappedBy="translatable", cascade={"persist"}, orphanRemoval=true)
     * @Groups({
     *     "product_read",
     *     "product_write",
     *     "product_read_frontend",
     *     "product_read_frontend_item"
     * })
     * @ORM\OrderBy({"id" = "ASC"})
     * @Assert\Valid()
     * @Assert\Count(min=1)
     */
    private $translations;

    /**
     * @var Image[]
     * @ORM\ManyToMany(targetEntity="App\Entity\Image")
     * @Groups({
     *     "product_read",
     *     "product_write",
     *     "product_read_frontend",
     *     "product_read_frontend_item"
     * })
     * @ORM\JoinColumn(nullable=true)
     * @ApiProperty(iri="http://schema.org/image")
     * @ApiSubresource()
     * @ORM\OrderBy({"id" = "DESC"})
     */
    public $images;

    /**
     * Product constructor.
     */
    public function __construct()
    {
        $this->companyProducts = new ArrayCollection();
        $this->productSellPrices = new ArrayCollection();
        $this->translations = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getEan(): ?string
    {
        return $this->ean;
    }

    public function setEan(string $ean): self
    {
        $this->ean = $ean;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @return Collection|CompanyProduct[]
     */
    public function getCompanyProducts(): Collection
    {
        return $this->companyProducts;
    }

    public function addCompanyProduct(CompanyProduct $companyProduct): self
    {
        if (!$this->companyProducts->contains($companyProduct)) {
            $this->companyProducts[] = $companyProduct;
            $companyProduct->setProduct($this);
        }

        return $this;
    }

    public function removeCompanyProduct(CompanyProduct $companyProduct): self
    {
        if ($this->companyProducts->contains($companyProduct)) {
            $this->companyProducts->removeElement($companyProduct);
            // set the owning side to null (unless already changed)
            if ($companyProduct->getProduct() === $this) {
                $companyProduct->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProductSellPrice[]
     */
    public function getProductSellPrices(): Collection
    {
        return $this->productSellPrices;
    }

    public function addProductSellPrice(ProductSellPrice $productSellPrice): self
    {
        if (!$this->productSellPrices->contains($productSellPrice)) {
            $this->productSellPrices[] = $productSellPrice;
            $productSellPrice->setProduct($this);
        }

        return $this;
    }

    public function removeProductSellPrice(ProductSellPrice $productSellPrice): self
    {
        if ($this->productSellPrices->contains($productSellPrice)) {
            $this->productSellPrices->removeElement($productSellPrice);
            // set the owning side to null (unless already changed)
            if ($productSellPrice->getProduct() === $this) {
                $productSellPrice->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProductTranslation[]
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(ProductTranslation $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation;
            $translation->setTranslatable($this);
        }

        return $this;
    }

    public function removeTranslation(ProductTranslation $translation): self
    {
        if ($this->translations->contains($translation)) {
            $this->translations->removeElement($translation);
            // set the owning side to null (unless already changed)
            if ($translation->getTranslatable() === $this) {
                $translation->setTranslatable(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
        }

        return $this;
    }

    /**
     * @return ProductTranslation
     */
    public function getTranslation(): ProductTranslation
    {
        /** @var ProductTranslation $translation */
        foreach ($this->getTranslations() as $translation) {
            if ($translation->getLanguage()->getCode() === 'en') {
                return $translation;
            }
        }

        return $this->getTranslations()->first();
    }

    /**
     * @return string
     * @Groups({
     *     "product_read",
     *     "product_read_frontend",
     *     "product_read_frontend_item"
     * })
     */
    public function getName(): string
    {
        return $this->getTranslation()->getName();
    }

    /**
     * @return string
     * @Groups({
     *     "product_read",
     *     "product_read_frontend",
     *     "product_read_frontend_item"
     * })
     */
    public function getSlug(): string
    {
        return $this->getTranslation()->getSlug();
    }
}
