<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use App\Interfaces\TranslatableInterface;
use App\Traits\TranslationSluggable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\Blameable;
use App\Traits\IsActive;
use App\Traits\Timestampable;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Controller\Text\TextFrontendGetItemAction;

/**
 * Text
 *
 * @ORM\Table(name="text")
 * @ORM\Entity(repositoryClass="App\Repository\TextRepository")
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"text_read", "read", "is_active_read", "slug"}},
 *          "denormalization_context"={"groups"={"text_write", "is_active_write"}},
 *          "order"={"id": "ASC"}
 *     },
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('ROLE_TEXT_SHOW')"
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_TEXT_UPDATE')"
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_TEXT_DELETE')"
 *          },
 *          "frontendGet"={
 *              "method"="GET",
 *              "path"="/frontend/text/{slug}",
 *              "normalization_context"={
 *                  "groups"={"text_read_frontend"}
 *              },
 *              "controller"=TextFrontendGetItemAction::class,
 *              "defaults"={"_api_receive"=false},
 *          },
 *     },
 *     collectionOperations={
 *          "get"={
 *              "normalization_context"={
 *                  "groups"={"text_read_collection", "read", "is_active_read"}
 *              },
 *              "access_control"="is_granted('ROLE_TEXT_LIST')"
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_TEXT_CREATE')"
 *          }
 *     }
 * )
 * @ApiFilter(DateFilter::class, properties={"createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "translations.title": "ipartial",
 *     "slug": "ipartial",
 * })
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "translations.title",
 *          "createdAt",
 *          "updatedAt"
 *     }
 * )
 */
class Text implements TranslatableInterface
{
    use Timestampable;
    use Blameable;
    use IsActive;
    use TranslationSluggable;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({
     *     "text_read",
     *     "text_read_collection",
     *     "text_read_frontend",
     * })
     */
    protected ?int $id = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TextTranslation", mappedBy="translatable", cascade={"persist"}, orphanRemoval=true)
     * @Groups({
     *     "text_read",
     *     "text_write",
     *     "text_read_collection",
     *     "text_read_frontend",
     * })
     * @ORM\OrderBy({"id" = "ASC"})
     * @Assert\Valid()
     * @Assert\Count(min=1)
     */
    private Collection $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function __sleep()
    {
        return [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|TextTranslation[]
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(TextTranslation $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation;
            $translation->setTranslatable($this);
        }

        return $this;
    }

    public function removeTranslation(TextTranslation $translation): self
    {
        if ($this->translations->contains($translation)) {
            $this->translations->removeElement($translation);
            // set the owning side to null (unless already changed)
            if ($translation->getTranslatable() === $this) {
                $translation->setTranslatable(null);
            }
        }

        return $this;
    }
}
