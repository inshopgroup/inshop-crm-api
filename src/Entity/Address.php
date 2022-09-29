<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\AddressRepository;
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
        'get' => ['security' => "is_granted('ROLE_ADDRESS_LIST')"],
        'post' => ['security' => "is_granted('ROLE_ADDRESS_CREATE')"],
    ],
    itemOperations: [
        'get' => ['security' => "is_granted('ROLE_ADDRESS_SHOW')"],
        'put' => ['security' => "is_granted('ROLE_ADDRESS_UPDATE')"],
        'delete' => ['security' => "is_granted('ROLE_ADDRESS_DELETE')"],
    ],
    attributes: [
        'order' => ['id' => "DESC"],
        'normalization_context' => ['groups' => ["address_read", "read", "is_active_read"]],
        'denormalization_context' => ['groups' => ["address_write", "is_active_write"]],
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
        "postCode" => "ipartial",
        "country.name" => "ipartial",
        "city.name" => "ipartial",
        "region" => "ipartial",
        "district" => "ipartial",
        "street" => "ipartial",
        "building" => "ipartial",
        "apartment" => "ipartial",
        "comment" => "ipartial",
    ]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        "id",
        "postCode",
        "country.name",
        "city.name",
        "region",
        "district",
        "street",
        "building",
        "apartment",
        "comment",
        "createdAt",
        "updatedAt"
    ]
)]
#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    use Timestampable;
    use Blameable;
    use IsActive;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        "address_read",
        "client_read",
        "client_write",
    ])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[Assert\NotBlank]
    #[Groups([
        "address_read",
        "address_write",
        "client_read",
    ])]
    private ?Country $country = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank]
    #[Groups([
        "address_read",
        "address_write",
        "client_read",
    ])]
    private ?string $city = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        "address_read",
        "address_write",
        "client_read",
    ])]
    private ?string $region = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        "address_read",
        "address_write",
        "client_read",
    ])]
    private ?string $district = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        "address_read",
        "address_write",
        "client_read",
    ])]
    private ?string $postCode = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank]
    #[Groups([
        "address_read",
        "address_write",
        "client_read",
    ])]
    private ?string $street = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank]
    #[Groups([
        "address_read",
        "address_write",
        "client_read",
    ])]
    private ?string $building = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        "address_read",
        "address_write",
        "client_read",
    ])]
    private ?string $apartment = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        "address_read",
        "address_write",
        "client_read",
    ])]
    private ?string $comment = null;

    #[ORM\ManyToMany(targetEntity: Client::class, mappedBy: 'addresses')]
    #[ORM\OrderBy(['id' => 'DESC'])]
    #[Groups([
        "address_read",
        "address_write"
    ])]
    private Collection $clients;

    public function __construct()
    {
        $this->clients = new ArrayCollection();
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

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): void
    {
        $this->country = $country;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getDistrict(): ?string
    {
        return $this->district;
    }

    public function setDistrict(?string $district): self
    {
        $this->district = $district;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getBuilding(): ?string
    {
        return $this->building;
    }

    public function setBuilding(?string $building): self
    {
        $this->building = $building;

        return $this;
    }

    public function getApartment(): ?string
    {
        return $this->apartment;
    }

    public function setApartment(string $apartment): self
    {
        $this->apartment = $apartment;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getSearchText(): string
    {
        return implode(
            ' ',
            [
                $this->getCountry() ? $this->getCountry()->getName() : null,
                $this->getCity(),
                $this->getRegion(),
                $this->getDistrict(),
                $this->getStreet(),
                $this->getPostCode(),
                $this->getBuilding(),
                $this->getApartment(),
            ]
        );
    }

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
