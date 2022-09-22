<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use App\Interfaces\ClientInterface;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"project_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"project_write", "is_active_write"}},
 *          "order"={"id": "DESC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_PROJECT_LIST')"
 *          },
 *          "post"={
 *              "security"="is_granted('ROLE_PROJECT_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_PROJECT_SHOW')"
 *          },
 *          "put"={
 *              "security"="is_granted('ROLE_PROJECT_UPDATE')"
 *          },
 *          "delete"={
 *              "security"="is_granted('ROLE_PROJECT_DELETE')"
 *          }
 *     })
 * @ApiFilter(DateFilter::class, properties={"createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "name": "ipartial",
 *     "status.id": "exact",
 *     "type.id": "exact",
 *     "client.name": "ipartial"
 * })
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "name",
 *          "status.id",
 *          "type.id",
 *          "clientname",
 *          "createdAt",
 *          "updatedAt"
 *     }
 * )
 */
#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project implements ClientInterface
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @Groups({"project_read", "user_read", "document_read", "document_write", "task_read", "task_write", "client_read", "client_write"})
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @Groups({"project_read", "project_write", "user_read", "document_read", "document_write", "task_read", "task_write", "client_read", "client_write"})
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $name;

    /**
     * @Groups({"project_read", "project_write", "document_read"})
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    /**
     * @Groups({"project_read", "project_write", "task_read"})
     */
    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'projects')]
    #[Assert\NotBlank]
    private ?Client $client = null;

    /**
     * @Groups({"project_read", "project_write", "document_read", "client_read", "client_write"})
     */
    #[ORM\ManyToOne(targetEntity: ProjectStatus::class)]
    #[Assert\NotBlank]
    private ?ProjectStatus $status = null;

    /**
     * @Groups({"project_read", "project_write", "client_read", "client_write"})
     */
    #[ORM\ManyToOne(targetEntity: ProjectType::class)]
    #[Assert\NotBlank]
    private ?ProjectType $type = null;

    /**
     * @Groups({"project_read", "project_write"})
     */
    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Task::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['id' => 'DESC'])]
    #[Assert\Valid]
    private Collection $tasks;

    /**
     * @Groups({"project_read"})
     */
    #[ORM\ManyToMany(targetEntity: Document::class, inversedBy: 'projects')]
    #[ORM\OrderBy(['id' => 'DESC'])]
    private Collection $documents;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->documents = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getStatus(): ?ProjectStatus
    {
        return $this->status;
    }

    public function setStatus(?ProjectStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setProject($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);
            // set the owning side to null (unless already changed)
            if ($task->getProject() === $this) {
                $task->setProject(null);
            }
        }

        return $this;
    }

    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
        }

        return $this;
    }

    public function getType(): ?ProjectType
    {
        return $this->type;
    }

    public function setType(?ProjectType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSearchText(): string
    {
        return implode(
            ' ',
            [
                $this->getName(),
                $this->getDescription(),
            ]
        );
    }
}
