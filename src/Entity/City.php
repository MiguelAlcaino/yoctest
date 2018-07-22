<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CityRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(columns={"country_id", "name"})})
 */
class City
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $timezone;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WeatherRecordDaily", mappedBy="city")
     */
    private $weatherRecordDailies;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Country", inversedBy="cities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $country;

    public function __construct()
    {
        $this->weatherRecordDailies = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @return Collection|WeatherRecordDaily[]
     */
    public function getWeatherRecordDailies(): Collection
    {
        return $this->weatherRecordDailies;
    }

    public function addWeatherRecordDaily(WeatherRecordDaily $weatherRecordDaily): self
    {
        if (!$this->weatherRecordDailies->contains($weatherRecordDaily)) {
            $this->weatherRecordDailies[] = $weatherRecordDaily;
            $weatherRecordDaily->setCity($this);
        }

        return $this;
    }

    public function removeWeatherRecordDaily(WeatherRecordDaily $weatherRecordDaily): self
    {
        if ($this->weatherRecordDailies->contains($weatherRecordDaily)) {
            $this->weatherRecordDailies->removeElement($weatherRecordDaily);
            // set the owning side to null (unless already changed)
            if ($weatherRecordDaily->getCity() === $this) {
                $weatherRecordDaily->setCity(null);
            }
        }

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }
}
