<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Document
 *
 * @ORM\Table(name="document")
 * @ORM\Entity(repositoryClass="App\Repository\DocumentRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"document_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"document_write", "is_active_write"}},
 *          "order"={"id": "DESC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_DOCUMENT_LIST')"
 *          },
 *          "post"={
 *              "security"="is_granted('ROLE_DOCUMENT_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_DOCUMENT_SHOW')"
 *          },
 *          "put"={
 *              "security"="is_granted('ROLE_DOCUMENT_UPDATE')"
 *          },
 *          "delete"={
 *              "security"="is_granted('ROLE_DOCUMENT_DELETE')"
 *          }
 *     })
 * @ApiFilter(DateFilter::class, properties={"createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "name": "partial",
 *     "client": "partial"
 * })
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "name",
 *          "client",
 *          "createdAt",
 *          "updatedAt"
 *     }
 * )
 */
class Document
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @Groups({"document_read", "project_read", "invoice_header_read", "invoice_header_write", "invoice_header_read"})
     */
    private ?int $id = null;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({"document_read", "document_write", "project_read", "invoice_header_read"})
     * @Assert\NotBlank()
     */
    private string $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="documents")
     * @Groups({"document_read", "document_write"})
     */
    private ?Client $client = null;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Project", mappedBy="documents")
     * @Groups({"document_read", "document_write"})
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private Collection $projects;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\File")
     * @ORM\JoinColumn()
     * @ApiProperty(iri="http://schema.org/image")
     * @ApiSubresource()
     * @Groups({"document_read", "document_write", "project_read"})
     * @ORM\OrderBy({"id" = "DESC"})
     */
    public Collection $files;

    public function __construct()
    {
        $this->companies = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->files = new ArrayCollection();
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

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->addDocument($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            $project->removeDocument($this);
        }

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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }
}
