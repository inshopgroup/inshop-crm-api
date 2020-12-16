<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use App\Interfaces\ClientInterface;
use App\Interfaces\SearchInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
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
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"task_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"task_write", "is_active_write"}},
 *          "order"={"id": "DESC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_TASK_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_TASK_CREATE')"
 *          },
 *          "deadline"={
 *              "access_control"="is_granted('ROLE_TASK_DEADLINE')",
 *              "method"="GET",
 *              "path"="/tasks/deadline",
 *              "controller"=TaskDeadlineAction::class,
 *              "defaults"={"_api_receive"=false},
 *              "normalization_context"={
 *                  "groups"={"task_read", "read", "is_active_read"}
 *              }
 *          },
 *     },
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
     * @var int|null
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
    private ?int $id = null;


    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Groups({
     *     "task_read",
     *     "task_write",
     *     "user_read",
     *     "project_read",
     *     "project_write"
     * })
     * @Assert\NotBlank()
     */
    private string $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({
     *     "task_read",
     *     "task_write",
     *     "user_read",
     *     "project_read"
     * })
     */
    private ?string $description = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="tasks")
     * @Groups({
     *     "task_read",
     *     "task_write",
     *     "user_read"
     * })
     * @Assert\NotBlank()
     */
    private ?Project $project = null;

    /**
     * @ORM\Column(type="date", nullable=false)
     * @Groups({
     *     "task_read",
     *     "task_write",
     *     "user_read",
     *     "project_read",
     *     "project_write"
     * })
     * @Assert\NotBlank()
     */
    private DateTime $deadline;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tasks")
     * @Groups({
     *     "task_read",
     *     "task_write",
     *     "project_read"
     * })
     */
    private ?User $assignee = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TaskStatus")
     * @Groups({
     *     "task_read",
     *     "task_write",
     *     "user_read",
     *     "project_read",
     *     "project_write"
     * })
     * @Assert\NotBlank()
     */
    private ?TaskStatus $status = null;

    /**
     * Estimated time in minutes
     *
     * @ORM\Column(type="float", nullable=false, options={"default": 0})
     * @Groups({
     *     "task_read",
     *     "task_write",
     *     "project_read",
     *     "project_write"
     * })
     */
    private float $timeEstimated = 0;

    /**
     * Spent time in minutes
     *
     * @ORM\Column(type="float", nullable=false, options={"default": 0})
     * @Groups({
     *     "task_read",
     *     "task_write",
     *     "project_read",
     *     "project_write"
     * })
     */
    private float $timeSpent = 0;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $googleEventId = null;

    public function __sleep()
    {
        return [];
    }

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

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDeadline(): DateTime
    {
        return $this->deadline;
    }

    /**
     * @param DateTime $deadline
     */
    public function setDeadline(DateTime $deadline): void
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
     * @param User|null $assignee
     * @return Task
     */
    public function setAssignee(?User $assignee): self
    {
        $this->assignee = $assignee;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus(): ?TaskStatus
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     * @return Task
     */
    public function setStatus(?TaskStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Client
     */
    public function getClient(): ?Client
    {
        return $this->getProject() ? $this->getProject()->getClient() : null;
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
