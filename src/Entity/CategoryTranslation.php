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
 * @ORM\Table(name="category_translation", uniqueConstraints={
 *     @ORM\UniqueConstraint(
 *          name="category_language_translatable_idx",
 *          columns={
 *              "language_id",
 *              "translatable_id"
 *          }
 *     )
 * })
 * @ORM\Entity(repositoryClass="App\Repository\CategoryTranslationRepository")
 * @UniqueEntity(fields={"language", "translatable"}, errorPath="language")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"category_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"category_write", "is_active_write"}},
 *          "order"={"id": "DESC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_CATEGORY_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_CATEGORY_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_CATEGORY_SHOW')"
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_CATEGORY_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_CATEGORY_DELETE')"
 *          }
 *     })
 * @ApiFilter(DateFilter::class, properties={"createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "name": "ipartial",
 *     "parent.name": "ipartial",
 * })
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "name",
 *          "parent.name",
 *          "createdAt",
 *          "updatedAt"
 *     }
 * )
 */
class CategoryTranslation
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({
     *     "category_read",
     *     "category_write"
     * })
     */
    private ?int $id = null;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128)
     */
    private string $slug;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="translations")
     * @Assert\NotBlank()
     */
    protected ?Category $translatable = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language")
     * @Assert\NotBlank()
     * @Groups({
     *     "category_read",
     *     "category_write",
     *     "product_read",
     *     "category_read_frontend"
     * })
     */
    protected ?Language $language = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Groups({
     *     "category_read",
     *     "category_write",
     *     "product_read",
     *     "category_read_frontend"
     * })
     * @Assert\NotBlank()
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({
     *     "category_read",
     *     "category_write",
     *     "product_read"
     * })
     */
    private ?string $description = null;

    public function __sleep()
    {
        return [];
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string
     * @return CategoryTranslation
     */
    public function setName($name): self
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
     * @param string
     * @return CategoryTranslation
     */
    public function setDescription($description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTranslatable(): ?Category
    {
        return $this->translatable;
    }

    public function setTranslatable(?Category $translatable): self
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
