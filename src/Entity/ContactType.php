<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use App\Repository\ContactTypeRepository;
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
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"contact_type_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"contact_type_write", "is_active_write"}},
 *          "order"={"id": "ASC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_CONTACT_TYPE_LIST')"
 *          },
 *          "post"={
 *              "security"="is_granted('ROLE_CONTACT_TYPE_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_CONTACT_TYPE_SHOW')"
 *          },
 *          "put"={
 *              "security"="is_granted('ROLE_CONTACT_TYPE_UPDATE')"
 *          },
 *          "delete"={
 *              "security"="is_granted('ROLE_CONTACT_TYPE_DELETE')"
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
#[ORM\Entity(repositoryClass: ContactTypeRepository::class)]
class ContactType
{
    public const TYPE_PHONE = 1;
    public const TYPE_MOBILE = 2;
    public const TYPE_FAX = 3;
    public const TYPE_EMAIL = 4;
    public const TYPE_WWW = 5;

    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @Groups({
     *     "contact_type_read",
     *     "contact_read",
     *     "contact_write",
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
     *     "contact_type_read",
     *     "contact_type_write",
     *     "contact_read",
     *     "client_read",
     *     "client_read_collection",
     * })
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function __toString()
    {
        return $this->getName() ?? '';
    }
}
