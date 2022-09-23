<?php

namespace App\Interfaces;

use Doctrine\Common\Collections\Collection;

interface TranslatableInterface
{
    public function getTranslations(): Collection;
}
