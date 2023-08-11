<?php

namespace App\Entity;

use App\Repository\GeonamesCountryLevelRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GeonamesCountryLevelRepository::class)]
class GeonamesCountryLevel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $countryCode = null;

    #[ORM\Column]
    private ?int $maxLevel = null;

    #[ORM\Column]
    private ?int $usedLevel = null;

    #[ORM\Column]
    private ?int $ADM1 = null;

    #[ORM\Column]
    private ?int $ADM2 = null;

    #[ORM\Column]
    private ?int $ADM3 = null;

    #[ORM\Column]
    private ?int $ADM4 = null;

    #[ORM\Column]
    private ?int $ADM5 = null;

    #[ORM\Column(length: 1)]
    private ?string $done = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMaxLevel(): ?int
    {
        return $this->maxLevel;
    }

    public function setMaxLevel(int $maxLevel): static
    {
        $this->maxLevel = $maxLevel;

        return $this;
    }

    public function getUsedLevel(): ?int
    {
        return $this->usedLevel;
    }

    public function setUsedLevel(int $usedLevel): static
    {
        $this->usedLevel = $usedLevel;

        return $this;
    }

    public function getADM1(): ?int
    {
        return $this->ADM1;
    }

    public function setADM1(int $ADM1): static
    {
        $this->ADM1 = $ADM1;

        return $this;
    }

    public function getADM2(): ?int
    {
        return $this->ADM2;
    }

    public function setADM2(int $ADM2): static
    {
        $this->ADM2 = $ADM2;

        return $this;
    }

    public function getADM3(): ?int
    {
        return $this->ADM3;
    }

    public function setADM3(int $ADM3): static
    {
        $this->ADM3 = $ADM3;

        return $this;
    }

    public function getADM4(): ?int
    {
        return $this->ADM4;
    }

    public function setADM4(int $ADM4): static
    {
        $this->ADM4 = $ADM4;

        return $this;
    }

    public function getADM5(): ?int
    {
        return $this->ADM5;
    }

    public function setADM5(int $ADM5): static
    {
        $this->ADM5 = $ADM5;

        return $this;
    }

    public function getDone(): ?string
    {
        return $this->done;
    }

    public function setDone(string $done): static
    {
        $this->done = $done;

        return $this;
    }

    public function toArray(): array
    {
        return [
            "countrycode" => $this->countryCode,
            "max_level"   => $this->maxLevel,
            "used_level"  => $this->usedLevel,
            "ADM1"        => $this->ADM1,
            "ADM2"        => $this->ADM2,
            "ADM3"        => $this->ADM3,
            "ADM4"        => $this->ADM4,
            "ADM5"        => $this->ADM5,
            "done"        => $this->done
        ];
    }
}
