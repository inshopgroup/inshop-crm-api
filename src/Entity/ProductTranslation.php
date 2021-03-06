<?php

namespace App\Entity;

use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * @ORM\Table(name="product_translation", uniqueConstraints={
 *     @ORM\UniqueConstraint(
 *          name="product_language_translatable_idx",
 *          columns={
 *              "language_id",
 *              "translatable_id"
 *          }
 *     )
 * })
 * @ORM\Entity(repositoryClass="App\Repository\ProductTranslationRepository")
 * @UniqueEntity(fields={"language", "translatable"}, errorPath="language")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"product_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"product_write", "is_active_write"}},
 *          "order"={"id": "DESC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_PRODUCT_LIST')"
 *          },
 *          "post"={
 *              "security"="is_granted('ROLE_PRODUCT_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_PRODUCT_SHOW')"
 *          },
 *          "put"={
 *              "security"="is_granted('ROLE_PRODUCT_UPDATE')"
 *          },
 *          "delete"={
 *              "security"="is_granted('ROLE_PRODUCT_DELETE')"
 *          }
 *     })
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
class ProductTranslation
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @Groups({
     *     "product_read",
     *     "product_write"
     * })
     */
    private ?int $id = null;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128)
     */
    private string $slug;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="translations")
     * @Assert\NotBlank()
     */
    protected ?Product $translatable = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language")
     * @Assert\NotBlank()
     * @Groups({
     *     "product_read",
     *     "product_write",
     *     "product_read_frontend"
     * })
     */
    protected ?Language $language = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({
     *     "product_write",
     *     "product_read",
     *     "product_read_frontend"
     * })
     * @Assert\NotBlank()
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({
     *     "product_write",
     *     "product_read",
     *     "product_read_frontend"
     * })
     */
    private ?string $description = null;

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string
     * @return ProductTranslation|null
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return ProductTranslation|null
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTranslatable(): ?Product
    {
        return $this->translatable;
    }

    public function setTranslatable(?Product $translatable): self
    {
        $this->translatable = $translatable;

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

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
