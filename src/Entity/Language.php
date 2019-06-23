<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use Gedmo\Mapping\Annotation as Gedmo;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * Language
 *
 * @ORM\Table(name="language")
 * @ORM\Entity(repositoryClass="App\Repository\LanguageRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"language_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"language_write", "is_active_write"}},
 *          "order"={"id": "ASC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_LANGUAGE_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_LANGUAGE_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_LANGUAGE_SHOW')"
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_LANGUAGE_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_LANGUAGE_DELETE')"
 *          }
 *     })
 * @ApiFilter(DateFilter::class, properties={"createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "name": "ipartial",
 *     "code": "ipartial",
 * })
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "name",
 *          "code",
 *          "createdAt",
 *          "updatedAt"
 *     }
 * )
 */
class Language
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({
     *     "language_read",
     *     "invoice_header_read",
     *     "invoice_header_write",
     *     "user_read",
     *     "user_write",
     *     "category_read",
     *     "category_write",
     *     "product_read",
     *     "product_write"
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Gedmo\Versioned
     * @Groups({
     *     "language_read",
     *     "language_write",
     *     "invoice_header_read",
     *     "user_read",
     *     "category_read",
     *     "product_read"
     * })
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Gedmo\Versioned
     * @Groups({
     *     "language_read",
     *     "language_write",
     *     "category_read",
     *     "product_read",
     *     "category_read_frontend"
     * })
     * @Assert\NotBlank()
     */
    private $code;

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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
