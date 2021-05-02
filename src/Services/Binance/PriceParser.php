<?php


namespace App\Services\Binance;


use Binance\API;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\RedisTagAwareAdapter;

class PriceParser
{
    const BTC_PRICE = 'BINANCE_BTC_PRICE';
    const ETH_PRICE = 'BINANCE_ETH_PRICE';
    const BINANCE_BNB_PRICE = 'BINANCE_BNB_PRICE';
    const BINANCE_USDT_PRICE = 'BINANCE_USDT_PRICE';
    const BINANCE_BUSD_PRICE = 'BINANCE_BUSD_PRICE';

    const CACHE_LIFESPAN = '5 minutes';
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var API
     */
    private $api;

    /**
     * @var string
     */
    private $binanceKey;

    /**
     * @var string
     */
    private $binanceSecret;

    /**
     * CacheAdapterService constructor.
     * @param string $binanceKey
     * @param string $binanceSecret
     */
    public function __construct( string $binanceKey, string $binanceSecret)
    {

        $this->api = new API($binanceKey, $binanceSecret);

        $client = RedisAdapter::createConnection('redis://localhost:6379');
        $this->cache = new RedisTagAwareAdapter($client);
    }


    public function getBTCPrice()
    {
        $cacheItem = $this->cache->getItem(self::BTC_PRICE);
        if (!$cacheItem->isHit()) {
            $price = $this->api->price( "BTCUSDT" );
            $expired = \DateInterval::createFromDateString(self::CACHE_LIFESPAN);
            $cacheItem->set($price)->expiresAfter($expired);
            $this->cache->save($cacheItem);
        }
        return $cacheItem->get();
    }

    public function getETHPrice()
    {
        $cacheItem = $this->cache->getItem(self::ETH_PRICE);
        if (!$cacheItem->isHit()) {
            $price = $this->api->price( "ETHUSDT" );
            $expired = \DateInterval::createFromDateString(self::CACHE_LIFESPAN);
            $cacheItem->set($price)->expiresAfter($expired);
            $this->cache->save($cacheItem);
        }
        return $cacheItem->get();
    }

    public function getBNBPrice()
    {
        $cacheItem = $this->cache->getItem(self::BINANCE_BNB_PRICE);
        if (!$cacheItem->isHit()) {
            $price = $this->api->price( "BNBUSDT");
            $expired = \DateInterval::createFromDateString(self::CACHE_LIFESPAN);
            $cacheItem->set($price)->expiresAfter($expired);
            $this->cache->save($cacheItem);
        }
        return $cacheItem->get();
    }





}
