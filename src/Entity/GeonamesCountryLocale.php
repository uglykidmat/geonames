<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\GeonamesCountryLocaleRepository;

#[ApiResource(order: ['geonameId' => 'ASC'])]
#[ORM\Entity(repositoryClass: GeonamesCountryLocaleRepository::class)]
class GeonamesCountryLocale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $geonameId = null;

    #[ORM\Column(length: 255)]
    private ?string $locale = null;

    #[ORM\Column(length: 2)]
    private ?string $countryCode = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isPreferredName = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isShortName = null;

    private EntityManagerInterface $entityManager;

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

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): static
    {
        $this->locale = $locale;

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

    public function getName(): ?string
    {
        if ($translation = $this->entityManager->getRepository(GeonamesTranslation::class)->getOneByGeonameId($this->geonameId)) {
            return $translation->getName();
        }

        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isIsPreferredName(): ?bool
    {
        return $this->isPreferredName;
    }

    public function setIsPreferredName(?bool $isPreferredName): static
    {
        $this->isPreferredName = $isPreferredName;

        return $this;
    }

    public function isIsShortName(): ?bool
    {
        return $this->isShortName;
    }

    public function setIsShortName(?bool $isShortName): static
    {
        $this->isShortName = $isShortName;

        return $this;
    }

    public function isPrefAndShort(): ?bool
    {
        return ($this->isShortName && $this->isPreferredName);
    }
}
