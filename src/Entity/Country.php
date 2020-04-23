<?php

namespace App\Entity;

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
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * Country
 *
 * @ORM\Table(name="country")
 * @ORM\Entity(repositoryClass="App\Repository\CountryRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"country_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"country_write", "is_active_write"}},
 *          "order"={"id": "DESC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_COUNTRY_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_COUNTRY_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_COUNTRY_SHOW')"
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_COUNTRY_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_COUNTRY_DELETE')"
 *          }
 *     })
 * @ApiFilter(DateFilter::class, properties={"createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "name": "ipartial",
 * })
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "name",
 *          "createdAt",
 *          "updatedAt"
 *     }
 * )
 */
class Country implements SearchInterface
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({
     *     "country_read",
     *     "city_read",
     *     "city_write",
     *     "address_read",
     *     "address_write",
     *     "company_read_collection",
     *     "client_read",
     *     "company_read",
     * })
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Groups({
     *     "country_read",
     *     "country_write",
     *     "city_read",
     *     "address_read",
     *     "company_read_collection",
     *     "client_read",
     *     "company_read",
     * })
     * @Assert\NotBlank()
     */
    private string $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\City", mappedBy="country")
     * @Groups({
     *     "country_read",
     *     "country_write",
     *     "address_read"
     * })
     */
    private Collection $cities;

    public function __construct()
    {
        $this->cities = new ArrayCollection();
    }

    public function __sleep()
    {
        return [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|City[]
     */
    public function getCities(): Collection
    {
        return $this->cities;
    }

    public function addCity(City $city): self
    {
        if (!$this->cities->contains($city)) {
            $this->cities[] = $city;
            $city->setCountry($this);
        }

        return $this;
    }

    public function removeCity(City $city): self
    {
        if ($this->cities->contains($city)) {
            $this->cities->removeElement($city);
            // set the owning side to null (unless already changed)
            if ($city->getCountry() === $this) {
                $city->setCountry(null);
            }
        }

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
                $this->getName(),
            ]
        );
    }
}
