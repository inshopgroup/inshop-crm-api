<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\User\UserPutItemController;
use App\Controller\User\UserPostCollectionController;
use App\Controller\DashboardAction;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use ApiPlatform\Core\Annotation\ApiResource;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * @UniqueEntity({"username"})
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"user_read", "read", "is_active_read"}},
 *         "denormalization_context"={"groups"={"user_write", "is_active_write"}},
 *         "order"={"id": "DESC"},
 *     },
 *     collectionOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_USER_LIST')"
 *          },
 *          "post"={
 *              "controller"=UserPostCollectionController::class,
 *              "security"="is_granted('ROLE_USER_CREATE')"
 *          },
 *          "dashboard"={
 *              "security"="is_granted('ROLE_USER_DASHBOARD')",
 *              "method"="GET",
 *              "path"="/users/dashboard",
 *              "controller"=DashboardAction::class,
 *              "defaults"={"_api_receive"=false},
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_USER_SHOW')"
 *          },
 *          "put"={
 *              "controller"=UserPutItemController::class,
 *              "security"="is_granted('ROLE_USER_UPDATE')"
 *          },
 *          "delete"={
 *              "security"="is_granted('ROLE_USER_DELETE')"
 *          }
 *     },
 * )
 * @ApiFilter(DateFilter::class, properties={"createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "name": "ipartial",
 *     "email": "ipartial",
 *     "groups.name": "ipartial"
 * })
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "name",
 *          "email",
 *          "groups.name",
 *          "createdAt",
 *          "updatedAt"
 *     }
 * )
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @Groups({
     *     "user_read",
     *     "task_read",
     *     "client_read",
     *     "project_read",
     *     "task_write"
     * })
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @Groups({
     *     "user_read",
     *     "user_write",
     *     "task_read",
     *     "client_read"
     * })
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $username;

    /**
     * @Assert\NotBlank()
     */
    #[ORM\Column(type: 'string', length: 64)]
    private string $password;

    /**
     * @Groups({
     *     "user_write"
     * })
     */
    private ?string $plainPassword = null;

    /**
     * @Groups({
     *     "user_read",
     *     "user_write",
     *     "task_read",
     *     "client_read",
     *     "project_read"
     * })
     * @Assert\NotBlank()
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    /**
     * @Groups({
     *     "user_read",
     *     "user_write",
     *     "task_read",
     *     "client_read"
     * })
     * @Assert\NotBlank()
     */
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $email;

    /**
     * @Groups({
     *     "user_read"
     * })
     * @ORM\OrderBy({"id" = "DESC"})
     */
    #[ORM\OneToMany(mappedBy: 'assignee', targetEntity: Task::class)]
    private Collection $tasks;

    /**
     * @Groups({
     *     "user_read",
     *     "user_write"
     * })
     */
    #[ORM\ManyToMany(targetEntity: Group::class)]
    private Collection $groups;

    /**
     * @Groups({
     *     "user_read",
     *     "user_write"
     * })
     * @Assert\NotNull()
     */
    #[ORM\ManyToOne(targetEntity: Language::class)]
    private ?Language $language = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $isGoogleSyncEnabled = false;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $googleAccessToken = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $googleCalendars = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $googleCalendarId = null;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->groups = new ArrayCollection();
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        $roles[] = ['ROLE_USER'];

        foreach ($this->getGroups() as $group) {
            $roles[] = $group->getRolesArray();
        }

        return array_merge(...$roles);
    }

    public function eraseCredentials(): void
    {
    }

    public function __serialize(): array
    {
        return array(
            $this->id,
            $this->username,
            $this->password,
        );
    }

    public function __unserialize($serialized): void
    {
        [
            $this->id,
            $this->username,
            $this->password,
        ] = unserialize($serialized, ['allowed_classes' => false]);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        $this->email = $username;

        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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
            $task->setAssignee($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);
            // set the owning side to null (unless already changed)
            if ($task->getAssignee() === $this) {
                $task->setAssignee(null);
            }
        }

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
        }

        return $this;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getIsGoogleSyncEnabled(): ?bool
    {
        return $this->isGoogleSyncEnabled;
    }

    public function setIsGoogleSyncEnabled(?bool $isGoogleSyncEnabled): self
    {
        $this->isGoogleSyncEnabled = $isGoogleSyncEnabled;

        return $this;
    }

    public function getGoogleAccessToken(): ?string
    {
        return $this->googleAccessToken;
    }

    public function setGoogleAccessToken(?string $googleAccessToken): self
    {
        $this->googleAccessToken = $googleAccessToken;

        return $this;
    }

    public function getGoogleCalendars(): ?string
    {
        return $this->googleCalendars;
    }

    public function setGoogleCalendars(?string $googleCalendars): self
    {
        $this->googleCalendars = $googleCalendars;

        return $this;
    }

    public function getGoogleCalendarId(): ?string
    {
        return $this->googleCalendarId;
    }

    public function setGoogleCalendarId(?string $googleCalendarId): self
    {
        $this->googleCalendarId = $googleCalendarId;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string)$this->getId();
    }

    public function isIsGoogleSyncEnabled(): ?bool
    {
        return $this->isGoogleSyncEnabled;
    }
}
