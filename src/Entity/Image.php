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
 * @Vich\Uploadable
 */
#[ApiResource(
    collectionOperations: [
        'get' => ['security' => "is_granted('ROLE_IMAGE_LIST')"],
        'post' => [
            'security' => "is_granted('ROLE_IMAGE_CREATE')",
            'method' => 'POST',
            'path' => '/images',
            'controller' => CreateImageAction::class,
            'defaults' => ['_api_receive' => false]
        ],
    ],
    iri: 'https://schema.org/MediaObject',
    itemOperations: [
        'get' => ['security' => "is_granted('ROLE_IMAGE_SHOW')"],
        'put' => ['security' => "is_granted('ROLE_IMAGE_UPDATE')"],
        'delete' => ['security' => "is_granted('ROLE_IMAGE_DELETE')"],
    ],
    attributes: [
        'order' => ['id' => "DESC"],
        'normalization_context' => ['groups' => ["image_read", "read", "is_active_read"]],
        'denormalization_context' => ['groups' => ["image_write", "is_active_write"]],
    ]
)]
#[ORM\Entity]
class Image
{
    use Timestampable;
    use Blameable;
    use IsActive;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        "image_write",
        "image_read",
        "product_read",
        "product_write",
        "product_read_frontend_item"
    ])]
    private ?int $id = null;

    /**
     * @Vich\UploadableField(
     *     mapping="image",
     *     fileNameProperty="contentUrl",
     *     size="size",
     *     mimeType="mimeType",
     *     originalName="originalName"
     * )
     */
    #[Assert\NotNull]
    #[Assert\File(
        maxSize: '5Mi',
        mimeTypes: ['image/jpeg', 'image/gif', 'image/png'],
        mimeTypesMessage: 'Wrong file type'
    )]
    public ?HttpFile $image = null;

    #[ApiProperty(iri: 'http://schema.org/contentUrl')]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        "image_write",
        "image_read",
        "product_read",
        "product_read_frontend_item"
    ])]
    public ?string $contentUrl = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        "image_write",
        "image_read",
        "product_read",
        "product_read_frontend_item"
    ])]
    protected ?string $size = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        "image_write",
        "image_read",
        "product_read",
        "product_read_frontend_item"
    ])]
    protected ?string $mimeType = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        "image_write",
        "image_read",
        "product_read",
        "product_read_frontend_item"
    ])]
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
