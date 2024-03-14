<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
class Country
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?array $currencies = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $capital = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $region = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subregion = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $languages = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $latencia = null;

    #[ORM\Column(nullable: true)]
    private ?int $area = null;

    #[ORM\Column(nullable: true)]
    private ?float $population = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $timezone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $continente = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCurrencies(): ?array
    {
        return $this->currencies;
    }

    public function setCurrencies(?array $currencies): static
    {
        $this->currencies = $currencies;

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

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getSubregion(): ?string
    {
        return $this->subregion;
    }

    public function setSubregion(?string $subregion): static
    {
        $this->subregion = $subregion;

        return $this;
    }

    public function getLanguages(): ?array
    {
        return $this->languages;
    }

    public function setLanguages(?array $languages): static
    {
        $this->languages = $languages;

        return $this;
    }

    public function getLatencia(): ?array
    {
        return $this->latencia;
    }

    public function setLatencia(?array $latencia): static
    {
        $this->latencia = $latencia;

        return $this;
    }

    public function getArea(): ?int
    {
        return $this->area;
    }

    public function setArea(?int $area): static
    {
        $this->area = $area;

        return $this;
    }

    public function getPopulation(): ?float
    {
        return $this->population;
    }

    public function setPopulation(?float $population): static
    {
        $this->population = $population;

        return $this;
    }

    public function getTimezone(): ?\DateTimeInterface
    {
        return $this->timezone;
    }

    public function setTimezone(?\DateTimeInterface $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getContinente(): ?string
    {
        return $this->continente;
    }

    public function setContinente(?string $continente): static
    {
        $this->continente = $continente;

        return $this;
    }
}
