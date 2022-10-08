<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\Client\ClientGetItemAction;
use App\Controller\Client\ClientLoginByTokenCollectionAction;
use App\Controller\Client\ClientPutItemController;
use App\Controller\Client\ClientRemindPasswordCollectionController;
use App\Controller\Client\ClientSignupPostCollectionController;
use App\Interfaces\ClientInterface;
use App\Repository\ClientRepository;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use function bin2hex;
use function random_bytes;

#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ["client_read_collection", "read", "is_active_read"]],
            'security' => "is_granted('ROLE_CLIENT_LIST')"
        ],
        'post' => ['security' => "is_granted('ROLE_CLIENT_CREATE')"],
        'signup' => [
            'method' => 'POST',
            'path' => '/frontend/signup',
            'denormalization_context' => ['groups' => ["signup_collection"]],
            'controller' => ClientSignupPostCollectionController::class,
            'validation_groups' => ['client_signup_frontend'],
            'defaults' => ['_api_receive' => true]
        ],
    ],
    itemOperations: [
        'get' => ['security' => "is_granted('ROLE_CLIENT_SHOW')"],
        'put' => ['security' => "is_granted('ROLE_CLIENT_UPDATE')"],
        'delete' => ['security' => "is_granted('ROLE_CLIENT_DELETE')"],
        'clientGet' => [
            'security' => "is_granted('ROLE_CLIENT')",
            'method' => 'GET',
            'path' => '/frontend/profile/me',
            'normalization_context' => ['groups' => ["client_get_item"]],
            'controller' => ClientGetItemAction::class,
            'defaults' => ['_api_receive' => false]
        ],
        'clientPut' => [
            'security' => "is_granted('ROLE_CLIENT')",
            'method' => 'PUT',
            'path' => '/frontend/profile/me',
            'normalization_context' => ['groups' => ["client_put_item"]],
            'controller' => ClientPutItemController::class,
            'validation_groups' => ['client_put_frontend'],
            'defaults' => ['_api_receive' => true]
        ],
        'loginByToken' => [
            'method' => 'GET',
            'path' => '/frontend/login/{token}',
            'controller' => ClientLoginByTokenCollectionAction::class,
            'defaults' => ['_api_receive' => false]
        ],
        'remindPassword' => [
            'method' => 'POST',
            'path' => '/frontend/remind/password',
            'controller' => ClientRemindPasswordCollectionController::class,
            'defaults' => ['_api_receive' => false]
        ],
    ],
    attributes: [
        'order' => ['id' => "DESC"],
        'normalization_context' => ['groups' => ["client_read", "read", "is_active_read"]],
        'denormalization_context' => ['groups' => ["client_write", "is_active_write"]],
    ]
)]
#[ApiFilter(
    DateFilter::class,
    properties: [
        "createdAt",
        "updatedAt",
    ]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        "id" => "exact",
        "name" => "ipartial",
        "labels.id" => "exact",
        "contacts.value" => "ipartial",
        "description" => "ipartial"
    ]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        "id",
        "name",
        "description",
        "createdAt",
        "updatedAt"
    ]
)]
#[UniqueEntity(fields: ['username'], message: 'User already exists', errorPath: 'username')]
#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client implements ClientInterface, UserInterface, PasswordAuthenticatedUserInterface
{
    use Timestampable;
    use Blameable;
    use IsActive;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        "client_read",
        "client_read_collection",
        "document_read",
        "project_read",
        "document_write",
        "project_write",
        "task_read",
        "contact_read",
        "contact_write",
        "order_header_read",
        "order_header_read_collection",
        "order_header_write",
        "address_read",
        "address_write",
        "client_get_item"
    ])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(
        groups: [
            'Default',
            'client_signup_frontend',
            'client_put_frontend'
        ]
    )]
    #[Groups([
        "client_read",
        "client_read_collection",
        "client_write",
        "document_read",
        "project_read",
        "document_write",
        "task_read",
        "contact_read",
        "order_header_read",
        "order_header_read_collection",
        "client_get_item",
        "client_put_item",
        "signup_collection",
        "address_read",
    ])]
    private string $name;

    #[ApiSubresource]
    #[ORM\ManyToMany(targetEntity: Address::class, inversedBy: 'clients')]
    #[ORM\OrderBy(['id' => 'DESC'])]
    #[Assert\Valid]
    #[Groups([
        "client_read",
        "client_write"
    ])]
    private Collection $addresses;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups([
        "client_read",
        "client_read_collection",
        "client_write"
    ])]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: Label::class)]
    #[ORM\OrderBy(['id' => 'DESC'])]
    #[Groups([
        "client_read",
        "client_read_collection",
        "client_write"
    ])]
    private Collection $labels;

    #[ApiSubresource]
    #[ORM\ManyToMany(targetEntity: Contact::class, inversedBy: 'clients', cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['id' => 'DESC'])]
    #[Assert\Valid]
    #[Groups([
        "client_read",
        "client_read_collection",
        "client_write"
    ])]
    private Collection $contacts;

    #[ApiSubresource]
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Project::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['id' => 'DESC'])]
    #[Assert\Valid]
    #[Groups([
        "document_read",
        "client_read",
        "client_write"
    ])]
    private Collection $projects;

    #[ApiSubresource]
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Document::class, orphanRemoval: true)]
    #[ORM\OrderBy(['id' => 'DESC'])]
    private Collection $documents;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\NotBlank(
        groups: [
            'Default',
            'client_signup_frontend',
            'client_put_frontend'
        ]
    )]
    #[Assert\Email(
        groups: [
            'Default',
            'client_signup_frontend',
            'client_put_frontend'
        ]
    )]
    #[Groups([
        "client_read",
        "client_write",
        "client_get_item",
        "client_put_item",
        "signup_collection",
    ])]
    private string $username;

    #[ORM\Column(type: 'string', length: 64)]
    private string $password;

    #[Assert\NotBlank(
        groups: [
            'client_signup_frontend'
        ]
    )]
    #[Groups([
        "client_write",
        "signup_collection",
    ])]
    private ?string $plainPassword = null;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $token = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?DateTime $tokenCreatedAt = null;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->password = bin2hex(random_bytes(32));
        $this->labels = new ArrayCollection();
    }

    public function getClient(): self
    {
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

    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): self
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses[] = $address;
        }

        return $this;
    }

    public function removeAddress(Address $address): self
    {
        if ($this->addresses->contains($address)) {
            $this->addresses->removeElement($address);
        }

        return $this;
    }

    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->contains($contact)) {
            $this->contacts->removeElement($contact);
        }

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
            $project->setClient($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            // set the owning side to null (unless already changed)
            if ($project->getClient() === $this) {
                $project->setClient(null);
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
            $document->addClient($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
            $document->removeClient($this);
        }

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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

    public function getSalt(): ?string
    {
        return null;
    }

    public function getRoles(): array
    {
        return ['ROLE_CLIENT'];
    }

    public function eraseCredentials(): void
    {
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getTokenCreatedAt(): ?DateTime
    {
        return $this->tokenCreatedAt;
    }

    public function setTokenCreatedAt(?DateTime $tokenCreatedAt): void
    {
        $this->tokenCreatedAt = $tokenCreatedAt;
    }

    public function getLabels(): Collection
    {
        return $this->labels;
    }

    public function addLabel(Label $label): self
    {
        if (!$this->labels->contains($label)) {
            $this->labels[] = $label;
        }

        return $this;
    }

    public function removeLabel(Label $label): self
    {
        if ($this->labels->contains($label)) {
            $this->labels->removeElement($label);
        }

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string)$this->getId();
    }
}
