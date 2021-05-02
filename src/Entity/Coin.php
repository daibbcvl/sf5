<?php

namespace App\Entity;

use App\Repository\CoinRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CoinRepository::class)
 */
class Coin
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="float", nullable=true, name="binance_price")
     */
    private $binancePrice;

    /**
     * @ORM\Column(type="float", nullable=true, name="hit_btc_price")
     */
    private $HitBtcPrice;

    /**
     * @ORM\Column(type="float", nullable=true, name="polo_price")
     */
    private $PoloPrice;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $binanceMeta = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $hitMeta = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $bittrexPrice;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $percentage;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $cakePrice;

    public function getId(): ?int
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getBinancePrice(): ?float
    {
        return $this->binancePrice;
    }

    public function setBinancePrice(?float $binancePrice): self
    {
        $this->binancePrice = $binancePrice;

        return $this;
    }

    public function getHitBtcPrice(): ?float
    {
        return $this->HitBtcPrice;
    }

    public function setHitBtcPrice(?float $HitBtcPrice): self
    {
        $this->HitBtcPrice = $HitBtcPrice;

        return $this;
    }

    public function getPoloPrice(): ?float
    {
        return $this->PoloPrice;
    }

    public function setPoloPrice(?float $PoloPrice): self
    {
        $this->PoloPrice = $PoloPrice;

        return $this;
    }

    public function getBinanceMeta(): ?array
    {
        return $this->binanceMeta;
    }

    public function setBinanceMeta(?array $binanceMeta): self
    {
        $this->binanceMeta = $binanceMeta;

        return $this;
    }

    public function getHitMeta(): ?array
    {
        return $this->hitMeta;
    }

    public function setHitMeta(?array $hitMeta): self
    {
        $this->hitMeta = $hitMeta;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getBittrexPrice(): ?float
    {
        return $this->bittrexPrice;
    }

    public function setBittrexPrice(?float $bittrexPrice): self
    {
        $this->bittrexPrice = $bittrexPrice;

        return $this;
    }

    public function getPercentage(): ?float
    {
        return $this->percentage;
    }

    public function setPercentage(?float $percentage): self
    {
        $this->percentage = $percentage;

        return $this;
    }

    public function getCakePrice(): ?float
    {
        return $this->cakePrice;
    }

    public function setCakePrice(?float $cakePrice): self
    {
        $this->cakePrice = $cakePrice;

        return $this;
    }
}
