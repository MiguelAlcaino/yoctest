<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\WeatherRecordDailyRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(columns={"city_id", "datetime"})})
 */
class WeatherRecordDaily
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private $maxTemp;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datetime;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private $temp;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private $minTemp;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\City", inversedBy="weatherRecordDailies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $city;

    public function getId()
    {
        return $this->id;
    }

    public function getMaxTemp()
    {
        return $this->maxTemp;
    }

    public function setMaxTemp($maxTemp): self
    {
        $this->maxTemp = $maxTemp;

        return $this;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getTemp()
    {
        return $this->temp;
    }

    public function setTemp($temp): self
    {
        $this->temp = $temp;

        return $this;
    }

    public function getMinTemp()
    {
        return $this->minTemp;
    }

    public function setMinTemp($minTemp): self
    {
        $this->minTemp = $minTemp;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }
}
