<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\BlameableEntity;
use App\Traits\SoftDeleteableEntity;
use App\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Controller\CategoryAction;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\Loggable
 * @ApiResource(attributes={
 *     "normalization_context"={"groups"={"category_read", "read"}},
 *     "denormalization_context"={"groups"={"category_write"}},
 *     "order"={"id": "DESC"}
 * },
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_CATEGORY_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_CATEGORY_CREATE')"
 *          },
 *          "frontend"={
 *              "method"="GET",
 *              "path"="/frontend/categories",
 *              "controller"=CategoryAction::class,
 *              "defaults"={"_api_receive"=false},
 *              "normalization_context"={
 *                  "groups"={"category_read_frontend"}
 *              },
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
 *     "translations.name": "ipartial",
 *     "translations.parent.name": "ipartial",
 * })
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "translations.name",
 *          "translations.parent.name",
 *          "createdAt",
 *          "updatedAt"
 *     }
 * )
 */
class Category
{
    /**
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableEntity;

    /**
     * Hook blameable behavior
     * updates createdBy, updatedBy fields
     */
    use BlameableEntity;

    /**
     * Hook SoftDeleteable behavior
     * updates deletedAt field
     */
    use SoftDeleteableEntity;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({
     *     "category_read",
     *     "product_write",
     *     "category_read_frontend"
     * })
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="subCategories")
     * @Gedmo\Versioned
     * @Groups({
     *     "category_read",
     *     "category_write",
     *     "product_read",
     *     "category_read_frontend"
     * })
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Category", mappedBy="parent")
     * @Groups({
     *     "category_read",
     *     "category_write",
     *     "category_read_frontend"
     * })
     * @Assert\Valid()
     */
    private $subCategories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CategoryTranslation", mappedBy="translatable", cascade={"persist"}, orphanRemoval=true)
     * @Groups({
     *     "category_read",
     *     "category_write",
     *     "category_read_frontend"
     * })
     * @ORM\OrderBy({"id" = "ASC"})
     * @Assert\Valid()
     * @Assert\Count(min=1)
     */
    private $translations;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     * @Gedmo\Versioned
     * @Groups({
     *     "category_read",
     *     "category_write"
     * })
     */
    private $isActive = true;

    /**
     * @var boolean
     *
     * @ORM\Column(type="integer")
     * @Gedmo\Versioned
     * @Groups({
     *     "category_read",
     *     "category_write"
     * })
     */
    private $position = 0;

    public function __construct()
    {
        $this->subCategories = new ArrayCollection();
        $this->translations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getSubCategories(): Collection
    {
        return $this->subCategories;
    }

    public function addSubCategory(Category $subCategory): self
    {
        if (!$this->subCategories->contains($subCategory)) {
            $this->subCategories[] = $subCategory;
            $subCategory->setParent($this);
        }

        return $this;
    }

    public function removeSubCategory(Category $subCategory): self
    {
        if ($this->subCategories->contains($subCategory)) {
            $this->subCategories->removeElement($subCategory);
            // set the owning side to null (unless already changed)
            if ($subCategory->getParent() === $this) {
                $subCategory->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CategoryTranslation[]
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(CategoryTranslation $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation;
            $translation->setTranslatable($this);
        }

        return $this;
    }

    public function removeTranslation(CategoryTranslation $translation): self
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
     * @return string
     * @Groups({
     *     "category_read",
     *     "product_read",
     *     "category_read_frontend"
     * })
     */
    public function getName(): string
    {
        /** @var CategoryTranslation $translation */
        $translation = $this->getTranslations()->first();

        if ($translation) {
            return $translation->getName();
        }

        return '';
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }
}
