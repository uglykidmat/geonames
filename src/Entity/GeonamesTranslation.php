<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\GeonamesTranslationRepository;

#[ApiResource(order: ['geonameId' => 'ASC'])]
#[ORM\Entity(repositoryClass: GeonamesTranslationRepository::class)]
class GeonamesTranslation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $geonameId = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 10)]
    private ?string $countryCode = null;

    #[ORM\Column(length: 255)]
    private ?string $fcode = null;

    #[ORM\Column(length: 5)]
    private ?string $locale = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGeonameId(): ?int
    {
        return $this->geonameId;
    }

    public function setGeonameId(int $geonameId): static
    {
        $this->geonameId = $geonameId;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): static
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getFcode(): ?string
    {
        return $this->fcode;
    }

    public function setFcode(string $fcode): static
    {
        $this->fcode = $fcode;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function toArray(): array
    {
        return [
            "geonameId"   => $this->geonameId,
            "name"        => $this->name,
            "countrycode" => $this->countryCode,
            "fcode"       => $this->fcode,
            "locale"      => $this->locale
        ];
    }
}
