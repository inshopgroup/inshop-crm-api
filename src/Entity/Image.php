<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\CreateImageAction;
use App\Repository\GroupRepository;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File as HttpFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @ApiResource(
 *     iri="http://schema.org/MediaObject",
 *     attributes={
 *          "normalization_context"={"groups"={"image_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"image_write", "is_active_write"}},
 *          "order"={"id": "DESC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_IMAGE_LIST')"
 *          },
 *          "post"={
 *              "security"="is_granted('ROLE_USER')",
 *              "method"="POST",
 *              "path"="/images",
 *              "controller"=CreateImageAction::class,
 *              "defaults"={"_api_receive"=false},
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_IMAGE_SHOW')"
 *          },
 *          "put"={
 *              "security"="is_granted('ROLE_IMAGE_UPDATE')"
 *          },
 *          "delete"={
 *              "security"="is_granted('ROLE_IMAGE_DELETE')"
 *          }
 *     })
 * @Vich\Uploadable
 */
#[ORM\Entity]
class Image
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @Groups({
     *     "image_write",
     *     "image_read",
     *     "product_read",
     *     "product_write",
     *     "product_read_frontend_item"
     * })
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var HttpFile|null
     * @Assert\NotNull()
     * @Assert\File(
     *     mimeTypes = {"image/jpeg", "image/gif", "image/png"},
     *     maxSize="5Mi",
     *     mimeTypesMessage = "Wrong file type"
     * )
     * @Vich\UploadableField(
     *     mapping="image",
     *     fileNameProperty="contentUrl",
     *     size="size",
     *     mimeType="mimeType",
     *     originalName="originalName"
     * )
     */
    public ?HttpFile $image = null;

    /**
     * @ApiProperty(iri="http://schema.org/contentUrl")
     * @Groups({
     *     "image_write",
     *     "image_read",
     *     "product_read",
     *     "product_read_frontend_item"
     * })
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public ?string $contentUrl = null;

    /**
     * @Groups({
     *     "image_write",
     *     "image_read",
     *     "product_read",
     *     "product_read_frontend_item"
     * })
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $size = null;

    /**
     * @Groups({
     *     "image_write",
     *     "image_read",
     *     "product_read",
     *     "product_read_frontend_item"
     * })
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $mimeType = null;

    /**
     * @Groups({
     *     "image_write",
     *     "image_read",
     *     "product_read",
     *     "product_read_frontend_item"
     * })
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $originalName = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getImage(): ?HttpFile
    {
        return $this->image;
    }

    public function setImage(?HttpFile $image): void
    {
        $this->image = $image;
    }

    public function getContentUrl(): ?string
    {
        return $this->contentUrl;
    }

    public function setContentUrl(?string $contentUrl): self
    {
        $this->contentUrl = $contentUrl;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): self
    {
        $this->originalName = $originalName;

        return $this;
    }
}
