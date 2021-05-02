<?php


namespace App\Services\HitBTC;


use Binance\API;
use ccxt\hitbtc;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\RedisTagAwareAdapter;
use Symfony\Component\DomCrawler\Crawler;

class HitTicker
{
    const BTC_PRICE = 'HIT_BTC_PRICE';

    const CACHE_LIFESPAN = '5 minutes';

    const TICKER ='HIT_BTC_TICKER';

    const OFFLINE ='HIT_OFFLINE';
    const WITH_DRAW_FEE ='HIT_WITHDRAW_FEE';
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
     * @var hitbtc
     */
    private $hitbtc;

    /**
     * CacheAdapterService constructor.
     * @param string $key
     * @param string $secret
     * @throws \ccxt\ExchangeError
     */
    public function __construct( string $key, string $secret)
    {

        $this->hitbtc = new hitbtc   (array(
            'apiKey' => $key,
            'secret' => $secret,
        ));




        $client = RedisAdapter::createConnection('redis://localhost:6379');
        $this->cache = new RedisTagAwareAdapter($client);
    }


    public function getTicker()
    {

        //$doge = $this->hitbtc->market('DOGEUSDT');


        $cacheItem = $this->cache->getItem(self::TICKER);
        if (!$cacheItem->isHit()) {
            $ticker = $this->hitbtc->fetch_tickers();
            $expired = \DateInterval::createFromDateString(self::CACHE_LIFESPAN);
            $cacheItem->set($ticker)->expiresAfter($expired);
            $this->cache->save($cacheItem);
        }
        return $cacheItem->get();
    }

    public function getOfflineCatch()
    {

        $cacheItem = $this->cache->getItem(self::WITH_DRAW_FEE);
        if (!$cacheItem->isHit()) {

            $html = file_get_contents('https://hitbtc.com/system-monitor');


            $crawler = new Crawler($html);

            $table = $crawler->filter('table')->filter('tr')->each(function ($tr, $i) {
                return $tr->filter('td')->each(function ($td, $i) {
                    return trim($td->text());
                });
            });


            $data = [];


            foreach ($table as $row) {
                if($row == []){
                    continue;
                }

                if(!isset($row[6])) {
                    continue;
                }
                if (strpos($row[6], 'Offline') !== false) {
                   $data[] = $row[0];
                }


            }
            $expired = \DateInterval::createFromDateString(self::CACHE_LIFESPAN);
            $cacheItem->set($data)->expiresAfter($expired);
            $this->cache->save($cacheItem);
        }
        return $cacheItem->get();




    }

    public function getCacheFees()
    {


        $cacheItem = $this->cache->getItem(self::OFFLINE);
        //if (!$cacheItem->isHit()) {

            $html = file_get_contents('https://withdrawalfees.com/exchanges/hitbtc');


            $crawler = new Crawler($html);

            $table = $crawler->filter('table')->filter('tr')->each(function ($tr, $i) {
                return $tr->filter('td')->each(function ($td, $i) {
                    return trim($td->text());


                });
            });


            $data = [];


            $i =0;
            foreach ($table as $row) {
                if($row == []){
                    continue;
                }




                $data[$row[0]] = $row[1];




            }
            $expired = \DateInterval::createFromDateString(self::CACHE_LIFESPAN);
            $cacheItem->set($data)->expiresAfter($expired);
            $this->cache->save($cacheItem);
       // }

       // dd($data);
        return $cacheItem->get();
    }


//    public function getBTCPrice()
//    {
//        $cacheItem = $this->cache->getItem(self::BTC_PRICE);
//        if (!$cacheItem->isHit()) {
//            $price = $this->api->price( "BTCUSDT" );
//            $expired = \DateInterval::createFromDateString(self::CACHE_LIFESPAN);
//            $cacheItem->set($price)->expiresAfter($expired);
//            $this->cache->save($cacheItem);
//        }
//        return $cacheItem->get();
//    }





}
