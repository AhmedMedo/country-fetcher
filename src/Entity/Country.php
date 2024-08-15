<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class Country
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['country_list'])]
    private string $uuid;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['country_list'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 100,nullable: true)]
    #[Groups(['country_list'])]
    private string $cca3;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['country_list'])]
    private string $region;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['country_list'])]
    private string $subRegion;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['country_list'])]
    private string $demonym;

    #[ORM\Column(type: 'integer')]
    #[Groups(['country_list'])]
    private int $population;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['country_list'])]
    private bool $independent;

    #[ORM\Column(type: 'string')]
    #[Groups(['country_list'])]
    private string $flag;

    #[ORM\Column(type: 'json')]
    #[Groups(['country_list'])]
    private array $currency = [];

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    public function getSubRegion(): string
    {
        return $this->subRegion;
    }

    public function setSubRegion(string $subRegion): void
    {
        $this->subRegion = $subRegion;
    }

    public function getDemonym(): string
    {
        return $this->demonym;
    }

    public function setDemonym(string $demonym): void
    {
        $this->demonym = $demonym;
    }

    public function getPopulation(): int
    {
        return $this->population;
    }

    public function setPopulation(int $population): void
    {
        $this->population = $population;
    }

    public function getIndependent(): bool
    {
        return $this->independent;
    }

    public function setIndependent(bool $independent): void
    {
        $this->independent = $independent;
    }

    public function getFlag(): string
    {
        return $this->flag;
    }

    public function setFlag(string $flag): void
    {
        $this->flag = $flag;
    }

    public function getCurrency(): array
    {
        return $this->currency;
    }

    public function setCurrency(array $currency): void
    {
        $this->currency = $currency;
    }

    public function getCca3(): string
    {
        return $this->cca3;
    }

    public function setCca3(string $cca3): void
    {
        $this->cca3 = $cca3;
    }
}
