<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\ContactRepository;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    collectionOperations: [
        'get' => ['security' => "is_granted('ROLE_CONTACT_LIST')"],
        'post' => ['security' => "is_granted('ROLE_CONTACT_CREATE')"],
    ],
    itemOperations: [
        'get' => ['security' => "is_granted('ROLE_CONTACT_SHOW')"],
        'put' => ['security' => "is_granted('ROLE_CONTACT_UPDATE')"],
        'delete' => ['security' => "is_granted('ROLE_CONTACT_DELETE')"],
    ],
    attributes: [
        'order' => ['id' => "DESC"],
        'normalization_context' => ['groups' => ["contact_read", "read", "is_active_read"]],
        'denormalization_context' => ['groups' => ["contact_write", "is_active_write"]],
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
        "value" => "ipartial",
        "contactType.name" => "ipartial",
    ]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        "id",
        "value",
        "contactType.name",
        "createdAt",
        "updatedAt"
    ]
)]
#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    use Timestampable;
    use Blameable;
    use IsActive;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        "contact_read",
        "client_read",
        "client_read_collection",
        "client_write",
    ])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups([
        "contact_read",
        "contact_write",
        "client_read",
        "client_read_collection",
        "client_write",
    ])]
    private string $value;

    #[ORM\ManyToOne(targetEntity: ContactType::class)]
    #[Assert\NotNull]
    #[Groups([
        "contact_read",
        "contact_write",
        "client_read",
        "client_read_collection",
        "client_write",
    ])]
    private ?ContactType $contactType = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'contacts')]
    #[Assert\NotBlank]
    #[Groups([
        "contact_read",
        "contact_write"
    ])]
    private Client $client;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setContactType(?ContactType $contactType): self
    {
        $this->contactType = $contactType;

        return $this;
    }

    public function getContactType(): ?ContactType
    {
        return $this->contactType;
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
