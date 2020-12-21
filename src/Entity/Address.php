<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Interfaces\SearchInterface;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Address
 *
 * @ORM\Table(name="address")
 * @ORM\Entity(repositoryClass="App\Repository\AddressRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"address_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"address_write", "is_active_write"}},
 *          "order"={"id": "DESC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_ADDRESS_LIST')"
 *          },
 *          "post"={
 *              "security"="is_granted('ROLE_ADDRESS_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_ADDRESS_SHOW')"
 *          },
 *          "put"={
 *              "security"="is_granted('ROLE_ADDRESS_UPDATE')"
 *          },
 *          "delete"={
 *              "security"="is_granted('ROLE_ADDRESS_DELETE')"
 *          }
 *     })
 * @ApiFilter(DateFilter::class, properties={"createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "postCode": "ipartial",
 *     "country.name": "ipartial",
 *     "city.name": "ipartial",
 *     "region": "ipartial",
 *     "district": "ipartial",
 *     "street": "ipartial",
 *     "building": "ipartial",
 *     "apartment": "ipartial",
 *     "comment": "ipartial",
 * })
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "postCode",
 *          "country.name",
 *          "city.name",
 *          "region",
 *          "district",
 *          "street",
 *          "building",
 *          "apartment",
 *          "comment",
 *          "createdAt",
 *          "updatedAt"
 *     }
 * )
 */
class Address implements SearchInterface
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({
     *     "address_read",
     *     "company_read",
     *     "company_write",
     *     "client_read",
     *     "client_write",
     *     "company_read_collection"
     * })
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Country")
     * @Groups({
     *     "address_read",
     *     "address_write",
     *     "company_read_collection",
     *     "client_read",
     *     "company_read",
     * })
     * @Assert\NotBlank()
     */
    private ?Country $country = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\City")
     * @Groups({
     *     "client_read",
     *     "address_read",
     *     "address_write",
     *     "company_read_collection",
     *     "company_read",
     * })
     * @Assert\NotBlank()
     */
    private ?City $city = null;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({
     *     "address_read",
     *     "address_write",
     *     "client_read",
     *     "company_read",
     * })
     */
    private ?string $region = null;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({
     *     "address_read",
     *     "address_write",
     *     "client_read",
     *     "company_read",
     * })
     */
    private ?string $district = null;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({
     *     "address_read",
     *     "address_write",
     *     "company_read_collection",
     *     "client_read",
     *     "company_read",
     * })
     */
    private ?string $postCode = null;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({
     *     "address_read",
     *     "address_write",
     *     "client_read",
     *     "company_read",
     * })
     * @Assert\NotBlank()
     */
    private ?string $street = null;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({
     *     "address_read",
     *     "address_write",
     *     "client_read",
     *     "company_read",
     * })
     * @Assert\NotBlank()
     */
    private ?string $building = null;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({
     *     "address_read",
     *     "address_write",
     *     "client_read",
     *     "company_read",
     * })
     */
    private ?string $apartment = null;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({
     *     "address_read",
     *     "address_write",
     *     "client_read",
     *     "company_read",
     * })
     */
    private ?string $comment = null;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Client", mappedBy="addresses")
     * @Groups({
     *     "address_read",
     *     "address_write"
     * })
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private Collection $clients;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Company", mappedBy="addresses")
     * @Groups({
     *     "address_read",
     *     "address_write"
     * })
     * @ORM\OrderBy({"id" = "DESC"})
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
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Address
     * @return Address
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return Country
     */
    public function getCountry(): ?Country
    {
        return $this->country;
    }

    /**
     * @param Country|null $country
     */
    public function setCountry(?Country $country): void
    {
        $this->country = $country;
    }

    /**
     * @return City
     */
    public function getCity(): ?City
    {
        return $this->city;
    }

    /**
     * @param City|null $city
     */
    public function setCity(?City $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getRegion(): ?string
    {
        return $this->region;
    }

    /**
     * @param string|null $region
     * @return Address
     */
    public function setRegion(?string $region): self
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @return string
     */
    public function getDistrict(): ?string
    {
        return $this->district;
    }

    /**
     * @param string|null $district
     * @return Address
     */
    public function setDistrict(?string $district): self
    {
        $this->district = $district;

        return $this;
    }

    /**
     * @return string
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * @param string|null $street
     * @return Address
     */
    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    /**
     * @return string
     */
    public function getBuilding(): ?string
    {
        return $this->building;
    }

    /**
     * @param string|null $building
     * @return Address
     */
    public function setBuilding(?string $building): self
    {
        $this->building = $building;

        return $this;
    }

    /**
     * @return string
     */
    public function getApartment(): ?string
    {
        return $this->apartment;
    }

    /**
     * @param string $apartment
     * @return Address
     * @return Address
     */
    public function setApartment(string $apartment): self
    {
        $this->apartment = $apartment;

        return $this;
    }

    /**
     * @return string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return Address
     * @return Address
     */
    public function setComment(string $comment): self
    {
        $this->comment = $comment;

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
                $this->getCountry() ? $this->getCountry()->getName() : null,
                $this->getCity() ? $this->getCity()->getName() : null,
                $this->getRegion(),
                $this->getDistrict(),
                $this->getStreet(),
                $this->getPostCode(),
                $this->getBuilding(),
                $this->getApartment(),
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
            $client->addAddress($this);
        }

        return $this;
    }

    public function removeClient(Client $client): self
    {
        if ($this->clients->contains($client)) {
            $this->clients->removeElement($client);
            $client->removeAddress($this);
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
            $company->addAddress($this);
        }

        return $this;
    }

    public function removeCompany(Company $company): self
    {
        if ($this->companies->contains($company)) {
            $this->companies->removeElement($company);
            $company->removeAddress($this);
        }

        return $this;
    }

    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    public function setPostCode(?string $postCode): self
    {
        $this->postCode = $postCode;

        return $this;
    }
}
