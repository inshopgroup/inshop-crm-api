<?php

namespace App\Entity;

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
 * PaymentType
 *
 * @ORM\Entity(repositoryClass="App\Repository\PaymentTypeRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"payment_type_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"payment_type_write", "is_active_write"}},
 *          "order"={"id": "ASC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_PAYMENT_TYPE_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_PAYMENT_TYPE_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_PAYMENT_TYPE_SHOW')"
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_PAYMENT_TYPE_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_PAYMENT_TYPE_DELETE')"
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
class PaymentType
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
     *     "payment_type_read",
     *     "order_header_read",
     *     "order_header_read_collection"
     * })
     */
    private ?int $id = null;
/**
     * @var integer
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Groups({
     *     "payment_type_read",
     *     "payment_type_write",
     *     "order_header_read",
     *     "order_header_read_collection",
     *     "order_header_write"
     * })
     * @Assert\NotBlank()
     */
    private int $name;

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
     * Set name
     *
     * @param string $name
     *
     * @return PaymentType
     */
    public function setName($name): PaymentType
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }
}
