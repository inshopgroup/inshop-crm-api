<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\ClientRepository;
use App\Repository\DocumentRepository;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
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
#[ORM\Entity(repositoryClass: DocumentRepository::class)]
class Document
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @Groups({"document_read", "project_read", "invoice_header_read", "invoice_header_write", "invoice_header_read"})
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @Groups({"document_read", "document_write", "project_read", "invoice_header_read"})
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $name;

    /**
     * @Groups({"document_read", "document_write"})
     */
    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'documents')]
    private ?Client $client = null;

    /**
     * @Groups({"document_read", "document_write"})
     */
    #[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'documents')]
    #[ORM\OrderBy(['id' => 'DESC'])]
    private Collection $projects;

    /**
     * @ApiProperty(iri="http://schema.org/image")
     * @ApiSubresource()
     * @Groups({"document_read", "document_write", "project_read"})
     */
    #[ORM\ManyToMany(targetEntity: File::class)]
    #[ORM\OrderBy(['id' => 'DESC'])]
    public Collection $files;

    public function __construct()
    {
        $this->companies = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->files = new ArrayCollection();
    }

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
