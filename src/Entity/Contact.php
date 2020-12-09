<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use App\Interfaces\SearchInterface;
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
 * Contact
 *
 * @ORM\Table(name="contact")
 * @ORM\Entity(repositoryClass="App\Repository\ContactRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"contact_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"contact_write", "is_active_write"}},
 *          "order"={"id": "DESC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_CONTACT_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_CONTACT_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_CONTACT_SHOW')"
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_CONTACT_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_CONTACT_DELETE')"
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
class Contact implements SearchInterface
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
     *     "contact_read",
     *     "client_read",
     *     "client_read_collection",
     *     "client_write",
     *     "company_write",
     *     "company_read_collection",
     *     "company_read",
     * })
     */
    private ?int $id = null;
/**
     * @var integer
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Groups({
     *     "contact_read",
     *     "contact_write",
     *     "client_read",
     *     "client_read_collection",
     *     "client_write",
     *     "company_read_collection",
     *     "company_read",
     * })
     * @Assert\NotBlank()
     */
    private int $value;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ContactType")
     * @Groups({
     *     "contact_read",
     *     "contact_write",
     *     "client_read",
     *     "client_read_collection",
     *     "client_write",
     *     "company_read_collection",
     *     "company_read",
     * })
     * @Assert\NotNull()
     */
    private ?ContactType $contactType = null;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Client", mappedBy="contacts")
     * @Groups({
     *     "contact_read",
     *     "contact_write"
     * })
     * @Assert\NotBlank()
     */
    private Collection $clients;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Company", mappedBy="contacts")
     * @Groups({"contact_read", "contact_write"})
     * @Assert\NotBlank()
     */
    private Collection $companies;

    public function __construct()
    {
        $this->clients = new ArrayCollection();
        $this->companies = new ArrayCollection();
    }

    public function __sleep()
    {
        return [];
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
     *
     * @return string
     */
    public function getValue(): ?string
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

    /**
     * @return Collection|Company[]
     */
    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    public function addCompany(Company $company): self
    {
        if (!$this->companies->contains($company)) {
            $this->companies[] = $company;
            $company->addContact($this);
        }

        return $this;
    }

    public function removeCompany(Company $company): self
    {
        if ($this->companies->contains($company)) {
            $this->companies->removeElement($company);
            $company->removeContact($this);
        }

        return $this;
    }
}
