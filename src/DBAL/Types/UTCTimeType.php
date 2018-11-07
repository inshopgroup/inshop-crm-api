<?php

namespace App\DBAL\Types;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\TimeType;

/**
 * Class UTCTimeType
 * @package AppBundle\DBAL\Types
 */
class UTCTimeType extends TimeType
{
    static private $utc;


    public static function getUtc()
    {
        self::$utc ? self::$utc : self::$utc = new \DateTimeZone('UTC');

        return self::$utc;
    }


    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof \DateTime) {
            $value->setTimezone(self::getUtc());
        }

        if (!($value instanceof \DateTime)) {
            $val = \DateTime::createFromFormat(
                '!'.$platform->getTimeFormatString(),
                $value,
                self::$utc ? self::$utc : self::$utc = new \DateTimeZone('UTC')
            );
            if (!$val) {
                throw ConversionException::conversionFailedFormat(
                    $value,
                    $this->getName(),
                    $platform->getTimeFormatString()
                );
            }
        }

        return $value->format($platform->getTimeFormatString());
    }

    /**
     * {@inheritdoc}
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value instanceof \DateTime) {
            return $value;
        }

        $val = \DateTime::createFromFormat(
            '!'.$platform->getTimeFormatString(),
            $value,
            self::$utc ? self::$utc : self::$utc = new \DateTimeZone('UTC')
        );
        if (!$val) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getTimeFormatString()
            );
        }

        return $val;
    }
}