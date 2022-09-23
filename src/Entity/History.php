<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use App\Repository\GroupRepository;
use App\Repository\HistoryRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\History\HistoryGetEntityCollectionAction;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={
 *              "groups"={
 *                  "history_read",
 *              }
 *          },
 *          "order"={
 *              "id": "DESC"
 *          }
 *     },
 *     collectionOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_HISTORY_LIST')"
 *          },
 *          "getEntity"={
 *              "security"="is_granted('ROLE_HISTORY_LIST')",
 *              "method"="GET",
 *              "path"="/histories/{entity}/{entityId}",
 *              "normalization_context"={
 *                  "groups"={"history_get_entity_collection"}
 *              },
 *              "controller"=HistoryGetEntityCollectionAction::class,
 *              "defaults"={"_api_receive"=false},
 *          },
 *     },
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('ROLE_HISTORY_SHOW')"
 *          }
 *     },
 * )
 * @ApiFilter(DateFilter::class, properties={"loggedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "action": "ipartial",
 *     "objectId": "ipartial",
 *     "objectClass": "ipartial",
 *     "username": "ipartial",
 * })
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "action",
 *          "objectId",
 *          "objectClass",
 *          "username",
 *          "loggedAt"
 *     }
 * )
 */
#[ORM\Entity(repositoryClass: HistoryRepository::class)]
class History
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        "history_read",
        "history_get_entity_collection",
    ])]
    protected ?int $id = null;

    #[ORM\Column(type: 'string', length: 8)]
    #[Groups([
        "history_read",
        "history_get_entity_collection",
    ])]
    protected string $action;

    #[ORM\Column(type: 'datetime')]
    #[Groups([
        "history_read",
        "history_get_entity_collection",
    ])]
    protected DateTimeInterface $loggedAt;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    #[Groups([
        "history_read",
        "history_get_entity_collection",
    ])]
    protected ?string $objectId = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups([
        "history_read",
        "history_get_entity_collection",
    ])]
    protected string $objectClass;

    #[ORM\Column(type: 'integer')]
    #[Groups([
        "history_read",
        "history_get_entity_collection",
    ])]
    protected int $version;

    #[ORM\Column(type: 'array', nullable: true)]
    #[Groups([
        "history_read",
        "history_get_entity_collection",
    ])]
    protected ?array $data = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        "history_read",
        "history_get_entity_collection",
    ])]
    protected ?string $username = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getLoggedAt(): \DateTimeInterface
    {
        return $this->loggedAt;
    }

    public function setLoggedAt(): self
    {
        $this->loggedAt = new DateTime();

        return $this;
    }

    public function getObjectId(): ?string
    {
        return $this->objectId;
    }

    public function setObjectId(?string $objectId): self
    {
        $this->objectId = $objectId;

        return $this;
    }

    public function getObjectClass(): string
    {
        return $this->objectClass;
    }

    public function setObjectClass(string $objectClass): self
    {
        $this->objectClass = $objectClass;

        return $this;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function setVersion(int $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(?array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }
}
