<?php


namespace App\Services\Polo;


use ccxt\hitbtc;
use ccxt\poloniex;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\RedisTagAwareAdapter;
use Symfony\Component\DomCrawler\Crawler;

class PoloTicker
{
    const BTC_PRICE = 'POLO_BTC_PRICE';

    const CACHE_LIFESPAN = '5 minutes';

    const TICKER ='POLO_BTC_TICKER';

    const OFFLINE ='POLO_OFFLINE';
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
     * @var poloniex
     */
    private $polo;

    /**
     * CacheAdapterService constructor.
     * @param string $key
     * @param string $secret
     * @throws \ccxt\ExchangeError
     */
    public function __construct( string $key, string $secret)
    {


        $this->polo = new poloniex([
            'apiKey' => $key,
            'secret' => $secret,
        ]);





        $client = RedisAdapter::createConnection('redis://localhost:6379');
        $this->cache = new RedisTagAwareAdapter($client);
    }


    public function getTicker()
    {

        //$doge = $this->hitbtc->market('DOGEUSDT');


        $cacheItem = $this->cache->getItem(self::TICKER);
        if (!$cacheItem->isHit()) {
            $ticker = $this->polo->fetch_tickers();
            $expired = \DateInterval::createFromDateString(self::CACHE_LIFESPAN);
            $cacheItem->set($ticker)->expiresAfter($expired);
            $this->cache->save($cacheItem);
        }
        return $cacheItem->get();
    }

    public function getWithdrawFeeList()
    {
        $url ='https://withdrawalfees.com/exchanges/hitbtc';
    }





}
