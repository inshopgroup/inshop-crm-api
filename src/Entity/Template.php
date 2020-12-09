<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Interfaces\SearchInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * Document
 *
 * @ORM\Table(name="template")
 * @ORM\Entity(repositoryClass="App\Repository\TemplateRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"template_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"template_write", "is_active_write"}},
 *          "order"={"id": "DESC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_TEMPLATE_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_TEMPLATE_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_TEMPLATE_SHOW')"
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_TEMPLATE_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_TEMPLATE_DELETE')"
 *          }
 *     })
 * @ApiFilter(DateFilter::class, properties={"createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "type.name": "ipartial",
 *     "name": "ipartial"
 * })
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "type.name",
 *          "name",
 *          "createdAt",
 *          "updatedAt"
 *     }
 * )
 */
class Template implements SearchInterface
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
     * @Groups({"template_read"})
     */
    private ?int $id = null;


    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Groups({"template_read", "template_write"})
     * @Assert\NotBlank()
     */
    private string $name;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\File")
     * @ORM\JoinColumn(nullable=true)
     * @ApiProperty(iri="http://schema.org/image")
     * @ApiSubresource()
     * @Groups({"template_read", "template_write"})
     * @ORM\OrderBy({"id" = "DESC"})
     */
    public Collection $files;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TemplateType")
     * @Groups({"template_read", "template_write"})
     * @Assert\NotNull()
     */
    private ?TemplateType $type = null;

    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    public function __sleep()
    {
        return [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
        }

        return $this;
    }

    /**
     * Search text
     *
     * @return string
     */
    public function getSearchText(): string
    {
        return implode(
            ' ',
            [
                $this->getName(),
            ]
        );
    }

    public function getType(): ?TemplateType
    {
        return $this->type;
    }

    public function setType(?TemplateType $type): self
    {
        $this->type = $type;

        return $this;
    }
}
