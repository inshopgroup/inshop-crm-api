<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\CreateFileAction;
use App\Traits\IsActive;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File as HttpFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Traits\Blameable;
use App\Traits\Timestampable;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ApiResource(iri="http://schema.org/MediaObject",
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_FILE_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_FILE_CREATE')",
 *              "method"="POST",
 *              "path"="/files",
 *              "controller"=CreateFileAction::class,
 *              "defaults"={"_api_receive"=false},
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_FILE_SHOW')"
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_FILE_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_FILE_DELETE')"
 *          }
 *     })
 * @Vich\Uploadable
 */
class File
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
     *     "document_write",
     *     "document_read",
     *     "project_read"
     * })
     */
    private ?int $id = null;
/**
     * @var HttpFile|null
     * @Assert\NotNull()
     * @Vich\UploadableField(
     *     mapping="file",
     *     fileNameProperty="contentUrl",
     *     size="size",
     *     mimeType="mimeType",
     *     originalName="originalName"
     * )
     */
    public ?HttpFile $file = null;

    /**
     * @var string|null
     * @ORM\Column(nullable=true)
     * @ApiProperty(iri="http://schema.org/contentUrl")
     * @Groups({
     *     "document_write",
     *     "document_read",
     *     "project_read"
     * })
     */
    public ?string $contentUrl = null;

    /**
     * @var string|null
     * @ORM\Column(nullable=true)
     * @Groups({
     *     "document_read",
     *     "project_read"
     * })
     */
    protected ?string $size = null;

    /**
     * @var string|null
     * @ORM\Column(nullable=true)
     * @Groups({
     *     "document_read",
     *     "project_read"
     * })
     */
    protected ?string $mimeType = null;

    /**
     * @var string|null
     * @ORM\Column(nullable=true)
     * @Groups({
     *     "document_read",
     *     "project_read"
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
     * @return File
     * @return File
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return null|HttpFile
     */
    public function getFile(): ?HttpFile
    {
        return $this->file;
    }

    /**
     * @param null|HttpFile $file
     * @return File
     * @return File
     */
    public function setFile(?HttpFile $file): self
    {
        $this->file = $file;

        return $this;
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
     */
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

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getOriginalName() ?? '';
    }
}
