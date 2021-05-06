<?php

namespace App\Controller;

use App\Entity\Coin;
use App\Message\CoinIndexNotification;
use App\Repository\CoinRepository;
use App\Services\Binance\BalanceParser;
use App\Services\Binance\PriceParser;
use App\Services\BinanceCache;
use App\Services\HitBTC\HitTicker;
use App\Services\Polo\PoloTicker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BalanceController
 * @package App\Controller
 * @Route("/index")
 */
class IndexController extends AbstractController
{


    /**
     * @Route("/", name="indexed_index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request, EntityManagerInterface $entityManager, CoinRepository $repository)
    {


        $this->call('poloniex', $repository, $entityManager);
        $this->call('bittrex', $repository, $entityManager);
        $this->call('hitbtc', $repository, $entityManager);
        $this->call('binance', $repository, $entityManager);
        $this->call('pancakeswap', $repository, $entityManager);


        return $this->redirectToRoute('coin_deviation');


    }

    /**
     * @Route("/truncate", name="indexed_truncate")
     * @param Request $request
     * @return Response
     */
    public function truncate(EntityManagerInterface $entityManager)
    {

        $connection = $entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();

        $connection->executeUpdate($platform->getTruncateTableSQL('coin'));

        return $this->redirectToRoute('coin_deviation');


    }


    /**
     * @Route("/list", name="index_list")
     * @param Request $request
     * @return Response
     */
    public function list(Request $request, EntityManagerInterface $entityManager, CoinRepository $repository)
    {
        $coins = $repository->findAll();

        return $this->render('list/index.html.twig', [
            'coins' => $coins,
        ]);
    }

    /**
     * @Route("/mapping", name="map")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param CoinRepository $repository
     * @param MessageBusInterface $bus
     * @return Response
     */
    public function mapping(CoinRepository $repository, MessageBusInterface $bus)
    {


//        $slug = 'the-sandbox';
//        exec("curl -v 'https://api.coinmarketcap.com/data-api/v3/cryptocurrency/market-pairs/latest?slug=".$slug."&start=1&limit=1000&category=spot&sort=cmc_rank_advanced'", $output, $retval);
//        //echo "Returned with status $retval and output:\n";
//        $response = (json_decode($output[0]));
//        dd($response->data->marketPairs);


        $coins = $repository->findAll();
        foreach ($coins as $coin) {

            $message = new CoinIndexNotification($coin->getSlug());
            $envelope = new Envelope($message, [
                new DelayStamp(10000)
            ]);
            $bus->dispatch($envelope);

        }

        return $this->redirectToRoute('coin_deviation');
    }

    /**
     * @Route("/get-price/{coin}/{exchange}", name="get_price")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param CoinRepository $repository
     * @param MessageBusInterface $bus
     * @return Response
     */
    public function getCurrentPrice(string $coin, string $exchange, CoinRepository $repository, EntityManagerInterface $entityManager)
    {


        $this->getPrice($entityManager, $repository, $coin, 'binance');
        $this->getPrice($entityManager, $repository, $coin, 'hitbtc');
        $this->getPrice($entityManager, $repository, $coin, 'poloniex');

        return $this->redirectToRoute('coin_deviation');

    }

