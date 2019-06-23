<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use Gedmo\Mapping\Annotation as Gedmo;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * Backup
 *
 * @ORM\Table(name="backup")
 * @ORM\Entity(repositoryClass="App\Repository\BackupRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"backup_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"backup_write", "is_active_write"}},
 *          "order"={"id": "DESC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_BACKUP_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_BACKUP_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_BACKUP_SHOW')"
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_BACKUP_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_BACKUP_DELETE')"
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
class Backup
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({
     *     "backup_read",
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Groups({
     *     "backup_read",
     *     "backup_write"
     * })
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({
     *     "backup_read",
     *     "backup_write"
     * })
     */
    protected $notice;

    /**
     * @ORM\Column(type="integer")
     * @Groups({
     *     "backup_read",
     *     "backup_write"
     * })
     */
    protected $size;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BackupType")
     * @Gedmo\Versioned
     * @Groups({
     *     "backup_read",
     *     "backup_write"
     * })
     * @Assert\NotNull()
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BackupStatus")
     * @Gedmo\Versioned
     * @Groups({
     *     "backup_read",
     *     "backup_write"
     * })
     * @Assert\NotNull()
     */
    private $status;

    /**
     * @var string|null
     * @ORM\Column(nullable=true)
     * @ApiProperty(iri="http://schema.org/contentUrl")
     * @Groups({
     *     "backup_read"
     * })
     */
    public $contentUrl;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): BackupType
    {
        return $this->type;
    }

    public function setType(BackupType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStatus(): BackupStatus
    {
        return $this->status;
    }

    public function setStatus(BackupStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNotice(): ?string
    {
        return $this->notice;
    }

    public function setNotice(string $notice): self
    {
        $this->notice = $notice;

        return $this;
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
}
