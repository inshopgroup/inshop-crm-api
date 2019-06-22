<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
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
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

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
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({
     *     "template_write",
     *     "template_read",
     *     "document_write",
     *     "document_read",
     *     "project_read"
     * })
     */
    private $id;

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
    public $file;

    /**
     * @var string|null
     * @ORM\Column(nullable=true)
     * @ApiProperty(iri="http://schema.org/contentUrl")
     * @Groups({
     *     "template_write",
     *     "template_read",
     *     "document_write",
     *     "document_read",
     *     "project_read"
     * })
     */
    public $contentUrl;

    /**
     * @var string|null
     * @ORM\Column(nullable=true)
     * @Groups({
     *     "template_read",
     *     "document_read",
     *     "project_read"
     * })
     */
    protected $size;

    /**
     * @var string|null
     * @ORM\Column(nullable=true)
     * @Groups({
     *     "template_read",
     *     "document_read",
     *     "project_read"
     * })
     */
    protected $mimeType;

    /**
     * @var string|null
     * @ORM\Column(nullable=true)
     * @Groups({
     *     "template_read",
     *     "document_read",
     *     "project_read"
     * })
     */
    protected $originalName;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
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
     */
    public function setFile(?HttpFile $file): void
    {
        $this->file = $file;
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

    public function getOriginalName(): string
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
        return $this->getOriginalName();
    }
}
