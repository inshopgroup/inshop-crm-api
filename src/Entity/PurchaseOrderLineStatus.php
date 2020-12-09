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
 * PurchaseOrderLineStatus
 *
 * @ORM\Table(name="purchase_order_line_status")
 * @ORM\Entity(repositoryClass="App\Repository\PurchaseOrderLineStatusRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"purchase_order_line_status_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"purchase_order_line_status_write", "is_active_write"}},
 *          "order"={"id": "ASC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_PURCHASE_ORDER_LINE_STATUS_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_PURCHASE_ORDER_LINE_STATUS_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_PURCHASE_ORDER_LINE_STATUS_SHOW')"
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_PURCHASE_ORDER_LINE_STATUS_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_PURCHASE_ORDER_LINE_STATUS_DELETE')"
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
class PurchaseOrderLineStatus
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({
     *     "purchase_order_line_status_read",
     *     "purchase_order_header_read",
     *     "purchase_order_header_write"
     * })
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Groups({
     *     "purchase_order_line_status_read",
     *     "purchase_order_line_status_write",
     *     "purchase_order_header_read"
     * })
     * @Assert\NotBlank()
     */
    private string $name;

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
}
