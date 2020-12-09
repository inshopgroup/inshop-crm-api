<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\CreateImageAction;
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
 *              "access_control"="is_granted('ROLE_IMAGE_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_USER')",
 *              "method"="POST",
 *              "path"="/images",
 *              "controller"=CreateImageAction::class,
 *              "defaults"={"_api_receive"=false},
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_IMAGE_SHOW')"
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_IMAGE_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_IMAGE_DELETE')"
 *          }
 *     })
 * @Vich\Uploadable
 */
class Image
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({
     *     "image_write",
     *     "image_read",
     *     "product_read",
     *     "product_write",
     *     "product_read_frontend_item"
     * })
     */
    private ?int $id = null;

    /**
     * @var HttpFile|null
     * @Assert\NotNull()
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
     * @var string|null
     * @ORM\Column(nullable=true)
     * @ApiProperty(iri="http://schema.org/contentUrl")
     * @Groups({
     *     "image_write",
     *     "image_read",
     *     "product_read",
     *     "product_read_frontend_item"
     * })
     */
    public ?string $contentUrl = null;

    /**
     * @var string|null
     * @ORM\Column(nullable=true)
     * @Groups({
     *     "image_write",
     *     "image_read",
     *     "product_read",
     *     "product_read_frontend_item"
     * })
     */
    protected ?string $size = null;

    /**
     * @var string|null
     * @ORM\Column(nullable=true)
     * @Groups({
     *     "image_write",
     *     "image_read",
     *     "product_read",
     *     "product_read_frontend_item"
     * })
     */
    protected ?string $mimeType = null;

    /**
     * @var string|null
     * @ORM\Column(nullable=true)
     * @Groups({
     *     "image_write",
     *     "image_read",
     *     "product_read",
     *     "product_read_frontend_item"
     * })
     */
    protected ?string $originalName = null;

    public function __sleep()
    {
        return [];
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Image
     * @return Image
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return null|HttpFile
     */
    public function getImage(): ?HttpFile
    {
        return $this->image;
    }

    /**
     * @param null|HttpFile $image
     */
    public function setImage(?HttpFile $image): void
    {
        $this->image = $image;
    }

    /**
     * @return null|string
     */
    public function getContentUrl(): ?string
    {
        return $this->contentUrl;
    }

    /**
     * @param null|string $contentUrl
     * @return Image
     * @return Image
     */
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
