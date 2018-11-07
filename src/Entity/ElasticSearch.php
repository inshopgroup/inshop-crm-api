<?php

namespace App\Entity;

use App\Interfaces\ElasticInterface;

/**
 * Class SearchModel
 * @package App\Entity
 */
class ElasticSearch implements ElasticInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $iri;

    /**
     * @var int
     */
    private $entityId;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $text;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getIri(): string
    {
        return $this->iri;
    }

    /**
     * @param string $iri
     */
    public function setIri(string $iri): void
    {
        $this->iri = $iri;
    }

    /**
     * @return int
     */
    public function getEntityId(): int
    {
        return $this->entityId;
    }

    /**
     * @param int $entityId
     */
    public function setEntityId(int $entityId): void
    {
        $this->entityId = $entityId;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'iri' => $this->getIri(),
            'entityId' => $this->getEntityId(),
            'type' => $this->getType(),
            'text' => $this->getText(),
        ];
    }
}
