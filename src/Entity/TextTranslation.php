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
 * @ORM\Table(name="text_translation", uniqueConstraints={
 *     @ORM\UniqueConstraint(
 *          name="text_language_translatable_idx",
 *          columns={
 *              "language_id",
 *              "translatable_id"
 *          }
 *     )
 * })
 * @ORM\Entity(repositoryClass="App\Repository\TextTranslationRepository")
 * @UniqueEntity(fields={"language", "translatable"}, errorPath="language")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"text_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"text_write", "is_active_write"}},
 *          "order"={"id": "DESC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_TEXT_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_TEXT_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_TEXT_SHOW')"
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_TEXT_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_TEXT_DELETE')"
 *          }
 *     })
 * @ApiFilter(DateFilter::class, properties={"createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "title": "ipartial",
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
class TextTranslation
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({
     *     "text_read",
     *     "text_write"
     * })
     */
    private $id;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=128)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Text", inversedBy="translations")
     * @Assert\NotBlank()
     */
    protected $translatable;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language")
     * @Assert\NotBlank()
     * @Groups({
     *     "text_read",
     *     "text_write",
     *     "text_read_frontend"
     * })
     */
    protected $language;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({
     *     "text_read",
     *     "text_write",
     *     "text_read_collection",
     *     "text_read_frontend",
     * })
     * @Assert\NotBlank()
     */
    protected $title = '';

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Groups({
     *     "text_read",
     *     "text_write",
     *     "text_read_frontend",
     * })
     * @Assert\NotBlank()
     */
    protected $content = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({
     *     "text_read",
     *     "text_write",
     *     "text_read_frontend",
     * })
     */
    protected $seoTitle = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({
     *     "text_read",
     *     "text_write",
     *     "text_read_frontend",
     * })
     */
    protected $seoDescription = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({
     *     "text_read",
     *     "text_write",
     *     "text_read_frontend",
     * })
     */
    protected $seoKeywords = '';

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getSeoTitle(): ?string
    {
        return $this->seoTitle;
    }

    public function setSeoTitle(string $seoTitle): self
    {
        $this->seoTitle = $seoTitle;

        return $this;
    }

    public function getSeoDescription(): ?string
    {
        return $this->seoDescription;
    }

    public function setSeoDescription(string $seoDescription): self
    {
        $this->seoDescription = $seoDescription;

        return $this;
    }

    public function getSeoKeywords(): ?string
    {
        return $this->seoKeywords;
    }

    public function setSeoKeywords(string $seoKeywords): self
    {
        $this->seoKeywords = $seoKeywords;

        return $this;
    }

    public function getTranslatable(): ?Text
    {
        return $this->translatable;
    }

    public function setTranslatable(?Text $translatable): self
    {
        $this->translatable = $translatable;

        return $this;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): self
    {
        $this->language = $language;

        return $this;
    }
}
