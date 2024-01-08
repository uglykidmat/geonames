<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\GeonamesAdministrativeDivisionRepository;

#[ApiResource(
    order: ['geonameId' => 'ASC'],
    uriTemplate: '/administrativedivision',
    operations: [
        new Get(
            uriTemplate: '/administrativedivision/{geonameId}',
            stateless: false,
            requirements: ['geonameId' => '\d+'],
            normalizationContext: ['groups' => ['standard']],
        ),
        new GetCollection(
            uriTemplate: '/administrativedivisions',
            stateless: false,
            normalizationContext: ['groups' => ['standard']],
        )
    ]
)]
#[ORM\Entity(repositoryClass: GeonamesAdministrativeDivisionRepository::class)]
class GeonamesAdministrativeDivision
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $geonameId = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $asciiName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $toponymName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $continentCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cc2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $countryCode = null;

    #[ORM\Column(nullable: true)]
    private ?int $countryId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adminName1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adminName2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adminName3 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adminName4 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adminName5 = null;

    #[ORM\Column(nullable: true)]
    private ?int $adminId1 = null;

    #[ORM\Column(nullable: true)]
    private ?int $adminId2 = null;

    #[ORM\Column(nullable: true)]
    private ?int $adminId3 = null;

    #[ORM\Column(nullable: true)]
    private ?int $adminId4 = null;

    #[ORM\Column(nullable: true)]
    private ?int $adminId5 = null;

    #[ORM\Column(nullable: true)]
    private ?string $adminCode1 = null;

    #[ORM\Column(nullable: true)]
    private ?string $adminCode2 = null;

    #[ORM\Column(nullable: true)]
    private ?string $adminCode3 = null;

    #[ORM\Column(nullable: true)]
    private ?string $adminCode4 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 15, nullable: true)]
    private ?string $lat = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 15, nullable: true)]
    private ?string $lng = null;

    #[ORM\Column(nullable: true)]
    private ?int $population = null;

    #[ORM\Column(nullable: true)]
    private ?int $timezone_gmtOffset = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $timezone_timeZoneId = null;

    #[ORM\Column(nullable: true)]
    private ?int $timezone_dstOffset = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adminTypeName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fcode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $geojson = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $fcl = null;

    #[ORM\Column(nullable: true)]
    private ?int $srtm3 = null;

    #[ORM\Column(nullable: true)]
    private ?int $astergdem = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adminCodeAlt1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adminCodeAlt2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adminCodeAlt3 = null;

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

    public function getAsciiName(): ?string
    {
        return $this->asciiName;
    }

    public function setAsciiName(?string $asciiName): static
    {
        $this->asciiName = $asciiName;

        return $this;
    }

    public function getToponymName(): ?string
    {
        return $this->toponymName;
    }

    public function setToponymName(?string $toponymName): static
    {
        $this->toponymName = $toponymName;

        return $this;
    }

    public function getContinentCode(): ?string
    {
        return $this->continentCode;
    }

    public function setContinentCode(?string $continentCode): static
    {
        $this->continentCode = $continentCode;

        return $this;
    }

    public function getCc2(): ?string
    {
        return $this->cc2;
    }

    public function setCc2(?string $cc2): static
    {
        $this->cc2 = $cc2;

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

    public function getAdminName1(): ?string
    {
        return $this->adminName1;
    }

    public function setAdminName1(?string $adminName1): static
    {
        $this->adminName1 = $adminName1;

        return $this;
    }

    public function getAdminName2(): ?string
    {
        return $this->adminName2;
    }

    public function setAdminName2(?string $adminName2): static
    {
        $this->adminName2 = $adminName2;

        return $this;
    }

    public function getAdminName3(): ?string
    {
        return $this->adminName3;
    }

    public function setAdminName3(?string $adminName3): static
    {
        $this->adminName3 = $adminName3;

        return $this;
    }

    public function getAdminName4(): ?string
    {
        return $this->adminName4;
    }

    public function setAdminName4(?string $adminName4): static
    {
        $this->adminName4 = $adminName4;

        return $this;
    }

    public function getAdminName5(): ?string
    {
        return $this->adminName5;
    }

    public function setAdminName5(?string $adminName5): static
    {
        $this->adminName5 = $adminName5;

        return $this;
    }

    public function getAdminId0(): ?string
    {
        return $this->countryId;
    }

    public function getAdminId1(): ?int
    {
        return $this->adminId1;
    }

    public function setAdminId1(?int $adminId1): static
    {
        $this->adminId1 = $adminId1;

        return $this;
    }

    public function getAdminId2(): ?int
    {
        return $this->adminId2;
    }

    public function setAdminId2(?int $adminId2): static
    {
        $this->adminId2 = $adminId2;

        return $this;
    }

    public function getAdminId3(): ?int
    {
        return $this->adminId3;
    }

    public function setAdminId3(?int $adminId3): static
    {
        $this->adminId3 = $adminId3;

        return $this;
    }

    public function getAdminId4(): ?int
    {
        return $this->adminId4;
    }

    public function setAdminId4(?int $adminId4): static
    {
        $this->adminId4 = $adminId4;

        return $this;
    }

    public function getAdminId5(): ?int
    {
        return $this->adminId5;
    }

    public function setAdminId5(?int $adminId5): static
    {
        $this->adminId5 = $adminId5;

        return $this;
    }

    public function getAdminCode1(): ?string
    {
        return $this->adminCode1;
    }

    public function setAdminCode1(?string $adminCode1): static
    {
        $this->adminCode1 = $adminCode1;

        return $this;
    }

    public function getAdminCode2(): ?string
    {
        return $this->adminCode2;
    }

    public function setAdminCode2(?string $adminCode2): static
    {
        $this->adminCode2 = $adminCode2;

        return $this;
    }

    public function getAdminCode3(): ?string
    {
        return $this->adminCode3;
    }

    public function setAdminCode3(?string $adminCode3): static
    {
        $this->adminCode3 = $adminCode3;

        return $this;
    }

    public function getAdminCode4(): ?string
    {
        return $this->adminCode4;
    }

    public function setAdminCode4(?string $adminCode4): static
    {
        $this->adminCode4 = $adminCode4;

        return $this;
    }

    public function getAdminCodes(bool $altCodes = false): array
    {
        $adminCodes = array(
            $altCodes && isset($this->adminCodeAlt1) ? $this->adminCodeAlt1 : $this->adminCode1,
            $altCodes && isset($this->adminCodeAlt2) ? $this->adminCodeAlt2 : $this->adminCode2,
            $altCodes && isset($this->adminCodeAlt3) ? $this->adminCodeAlt3 : $this->adminCode3,
            $this->adminCode4
        );
        return $adminCodes;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(?string $lat): static
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(?string $lng): static
    {
        $this->lng = $lng;

        return $this;
    }

    public function getPopulation(): ?int
    {
        return $this->population;
    }

    public function setPopulation(?int $population): static
    {
        $this->population = $population;

        return $this;
    }

    public function getTimezoneGmtOffset(): ?string
    {
        return $this->timezone_gmtOffset;
    }

    public function setTimezoneGmtOffset(?string $timezone_gmtOffset): static
    {
        $this->timezone_gmtOffset = $timezone_gmtOffset;

        return $this;
    }

    public function getTimezoneTimeZoneId(): ?string
    {
        return $this->timezone_timeZoneId;
    }

    public function setTimezoneTimeZoneId(?string $timezone_timeZoneId): static
    {
        $this->timezone_timeZoneId = $timezone_timeZoneId;

        return $this;
    }

    public function getTimezoneDstOffset(): ?int
    {
        return $this->timezone_dstOffset;
    }

    public function setTimezoneDstOffset(?int $timezone_dstOffset): static
    {
        $this->timezone_dstOffset = $timezone_dstOffset;

        return $this;
    }

    public function getAdminTypeName(): ?string
    {
        return $this->adminTypeName;
    }

    public function setAdminTypeName(?string $adminTypeName): static
    {
        $this->adminTypeName = $adminTypeName;

        return $this;
    }

    public function getFcode(): ?string
    {
        return $this->fcode;
    }

    public function setFcode(?string $fcode): static
    {
        $this->fcode = $fcode;

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

    public function getCountryId(): ?int
    {
        return $this->countryId;
    }

    public function setCountryId(?int $countryId): static
    {
        $this->countryId = $countryId;

        return $this;
    }

    public function getFcl(): ?string
    {
        return $this->fcl;
    }

    public function setFcl(?string $fcl): static
    {
        $this->fcl = $fcl;

        return $this;
    }

    public function getSrtm3(): ?int
    {
        return $this->srtm3;
    }

    public function setSrtm3(?int $srtm3): static
    {
        $this->srtm3 = $srtm3;

        return $this;
    }

    public function getAstergdem(): ?int
    {
        return $this->astergdem;
    }

    public function setAstergdem(?int $astergdem): static
    {
        $this->astergdem = $astergdem;

        return $this;
    }

    public function getAdminCodeAlt1(): ?string
    {
        return $this->adminCodeAlt1;
    }

    public function setAdminCodeAlt1(?string $adminCodeAlt1): static
    {
        $this->adminCodeAlt1 = $adminCodeAlt1;

        return $this;
    }

    public function getAdminCodeAlt2(): ?string
    {
        return $this->adminCodeAlt2;
    }

    public function setAdminCodeAlt2(?string $adminCodeAlt2): static
    {
        $this->adminCodeAlt2 = $adminCodeAlt2;

        return $this;
    }

    public function getAdminCodeAlt3(): ?string
    {
        return $this->adminCodeAlt3;
    }

    public function setAdminCodeAlt3(?string $adminCodeAlt3): static
    {
        $this->adminCodeAlt3 = $adminCodeAlt3;

        return $this;
    }
}
