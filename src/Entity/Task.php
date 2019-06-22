<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use App\Interfaces\ClientInterface;
use App\Interfaces\SearchInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use Gedmo\Mapping\Annotation as Gedmo;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Validator\Constraints as Assert;
use App\Controller\TaskDeadlineAction;

/**
 * Task
 *
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\Loggable
 * @ApiResource(attributes={
 *     "normalization_context"={"groups"={"task_read", "read"}},
 *     "denormalization_context"={"groups"={"task_write"}},
 *     "order"={"id": "DESC"}
 * }, collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_TASK_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_TASK_CREATE')"
 *          },
 *     "deadline"={
 *         "access_control"="is_granted('ROLE_TASK_DEADLINE')",
 *         "method"="GET",
 *         "path"="/tasks/deadline",
 *         "controller"=TaskDeadlineAction::class,
 *         "defaults"={"_api_receive"=false},
 *         "normalization_context"={
 *             "groups"={"task_read", "read"}
 *         }
 *     },
 * },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_TASK_SHOW')"
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_TASK_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_TASK_DELETE')"
 *          }
 *     })
 * @ApiFilter(DateFilter::class, properties={"deadline", "createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "status.id": "exact",
 *     "project.name": "ipartial",
 *     "project.client.name": "ipartial",
 *     "assignee.name": "ipartial",
 *     "name": "ipartial"
 * })
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "status.id",
 *          "project.name",
 *          "project.client.name",
 *          "assignee.name",
 *          "name",
 *          "timeEstimated",
 *          "timeSpent",
 *          "deadline",
 *          "createdAt",
 *          "updatedAt"
 *     }
 * )
 */
class Task implements ClientInterface, SearchInterface
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
     * @Groups({
     *     "task_read",
     *     "user_read",
     *     "project_read",
     *     "project_write"
     * })
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Gedmo\Versioned
     * @Groups({
     *     "task_read",
     *     "task_write",
     *     "user_read",
     *     "project_read",
     *     "project_write"
     * })
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned
     * @Groups({
     *     "task_read",
     *     "task_write",
     *     "user_read",
     *     "project_read"
     * })
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="tasks")
     * @Gedmo\Versioned
     * @Groups({
     *     "task_read",
     *     "task_write",
     *     "user_read"
     * })
     * @Assert\NotBlank()
     */
    private $project;

    /**
     * @ORM\Column(type="date", nullable=false)
     * @Gedmo\Versioned
     * @Groups({
     *     "task_read",
     *     "task_write",
     *     "user_read",
     *     "project_read",
     *     "project_write"
     * })
     * @Assert\NotBlank()
     */
    private $deadline;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tasks")
     * @Gedmo\Versioned
     * @Groups({
     *     "task_read",
     *     "task_write",
     *     "project_read"
     * })
     */
    private $assignee;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TaskStatus")
     * @Gedmo\Versioned
     * @Groups({
     *     "task_read",
     *     "task_write",
     *     "user_read",
     *     "project_read",
     *     "project_write"
     * })
     * @Assert\NotBlank()
     */
    private $status;

    /**
     * Estimated time in minutes
     *
     * @ORM\Column(type="float", nullable=false, options={"default": 0})
     * @Gedmo\Versioned
     * @Groups({
     *     "task_read",
     *     "task_write",
     *     "project_read",
     *     "project_write"
     * })
     */
    private $timeEstimated = 0;

    /**
     * Spent time in minutes
     *
     * @ORM\Column(type="float", nullable=false, options={"default": 0})
     * @Gedmo\Versioned
     * @Groups({
     *     "task_read",
     *     "task_write",
     *     "project_read",
     *     "project_write"
     * })
     */
    private $timeSpent = 0;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $googleEventId;

    /**
     * @return int|null
     */
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

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDeadline(): \DateTime
    {
        return $this->deadline;
    }

    /**
     * @param \DateTime $deadline
     */
    public function setDeadline(\DateTime $deadline): void
    {
        $this->deadline = $deadline;
    }

    /**
     * @return User
     */
    public function getAssignee(): ?User
    {
        return $this->assignee;
    }

    /**
     * @param User $assignee
     */
    public function setAssignee(User $assignee = null): void
    {
        $this->assignee = $assignee;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->getProject()->getClient();
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

    public function getTimeEstimated(): float
    {
        return round($this->timeEstimated / 60, 1);
    }

    public function setTimeEstimated(float $timeEstimated): self
    {
        $this->timeEstimated = (int)($timeEstimated * 60);

        return $this;
    }

    public function getTimeSpent(): float
    {
        return round($this->timeSpent / 60, 1);
    }

    public function setTimeSpent(float $timeSpent): self
    {
        $this->timeSpent = (int)($timeSpent * 60);

        return $this;
    }

    public function getGoogleEventId(): ?string
    {
        return $this->googleEventId;
    }

    public function setGoogleEventId(?string $googleEventId): self
    {
        $this->googleEventId = $googleEventId;

        return $this;
    }
}
