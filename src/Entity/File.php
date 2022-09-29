<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\CreateFileAction;
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
        'get' => ['security' => "is_granted('ROLE_FILE_LIST')"],
        'post' => [
            'security' => "is_granted('ROLE_FILE_CREATE')",
            'method' => 'POST',
            'path' => '/files',
            'controller' => CreateFileAction::class,
            'defaults' => ['_api_receive' => false]
        ],
    ],
    iri: 'https://schema.org/MediaObject',
    itemOperations: [
        'get' => ['security' => "is_granted('ROLE_FILE_SHOW')"],
        'put' => ['security' => "is_granted('ROLE_FILE_UPDATE')"],
        'delete' => ['security' => "is_granted('ROLE_FILE_DELETE')"],
    ],
    attributes: [
        'order' => ['id' => "DESC"],
        'normalization_context' => ['groups' => ["file_read", "read", "is_active_read"]],
        'denormalization_context' => ['groups' => ["file_write", "is_active_write"]],
    ]
)]
#[ORM\Entity]
class File
{
    use Timestampable;
    use Blameable;
    use IsActive;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        "document_read",
        "document_write",
        "project_read",
    ])]
    private ?int $id = null;

    /**
     * @Vich\UploadableField(
     *     mapping="file",
     *     fileNameProperty="contentUrl",
     *     size="size",
     *     mimeType="mimeType",
     *     originalName="originalName"
     * )
     */
    #[Assert\NotNull]
    public ?HttpFile $file = null;

    /**
     * @ApiProperty(iri="http://schema.org/contentUrl")
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        "document_read",
        "document_write",
        "project_read",
    ])]
    public ?string $contentUrl = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        "document_read",
        "project_read",
    ])]
    protected ?string $size = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        "document_read",
        "project_read",
    ])]
    protected ?string $mimeType = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        "document_read",
        "project_read",
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

    public function getFile(): ?HttpFile
    {
        return $this->file;
    }

    public function setFile(?HttpFile $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getContentUrl(): ?string
    {
        return $this->contentUrl;
    }

    public function setContentUrl(?string $contentUrl): void
    {
        $this->contentUrl = $contentUrl;
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

    public function __toString(): string
    {
        return $this->getOriginalName() ?? '';
    }
}
