<?php


namespace App\Services\Binance;


use Binance\API;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\RedisTagAwareAdapter;

class BalanceParser
{

    const TICKER = 'BINANCE_TICKER';
    const HISTORY = 'HISTORY';
    const FLOOR = 0.000000001;


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
    public function __construct(string $binanceKey, string $binanceSecret)
    {
        //$this->cache = $cache;
        $this->api = new API($binanceKey, $binanceSecret);

        $client = RedisAdapter::createConnection('redis://localhost:6379');
        $this->cache = new RedisTagAwareAdapter($client);
    }


    public function getBalances(float $btcPrice)
    {
        $ticker = $this->getTicker();


        $balances = $this->api->balances($ticker);
        foreach ($balances as $key => $balance) {
            if ($balance['available'] + $balance['onOrder'] < 0.0000000000001) {
                unset($balances[$key]);
            } else {
                $balances[$key]['amount'] = ($balance['available'] + $balance['onOrder']);
                $balances[$key]['price'] = $this->getUsdPrice($key, $ticker);
                $balances[$key]['total'] = floatval($balance['btcTotal']) * $btcPrice;

                if (($balance['available'] + $balance['onOrder'] > 0.0000000000001 && $balance['btcValue'] == 0) || $key == 'TUSD') {
                    $balances[$key]['total'] = floatval(($balance['available'] + $balance['onOrder']) * $this->getUsdPrice($key, $ticker));
                }
                $balances[$key]['bought'] = $this->getBoughtPrice($key, $balance['available'] + $balance['onOrder']);
            }
        }
        return $balances;
    }

    public function getTotalBalance(array $balances): int
    {
        $total = 0;
        foreach ($balances as $key => $balance) {
            $total += $balance['total'];
        }

        return $total;
    }

    public function getTicker()
    {
        $cacheItem = $this->cache->getItem(self::TICKER);
        if (!$cacheItem->isHit()) {
            $ticker = $this->api->prices();
            $expired = \DateInterval::createFromDateString(self::CACHE_LIFESPAN);
            $cacheItem->set($ticker)->expiresAfter($expired);
            $this->cache->save($cacheItem);
        }
        return $cacheItem->get();
    }

    public function getUsdPrice(string $key, array $ticker)
    {
        if ($key == 'USDT') {
            return 1.00;
        }
        if (isset($ticker[$key . 'USDT'])) {
            return $ticker[$key . 'USDT'];
        } else {
            return $ticker[$key . 'BUSD'];
        }
    }

    public function getBoughtPrice(string $key, float $amount)
    {
        switch ($key) {
            case 'TUSD':
            case 'USDT':
            case 'BUSD':
                $price = $amount;
                break;
            case 'XRPDOWN':
                $price = 0;
                break;
            case 'BNB':
                $history = $this->getHistory($key);
                $sum = 0;
                $totalAmount = 0;
                $time = array_column($history, 'time');
                array_multisort($time, SORT_DESC, $history);

                $k = 0;
                for ($i = 0; $i < count($history); $i++) {
                    if ($totalAmount > $amount) {
                        continue;
                    }
                    if ($history[$i]['isBuyer']) {
                        $sum += $history[$i]['qty'] * $history[$i]['price'];
                        $totalAmount += $history[$i]['qty'];
                        $k = $i;
                    }
                }
                if ($totalAmount > $amount) {
                    $sum -= $history[$k]['qty'] * $history[$k]['price'];
                }
                $price = $sum;
                break;

            default:
                $history = $this->getHistory($key);
                $sum = 0;
                $totalAmount = 0;
                $time = array_column($history, 'time');
                array_multisort($time, SORT_DESC, $history);

                foreach ($history as $item) {
                    if ($item['isBuyer']) {
                        $sum += $item['qty'] * $item['price'];
                        $totalAmount += $item['qty'];
                    }
                    $sub = abs($totalAmount - $amount);
                    if ($sub < 0.00001) {
                        break;
                    }
                }

                $price = $sum;
                break;
        }

        return $price;
    }

    public function getHistory(string $key): array
    {

        $cacheItem = $this->cache->getItem(self::HISTORY . $key);
        if (!$cacheItem->isHit()) {
            $history = [];
            try {
                $history = $this->api->history("{$key}USDT");
            } catch (\Exception $exception) {
                $history = $this->api->history("{$key}BUSD");
            }

            $expired = \DateInterval::createFromDateString(self::CACHE_LIFESPAN);
            $cacheItem->set($history)->expiresAfter($expired);
            $this->cache->save($cacheItem);
        }

        return $cacheItem->get();
    }
}
