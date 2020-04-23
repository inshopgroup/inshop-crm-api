<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use App\Interfaces\ClientInterface;
use App\Interfaces\SearchInterface;
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

use function Symfony\Component\String\u;

/**
 * Project
 *
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"project_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"project_write", "is_active_write"}},
 *          "order"={"id": "DESC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_PROJECT_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_PROJECT_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_PROJECT_SHOW')"
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_PROJECT_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_PROJECT_DELETE')"
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
class Project implements ClientInterface, SearchInterface
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"project_read", "user_read", "document_read", "document_write", "task_read", "task_write", "client_read", "client_write"})
     */
    private ?int $id = null;


    /**
     * @var integer
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Groups({"project_read", "project_write", "user_read", "document_read", "document_write", "task_read", "task_write", "client_read", "client_write"})
     * @Assert\NotBlank()
     */
    private int $name;

    /**
     * @var integer
     *
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"project_read", "project_write", "document_read"})
     */
    private ?int $description = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="projects")
     * @Groups({"project_read", "project_write", "task_read"})
     * @Assert\NotBlank()
     */
    private ?Client $client = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProjectStatus")
     * @Groups({"project_read", "project_write", "document_read", "client_read", "client_write"})
     * @Assert\NotBlank()
     */
    private ?ProjectStatus $status = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProjectType")
     * @Groups({"project_read", "project_write", "client_read", "client_write"})
     * @Assert\NotBlank()
     */
    private ?ProjectType $type = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Task", mappedBy="project", cascade={"persist"}, orphanRemoval=true)
     * @Groups({"project_read", "project_write"})
     * @ORM\OrderBy({"id" = "ASC"})
     * @Assert\Valid()
     */
    private Collection $tasks;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Document", inversedBy="projects")
     * @Groups({"project_read"})
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private Collection $documents;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->documents = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    /**
     * @return ProjectStatus
     */
    public function getStatus(): ?ProjectStatus
    {
        return $this->status;
    }

    /**
     * @param ProjectStatus $status
     * @return Project
     */
    public function setStatus(?ProjectStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return Collection|Task[]
     */
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

    /**
     * @return Collection|Document[]
     */
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
                $this->getDescription(),
            ]
        );
    }
}
