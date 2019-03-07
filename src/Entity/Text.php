<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\BlameableEntity;
use App\Traits\SoftDeleteableEntity;
use App\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Controller\Text\TextFrontendGetItemAction;

/**
 * Text
 *
 * @ORM\Table(name="text")
 * @ORM\Entity(repositoryClass="App\Repository\TextRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\Loggable
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"text_read", "read"}},
 *          "denormalization_context"={"groups"={"text_write"}},
 *          "order"={"id": "ASC"}
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
 *          },
 *          "frontendGet"={
 *              "method"="GET",
 *              "path"="/frontend/text/{slug}",
 *              "normalization_context"={
 *                  "groups"={"text_read_frontend"}
 *              },
 *              "controller"=TextFrontendGetItemAction::class,
 *              "defaults"={"_api_receive"=false},
 *          },
 *     },
 *     collectionOperations={
 *          "get"={
 *              "normalization_context"={
 *                  "groups"={"text_read_collection", "read"}
 *              },
 *              "access_control"="is_granted('ROLE_TEXT_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_TEXT_CREATE')"
 *          }
 *     }
 * )
 * @ApiFilter(DateFilter::class, properties={"createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "title": "ipartial",
 *     "slug": "ipartial",
 * })
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "title",
 *          "slug",
 *          "createdAt",
 *          "updatedAt"
 *     }
 * )
 */
class Text
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
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({
     *     "text_read",
     *     "text_read_collection",
     * })
     */
    protected $id;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=128)
     * @Groups({
     *     "text_read",
     *     "text_read_collection",
     * })
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned
     * @Groups({
     *     "text_read",
     *     "text_read_collection",
     *     "text_read_frontend",
     *     "text_write",
     * })
     * @Assert\NotBlank()
     */
    protected $title = '';

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Gedmo\Versioned
     * @Groups({
     *     "text_read",
     *     "text_read_frontend",
     *     "text_write",
     * })
     * @Assert\NotBlank()
     */
    protected $content = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned
     * @Groups({
     *     "text_read",
     *     "text_read_frontend",
     *     "text_write",
     * })
     */
    protected $seoTitle = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned
     * @Groups({
     *     "text_read",
     *     "text_read_frontend",
     *     "text_write",
     * })
     */
    protected $seoDescription = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned
     * @Groups({
     *     "text_read",
     *     "text_read_frontend",
     *     "text_write",
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
}
