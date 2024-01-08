<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\GeonamesTranslationRepository;
use Symfony\Component\Validator\Constraints as Assert;


#[ApiResource(
    order: ['geonameId' => 'ASC'],
    uriTemplate: '/translation',
    operations: [
        new Get(
            uriTemplate: '/translation/{geonameId}',
            stateless: false,
            requirements: ['geonameId' => '\d+'],
            normalizationContext: ['groups' => ['standard']],
        ),
        new Post(
            uriTemplate: '/translation/{geonameId}',
            stateless: false,
            requirements: ['geonameId' => '\d+'],
        ),
        new Post(
            uriTemplate: '/translation/bulk',
            output: ArrayCollection::class,
            stateless: false,
            validate: false,
        ),
        new Put(
            normalizationContext: ['groups' => ['standard']],
            stateless: false,
            extraProperties: ['standard_put' => false],
        ),
        new Delete(
            uriTemplate: '/translation/{geonameId}',
            stateless: false,
            requirements: ['geonameId' => '\d+'],
            normalizationContext: ['groups' => ['standard']],
        ),
        new GetCollection(
            uriTemplate: '/translations',
            stateless: false,
            normalizationContext: ['groups' => ['standard']],
        )
    ]
)]
#[ORM\Entity(repositoryClass: GeonamesTranslationRepository::class)]
class GeonamesTranslation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Type(
        type: 'integer',
        message: 'The value {{ value }} is not a valid {{ type }}.',
    )]
    #[ApiProperty(identifier: true)]
    private ?int $geonameId = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private ?string $name = null;

    #[ORM\Column(length: 10)]
    private ?string $countryCode = null;

    #[ORM\Column(length: 255)]
    private ?string $fcode = null;

    #[ORM\Column(length: 5)]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
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
