<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\GeonamesCountryRepository;

#[ApiResource(order: ['geonameId' => 'ASC'])]
#[ORM\Entity(repositoryClass: GeonamesCountryRepository::class)]
class GeonamesCountry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $continent = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $countryCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $capital = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $languages = null;

    #[ORM\Column]
    private ?int $geonameId = null;

    #[ORM\Column(type: Types::FLOAT, precision: 17, scale: 15)]
    private ?float $south = null;

    #[ORM\Column(type: Types::FLOAT, precision: 17, scale: 15)]
    private ?float $north = null;

    #[ORM\Column(type: Types::FLOAT, precision: 17, scale: 15)]
    private ?float $east = null;

    #[ORM\Column(type: Types::FLOAT, precision: 17, scale: 15)]
    private ?float $west = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $isoAlpha3 = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $fipsCode = null;

    #[ORM\Column]
    private ?int $population = null;

    #[ORM\Column(nullable: true)]
    private ?int $isoNumeric = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $areaInSqKm = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $countryName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $continentName = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $currencyCode = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 6, nullable: true)]
    private ?string $lat = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 6, nullable: true)]
    private ?string $lng = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $geojson = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?GeonamesCountryLevel $level = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContinent(): ?string
    {
        return $this->continent;
    }

    public function setContinent(?string $continent): static
    {
        $this->continent = $continent;

        return $this;
    }

    public function getCapital(): ?string
    {
        return $this->capital;
    }

    public function setCapital(?string $capital): static
    {
        $this->capital = $capital;

        return $this;
    }

    public function getLanguages(): ?string
    {
        return $this->languages;
    }

    public function setLanguages(?string $languages): static
    {
        $this->languages = $languages;

        return $this;
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

    public function getSouth(): ?string
    {
        return $this->south;
    }

    public function setSouth(string $south): static
    {
        $this->south = $south;

        return $this;
    }

    public function getNorth(): ?string
    {
        return $this->north;
    }

    public function setNorth(string $north): static
    {
        $this->north = $north;

        return $this;
    }

    public function getEast(): ?string
    {
        return $this->east;
    }

    public function setEast(string $east): static
    {
        $this->east = $east;

        return $this;
    }

    public function getWest(): ?string
    {
        return $this->west;
    }

    public function setWest(string $west): static
    {
        $this->west = $west;

        return $this;
    }

    public function getIsoAlpha3(): ?string
    {
        return $this->isoAlpha3;
    }

    public function setIsoAlpha3(?string $isoAlpha3): static
    {
        $this->isoAlpha3 = $isoAlpha3;

        return $this;
    }

    public function getFipsCode(): ?string
    {
        return $this->fipsCode;
    }

    public function setFipsCode(?string $fipsCode): static
    {
        $this->fipsCode = $fipsCode;

        return $this;
    }

    public function getPopulation(): ?int
    {
        return $this->population;
    }

    public function setPopulation(int $population): static
    {
        $this->population = $population;

        return $this;
    }

    public function getIsoNumeric(): ?int
    {
        return $this->isoNumeric;
    }

    public function setIsoNumeric(?int $isoNumeric): static
    {
        $this->isoNumeric = $isoNumeric;

        return $this;
    }

    public function getAreaInSqKm(): ?string
    {
        return $this->areaInSqKm;
    }

    public function setAreaInSqKm(?string $areaInSqKm): static
    {
        $this->areaInSqKm = $areaInSqKm;

        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): static
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getCountryName(): ?string
    {
        return $this->countryName;
    }

    public function getAdminCode0(): ?string
    {
        return $this->geonameId;
    }

    public function setCountryName(?string $countryName): static
    {
        $this->countryName = $countryName;

        return $this;
    }

    public function getContinentName(): ?string
    {
        return $this->continentName;
    }

    public function setContinentName(?string $continentName): static
    {
        $this->continentName = $continentName;

        return $this;
    }

    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(?string $currencyCode): static
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    public function getLat(): ?string
    {
        return $this->lat;
    }

    public function setLat(string $lat): static
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?string
    {
        return $this->lng;
    }

    public function setLng(?string $lng): static
    {
        $this->lng = $lng;

        return $this;
    }

    public function getGeojson(): ?string
    {
        return $this->geojson;
    }

    public function setGeojson(?string $geojson): static
    {
        $this->geojson = $geojson;

        return $this;
    }

    public function getLevel(): ?GeonamesCountryLevel
    {
        return $this->level;
    }

    public function setLevel(GeonamesCountryLevel $level): static
    {
        $this->level = $level;

        return $this;
    }
}
