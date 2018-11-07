<?php

namespace App\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\{
    ConversionException, DateTimeType
};

class UTCDateTimeType extends DateTimeType
{
    static private $utc;

    public static function getUtc()
    {
        return self::$utc ?: self::$utc = new \DateTimeZone('UTC');
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof \DateTime) {
            $utc = clone $value;
            $utc->setTimezone(self::getUtc());
            return parent::convertToDatabaseValue($utc, $platform);
        } else {
            return parent::convertToDatabaseValue($value, $platform);
        }
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value || $value instanceof \DateTime) {
            return $value;
        }

        $converted = \DateTime::createFromFormat(
            $platform->getDateTimeFormatString(),
            $value,
            self::$utc ?: self::$utc = new \DateTimeZone('UTC')
        );

        if (!$converted) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getDateTimeFormatString()
            );
        }

        $converted->setTimezone(new \DateTimeZone('Europe/Warsaw'));

        return $converted;
    }
}
