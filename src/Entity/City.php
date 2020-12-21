<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use App\Interfaces\SearchInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * City
 *
 * @ORM\Table(name="city")
 * @ORM\Entity(repositoryClass="App\Repository\CityRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"city_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"city_write", "is_active_write"}},
 *          "order"={"id": "DESC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_CITY_LIST')"
 *          },
 *          "post"={
 *              "security"="is_granted('ROLE_CITY_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_CITY_SHOW')"
 *          },
 *          "put"={
 *              "security"="is_granted('ROLE_CITY_UPDATE')"
 *          },
 *          "delete"={
 *              "security"="is_granted('ROLE_CITY_DELETE')"
 *          }
 *     })
 * @ApiFilter(DateFilter::class, properties={"createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "name": "ipartial",
 *     "country.name": "ipartial",
 * })
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "name",
 *          "country.name",
 *          "createdAt",
 *          "updatedAt"
 *     }
 * )
 */
class City implements SearchInterface
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({
     *     "city_read",
     *     "address_read",
     *     "country_write",
     *     "country_read",
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
     *     "city_read",
     *     "city_write",
     *     "address_read",
     *     "country_read",
     *     "company_read_collection",
     *     "client_read",
     *     "company_read",
     * })
     * @Assert\NotBlank()
     */
    private string $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Country", inversedBy="cities")
     * @Groups({
     *     "city_read",
     *     "city_write"
     * })
     * @Assert\NotBlank()
     */
    private ?Country $country = null;

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

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

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
                $this->getCountry() ? $this->getCountry()->getName() : null,
            ]
        );
    }
}