    private function getPrice(EntityManagerInterface $entityManager, CoinRepository $repository, string $coin, string $exchange)
    {
        $coinObject = $repository->findOneBy(['name' => $coin]);

        switch ($exchange) {
            case 'binance':

                $priceParser = new PriceParser($this->getParameter('BN_KEY'), $this->getParameter('BN_SECRET'));
                $btcPrice = $priceParser->getBTCPrice();
                $bnbPrice = $priceParser->getBNBPrice();
                $ethPrice = $priceParser->getETHPrice();

                $balanceParser = new BalanceParser($this->getParameter('BN_KEY'), $this->getParameter('BN_SECRET'));
                $ticker = $balanceParser->getTicker();


                if (isset($ticker["{$coin}USDT"])) {
                    $price = $ticker["{$coin}USDT"];
                }
                elseif (isset($ticker["{$coin}ETH"])) {
                    $price = floatval($ticker["{$coin}ETH"]) * $ethPrice;
                } elseif (isset($ticker["{$coin}BNB"])) {
                    $price = floatval($ticker["{$coin}BNB"]) * $bnbPrice;
                } elseif (isset($ticker["{$coin}BTC"])) {
                    $price = floatval($ticker["{$coin}BTC"]) * $btcPrice;
                }
                $coinObject->setBinancePrice($price);

                break;
            case 'hitbtc':

                $hitTicker = new HitTicker($this->getParameter('HITBC_KEY'), $this->getParameter('HITBTC_SECRET'));
                $ticker = $hitTicker->getTicker();
                $btcPrice = $ticker['BTC/USDT']['bid'];
                $ethPrice = $ticker['ETH/USDT']['bid'];

                if (isset($ticker["{$coin}/USDT"])) {
                    $price = $ticker["{$coin}/USDT"]['bid'];
                    $coinObject->setHitBtcPrice($price);
                } elseif (isset($ticker["{$coin}/BTC"])) {
                    $price = floatval($ticker["{$coin}/BTC"]['bid']) * $btcPrice;
                    $coinObject->setHitBtcPrice($price);
                } elseif (isset($ticker["{$coin}/ETH"])) {
                    $price = floatval($ticker["{$coin}/ETH"]['bid']) * $ethPrice;
                    $coinObject->setHitBtcPrice($price);
                }

                break;
            case 'poloniex':

                $poloTicker = new PoloTicker($this->getParameter('POLO_KEY'), $this->getParameter('POLO_SECRET'));
                $ticker = $poloTicker->getTicker();


                $btcPrice = $ticker['BTC/USDT']['bid'];
                $ethPrice = $ticker['ETH/USDT']['bid'];

                if (isset($ticker["{$coin}/USDT"])) {
                    $price = $ticker["{$coin}/USDT"]['bid'];
                    $coinObject->setPoloPrice($price);
                } elseif (isset($ticker["{$coin}/BTC"])) {
                    $price = floatval($ticker["{$coin}/BTC"]['bid']) * $btcPrice;
                    $coinObject->setPoloPrice($price);
                } elseif (isset($ticker["{$coin}/ETH"])) {
                    $price = floatval($ticker["{$coin}/ETH"]['bid']) * $ethPrice;
                    $coinObject->setPoloPrice($price);
                }
                break;
        }

        $entityManager->persist($coinObject);
        $entityManager->flush();
    }


    private function call($exchange, $repository, $entityManager)
    {

        $func = '';
        switch ($exchange) {
            case 'poloniex':
                $func = 'setPoloPrice';
                break;
            case 'hitbtc':
                $func = 'setHitBtcPrice';
                break;
            case 'binance':
                $func = 'setBinancePrice';
                break;
            case 'bittrex':
                $func = 'setBittrexPrice';
                break;
            case 'pancakeswap':
                $func = 'setCakePrice';
                break;
        }
        $output = null;
        $retval = null;
        exec("curl -v 'https://web-api.coinmarketcap.com/v1/exchange/market-pairs/latest?aux=num_market_pairs,category,fee_type,market_url,currency_name,currency_slug,effective_liquidity&convert=USD,BTC&limit=500&market_status=active&slug=" . $exchange . "&start=1'", $output, $retval);
        //echo "Returned with status $retval and output:\n";
        $response = (json_decode($output[0]));

        //dd($response->data->market_pairs);
        foreach ($response->data->market_pairs as $item) {
            $lastCoin = $repository->findOneBy(['slug' => $item->market_pair_base->currency_slug]);


            if ($lastCoin) {
                continue;
            } else {
                $coin = new Coin();
                $coin->setName($item->market_pair_base->currency_symbol);
                $coin->setSlug($item->market_pair_base->currency_slug);


                $entityManager->persist($coin);
                $entityManager->flush();
            }
        }

    }

}
