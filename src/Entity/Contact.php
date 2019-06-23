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
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
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
     * @var integer
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
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Gedmo\Versioned
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
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ContactType")
     * @Gedmo\Versioned
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
    private $contactType;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Client", mappedBy="contacts")
     * @Groups({
     *     "contact_read",
     *     "contact_write"
     * })
     * @Assert\NotBlank()
     */
    private $clients;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Company", mappedBy="contacts")
     * @Groups({"contact_read", "contact_write"})
     * @Assert\NotBlank()
     */
    private $companies;

    public function __construct()
    {
        $this->clients = new ArrayCollection();
        $this->companies = new ArrayCollection();
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
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set contactType
     *
     * @param \App\Entity\ContactType $contactType
     *
     * @return Contact
     */
    public function setContactType(\App\Entity\ContactType $contactType = null)
    {
        $this->contactType = $contactType;

        return $this;
    }

    /**
     * Get contactType
     *
     * @return \App\Entity\ContactType
     */
    public function getContactType()
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
