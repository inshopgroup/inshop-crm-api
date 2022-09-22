<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use App\Repository\ClientRepository;
use App\Repository\ContactRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"contact_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"contact_write", "is_active_write"}},
 *          "order"={"id": "DESC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_CONTACT_LIST')"
 *          },
 *          "post"={
 *              "security"="is_granted('ROLE_CONTACT_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_CONTACT_SHOW')"
 *          },
 *          "put"={
 *              "security"="is_granted('ROLE_CONTACT_UPDATE')"
 *          },
 *          "delete"={
 *              "security"="is_granted('ROLE_CONTACT_DELETE')"
 *          }
 *     })
 * @ApiFilter(DateFilter::class, properties={"createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "value": "ipartial",
 *     "contactType.name": "ipartial",
 * })
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "value",
 *          "contactType.name",
 *          "createdAt",
 *          "updatedAt"
 *     }
 * )
 */
#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @Groups({
     *     "contact_read",
     *     "client_read",
     *     "client_read_collection",
     *     "client_write",
     * })
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @Groups({
     *     "contact_read",
     *     "contact_write",
     *     "client_read",
     *     "client_read_collection",
     *     "client_write",
     * })
     * @Assert\NotBlank()
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $value;

    /**
     * @Groups({
     *     "contact_read",
     *     "contact_write",
     *     "client_read",
     *     "client_read_collection",
     *     "client_write",
     * })
     * @Assert\NotNull()
     */
    #[ORM\ManyToOne(targetEntity: ContactType::class)]
    private ?ContactType $contactType = null;

    /**
     * @Groups({
     *     "contact_read",
     *     "contact_write"
     * })
     * @Assert\NotBlank()
     */
    #[ORM\ManyToMany(targetEntity: Client::class, mappedBy: 'contacts')]
    private Collection $clients;

    public function __construct()
    {
        $this->clients = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return Contact
     */
    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Set contactType
     *
     * @param ContactType|null $contactType
     *
     * @return Contact
     */
    public function setContactType(?ContactType $contactType): self
    {
        $this->contactType = $contactType;

        return $this;
    }

    /**
     * Get contactType
     *
     * @return ContactType
     */
    public function getContactType(): ?ContactType
    {
        return $this->contactType;
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
                $this->getValue(),
            ]
        );
    }

    /**
     * @return Collection|Client[]
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): self
    {
        if (!$this->clients->contains($client)) {
            $this->clients[] = $client;
            $client->addContact($this);
        }

        return $this;
    }

    public function removeClient(Client $client): self
    {
        if ($this->clients->contains($client)) {
            $this->clients->removeElement($client);
            $client->removeContact($this);
        }

        return $this;
    }
}
