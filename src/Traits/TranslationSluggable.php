<?php

namespace App\Traits;

use App\Interfaces\TranslatableInterface;
use RuntimeException;
use Symfony\Component\Serializer\Annotation\Groups;

trait TranslationSluggable
{
    public function getTranslation()
    {
        if ($this instanceof TranslatableInterface) {
            foreach ($this->getTranslations() as $translation) {
                if ($translation->getLanguage()->getCode() === 'en') {
                    return $translation;
                }
            }

            return $this->getTranslations()->first();
        }

        throw new RuntimeException('Class should implement TranslatableInterface');
    }

    #[Groups([
        "slug",
    ])]
    public function getSlug(): string
    {
        return $this->getTranslation()->getSlug();
    }
}
