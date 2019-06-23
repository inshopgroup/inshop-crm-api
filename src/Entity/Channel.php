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
 * Channel
 *
 * @ORM\Table(name="channel")
 * @ORM\Entity(repositoryClass="App\Repository\ChannelRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"channel_read", "read", "is_active_read"}},
 *          "denormalization_context"={"groups"={"channel_write", "is_active_write"}},
 *          "order"={"id": "DESC"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_CHANNEL_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_CHANNEL_CREATE')"
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_CHANNEL_SHOW')"
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_CHANNEL_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_CHANNEL_DELETE')"
 *          }
 *     })
 * @ApiFilter(DateFilter::class, properties={"createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "name": "ipartial",
 *     "currency.name": "ipartial",
 * })
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "name",
 *          "currency.name",
 *          "createdAt",
 *          "updatedAt"
 *     }
 * )
 */
class Channel
{
    use Timestampable;
    use Blameable;
    use IsActive;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({
     *     "channel_read",
     *     "product_sell_price_read",
     *     "product_sell_price_write",
     *     "order_header_read",
     *     "order_header_read_collection",
     *     "order_header_write"
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Gedmo\Versioned
     * @Groups({
     *     "channel_read",
     *     "channel_write",
     *     "product_sell_price_read",
     *     "order_header_read",
     *     "order_header_read_collection"
     * })
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Currency")
     * @Gedmo\Versioned
     * @Groups({
     *     "channel_read",
     *     "channel_write",
     *     "product_sell_price_read",
     *     "order_header_read",
     *     "order_header_read_collection"
     * })
     * @Assert\NotNull()
     */
    private $currency;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     * @Gedmo\Versioned
     * @Groups({
     *     "channel_read",
     *     "channel_write"
     * })
     */
    private $isPublic;

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

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function setCurrency(Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getIsPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->isPublic = $isPublic;

        return $this;
    }
}
