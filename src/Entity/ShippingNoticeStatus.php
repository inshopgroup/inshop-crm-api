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
 * ShippingNoticeStatus
 *
 * @ORM\Table(name="shipping_notice_status")
 * @ORM\Entity(repositoryClass="App\Repository\ShippingNoticeStatusRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"shipping_notice_status_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"shipping_notice_status_write", "is_active_write"}},
 *          "order"={"id": "ASC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_SHIPPING_NOTICE_STATUS_LIST')"
 *          },
 *          "post"={
 *              "security"="is_granted('ROLE_SHIPPING_NOTICE_STATUS_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_SHIPPING_NOTICE_STATUS_SHOW')"
 *          },
 *          "put"={
 *              "security"="is_granted('ROLE_SHIPPING_NOTICE_STATUS_UPDATE')"
 *          },
 *          "delete"={
 *              "security"="is_granted('ROLE_SHIPPING_NOTICE_STATUS_DELETE')"
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
class ShippingNoticeStatus
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @Groups({
     *     "shipping_notice_status_read",
     *     "shipping_notice_header_read",
     *     "shipping_notice_header_write",
     *     "shipping_notice_header_read_collection"
     * })
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({
     *     "shipping_notice_status_read",
     *     "shipping_notice_status_write",
     *     "shipping_notice_header_read",
     *     "shipping_notice_header_read_collection"
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
}
