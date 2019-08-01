<?php

namespace App\Interfaces;

use Doctrine\Common\Collections\Collection;

/**
 * Interface TranslatableInterface
 * @package App\Interfaces
 */
interface TranslatableInterface
{
    /**
     * @return Collection
     */
    public function getTranslations(): Collection;
}
