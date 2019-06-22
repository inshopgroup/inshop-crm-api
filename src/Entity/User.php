<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\User\UserPutItemController;
use App\Controller\User\UserPostCollectionController;
use App\Controller\DashboardAction;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use Gedmo\Mapping\Annotation as Gedmo;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * User
 *
 * @ORM\Table(name="`user`")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity({"username"})
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\Loggable
 * @ApiResource(
 *     collectionOperations={"get", "post"={"controller"=UserPostCollectionController::class}},
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_USER_SHOW')"
 *          },
 *          "put"={
 *              "controller"=UserPutItemController::class,
 *              "access_control"="is_granted('ROLE_USER_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_USER_DELETE')"
 *          }
 *     },
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_USER_LIST')"
 *          },
 *          "post"={
 *              "controller"=UserPostCollectionController::class,
 *              "access_control"="is_granted('ROLE_USER_CREATE')"
 *          },
 *          "dashboard"={
 *              "access_control"="is_granted('ROLE_USER_DASHBOARD')",
 *              "method"="GET",
 *              "path"="/users/dashboard",
 *              "controller"=DashboardAction::class,
 *              "defaults"={"_api_receive"=false},
 *          }
 *     },
 *     attributes={
 *         "normalization_context"={"groups"={"user_read", "read"}},
 *         "denormalization_context"={"groups"={"user_write"}},
 *         "order"={"id": "DESC"},
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
class User implements \Serializable, UserInterface
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
     *     "user_read",
     *     "task_read",
     *     "client_read",
     *     "project_read",
     *     "task_write"
     * })
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Versioned
     * @Groups({
     *     "user_read",
     *     "user_write",
     *     "task_read",
     *     "client_read"
     * })
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64)
     * @Gedmo\Versioned
     * @Assert\NotBlank()
     */
    private $password;

    /**
     * @var string
     *
     * @Groups({
     *     "user_write"
     * })
     */
    private $plainPassword;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Gedmo\Versioned
     * @Groups({
     *     "user_read",
     *     "user_write",
     *     "task_read",
     *     "client_read",
     *     "project_read"
     * })
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Versioned
     * @Groups({
     *     "user_read",
     *     "user_write",
     *     "task_read",
     *     "client_read"
     * })
     * @Assert\NotBlank()
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Task", mappedBy="assignee")
     * @Groups({
     *     "user_read"
     * })
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $tasks;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Group")
     * @Groups({
     *     "user_read",
     *     "user_write"
     * })
     */
    private $groups;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language")
     * @Gedmo\Versioned
     * @Groups({
     *     "user_read",
     *     "user_write"
     * })
     * @Assert\NotNull()
     */
    private $language;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isGoogleSyncEnabled = false;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $googleAccessToken;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $googleCalendars;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $googleCalendarId;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->groups = new ArrayCollection();
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getSalt()
    {
        return null;
    }

    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        $roles = ['ROLE_USER'];

        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRolesArray());
        }

        return $roles;
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
        ) = unserialize($serialized, ['allowed_classes' => false]);
    }

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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
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

    /**
     * @return string
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword(string $plainPassword = null): void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return Collection|Group[]
     */
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

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function setLanguage(Language $language): self
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
}
