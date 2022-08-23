<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

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
 *              "security"="is_granted('ROLE_COUNTRY_LIST')"
 *          },
 *          "post"={
 *              "security"="is_granted('ROLE_COUNTRY_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_COUNTRY_SHOW')"
 *          },
 *          "put"={
 *              "security"="is_granted('ROLE_COUNTRY_UPDATE')"
 *          },
 *          "delete"={
 *              "security"="is_granted('ROLE_COUNTRY_DELETE')"
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
class Country
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @Groups({
     *     "country_read",
     *     "city_read",
     *     "city_write",
     *     "address_read",
     *     "address_write",
     *     "client_read",
     * })
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({
     *     "country_read",
     *     "country_write",
     *     "city_read",
     *     "address_read",
     *     "client_read",
     * })
     * @Assert\NotBlank()
     */
    private string $name;

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
