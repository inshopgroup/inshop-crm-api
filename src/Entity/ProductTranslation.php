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
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\Loggable
 * @ApiResource(attributes={
 *     "normalization_context"={"groups"={"product_read", "read"}},
 *     "denormalization_context"={"groups"={"product_write"}},
 *     "order"={"id": "DESC"}
 * },
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_PRODUCT_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_PRODUCT_CREATE')"
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
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({
     *     "product_read",
     *     "product_write"
     * })
     */
    private $id;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128)
     * @Groups({
     *     "product_read",
     *     "product_write",
     *     "product_read_frontend"
     * })
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="translations")
     * @Assert\NotBlank()
     */
    protected $translatable;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language")
     * @Assert\NotBlank()
     * @Groups({
     *     "product_read",
     *     "product_write",
     *     "product_read_frontend"
     * })
     */
    protected $language;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Gedmo\Versioned
     * @Groups({
     *     "product_write",
     *     "product_read",
     *     "product_read_frontend"
     * })
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     * @Groups({
     *     "product_write",
     *     "product_read",
     *     "product_read_frontend"
     * })
     */
    private $description;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string
     * @return null
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param  string
     * @return null
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTranslatable(): Product
    {
        return $this->translatable;
    }

    public function setTranslatable(Product $translatable): self
    {
        $this->translatable = $translatable;

        return $this;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function setLanguage(Language $language): self
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
