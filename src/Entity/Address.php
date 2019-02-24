<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Interfaces\SearchInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\BlameableEntity;
use App\Traits\SoftDeleteableEntity;
use App\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * Address
 *
 * @ORM\Table(name="address")
 * @ORM\Entity(repositoryClass="App\Repository\AddressRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\Loggable
 * @ApiResource(attributes={
 *     "normalization_context"={"groups"={"address_read", "read"}},
 *     "denormalization_context"={"groups"={"address_write"}},
 *     "order"={"id": "DESC"}
 * },
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_ADDRESS_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_ADDRESS_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_ADDRESS_SHOW')"
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_ADDRESS_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_ADDRESS_DELETE')"
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
    /**
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableEntity;

    /**
     * Hook blameable behavior
     * updates createdBy, updatedBy fields
     */
    use BlameableEntity;

    /**
     * Hook SoftDeleteable behavior
     * updates deletedAt field
     */
    use SoftDeleteableEntity;

    /**
     * @var integer
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
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Country")
     * @Gedmo\Versioned
     * @Groups({
     *     "address_read",
     *     "address_write",
     *     "company_read_collection",
     *     "client_read",
     *     "company_read",
     * })
     * @Assert\NotBlank()
     */
    private $country;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\City")
     * @Gedmo\Versioned
     * @Groups({
     *     "client_read",
     *     "address_read",
     *     "address_write",
     *     "company_read_collection",
     *     "company_read",
     * })
     * @Assert\NotBlank()
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     * @Groups({
     *     "address_read",
     *     "address_write",
     *     "client_read",
     *     "company_read",
     * })
     */
    private $region;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     * @Groups({
     *     "address_read",
     *     "address_write",
     *     "client_read",
     *     "company_read",
     * })
     */
    private $district;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     * @Groups({
     *     "address_read",
     *     "address_write",
     *     "company_read_collection",
     *     "client_read",
     *     "company_read",
     * })
     */
    private $postCode;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     * @Groups({
     *     "address_read",
     *     "address_write",
     *     "client_read",
     *     "company_read",
     * })
     * @Assert\NotBlank()
     */
    private $street;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     * @Groups({
     *     "address_read",
     *     "address_write",
     *     "client_read",
     *     "company_read",
     * })
     * @Assert\NotBlank()
     */
    private $building;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     * @Groups({
     *     "address_read",
     *     "address_write",
     *     "client_read",
     *     "company_read",
     * })
     */
    private $apartment;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     * @Groups({
     *     "address_read",
     *     "address_write",
     *     "client_read",
     *     "company_read",
     * })
     */
    private $comment;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Client", mappedBy="addresses")
     * @Groups({
     *     "address_read",
     *     "address_write"
     * })
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $clients;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Company", mappedBy="addresses")
     * @Groups({
     *     "address_read",
     *     "address_write"
     * })
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $companies;

    public function __construct()
    {
        $this->clients = new ArrayCollection();
        $this->companies = new ArrayCollection();
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
     * @return Country
     */
    public function getCountry(): Country
    {
        return $this->country;
    }

    /**
     * @param Country $country
     */
    public function setCountry(?Country $country): void
    {
        $this->country = $country;
    }

    /**
     * @return City
     */
    public function getCity(): City
    {
        return $this->city;
    }

    /**
     * @param City $city
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
     * @param string $region
     */
    public function setRegion(string $region = null): void
    {
        $this->region = $region;
    }

    /**
     * @return string
     */
    public function getDistrict(): ?string
    {
        return $this->district;
    }

    /**
     * @param string $district
     */
    public function setDistrict(string $district = null): void
    {
        $this->district = $district;
    }

    /**
     * @return string
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet(string $street = null): void
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getBuilding(): ?string
    {
        return $this->building;
    }

    /**
     * @param string $building
     */
    public function setBuilding(string $building = null): void
    {
        $this->building = $building;
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
     */
    public function setApartment(string $apartment = null): void
    {
        $this->apartment = $apartment;
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
     */
    public function setComment(string $comment = null): void
    {
        $this->comment = $comment;
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
                $this->getCountry()->getName(),
                $this->getCity()->getName(),
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
