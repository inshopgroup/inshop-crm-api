<?php

namespace App\Traits;

use App\Interfaces\TranslatableInterface;
use Exception;
use RuntimeException;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Trait TranslationSluggable
 * @package App\Traits
 */
trait TranslationSluggable
{
    /**
     * @return mixed
     * @throws Exception
     */
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

    /**
     * @return string
     * @throws Exception
     * @Groups({"slug"})
     */
    public function getSlug(): string
    {
        return $this->getTranslation()->getSlug();
    }
}
