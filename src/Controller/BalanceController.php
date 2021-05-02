<?php

namespace App\Controller;

use App\Entity\Coin;
use App\Repository\CoinRepository;
use App\Services\Binance\BalanceParser;
use App\Services\Binance\PriceParser;
use App\Services\BinanceCache;
use App\Services\HitBTC\HitTicker;
use App\Services\Polo\PoloTicker;
use ccxt\hitbtc;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BalanceController
 * @package App\Controller
 * @Route("/balance")
 */
class BalanceController extends AbstractController
{


    /**
     * @Route("/", name="balance_index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {


//        $binance = new PriceParser($this->getParameter('BN_KEY'), $this->getParameter('BN_SECRET'));
//
//        $btcPrice = $binance->getBTCPrice();
//        $balanceParser = new BalanceParser($this->getParameter('BN_KEY'), $this->getParameter('BN_SECRET'));
//
//        $balances = $balanceParser->getBalances($btcPrice);
//        $total = $balanceParser->getTotalBalance($balances);


// Your Account SID and Auth Token from twilio.com/console
//        $sid = 'AC10df3403ea902dd67a47d7d0cc1e02d2';
//        $token = '4cc1f54e37428dffc3fe0a4a51e15d5d';
//
//        $twilio = new Client($sid, $token);
//
//        $call = $twilio->calls
//            ->create("+84962738266", // to
//                "+18432790839", // from
//                [
//                    "twiml" => "<Response><Say>Ahoy, World!</Say></Response>"
//                ]
//            );
//
//        print($call->sid);
//
//
//        die;


        // SID
        //SKy2ynzSn6ocSAdhF7q94p1p4qe9WzlIiO
        //SECRET
        //elJxemxuazZCM1dNdWNBMkJCOWl1cnpTaHY2NWFqTk0=


//        $hitbtc = new hitbtc   (array(
//            'apiKey' => '_nzlhzqyFfsEcGLtwoyDn70qx7wPg3j8',
//            'secret' => 'KZ132IEUXnO42FPcxFpqeWXl45wIdmRI',
//        ));
//
//        $kq = $hitbtc->fetch_tickers();
//
//        dd($kq);


//        return $this->render('balance/index.html.twig', [
//            'balances' => $balances,
//            'total' => $total,
//        ]);


    }


    /**
     * @Route("/coin/binance", name="coin_import_binance")
     * @param Request $request
     * @return Response
     */
    public function binance(Request $request, EntityManagerInterface $entityManager, CoinRepository $repository)
    {

        $priceParser = new PriceParser($this->getParameter('BN_KEY'), $this->getParameter('BN_SECRET'));

        $btcPrice = $priceParser->getBTCPrice();

        $ETHPrice = $priceParser->getETHPrice();
        $bnbPrice = $priceParser->getBNBPrice();

        // dd($bnbPrice);

        $balanceParser = new BalanceParser($this->getParameter('BN_KEY'), $this->getParameter('BN_SECRET'));

        $ticker = $balanceParser->getTicker();

        //dd($ticker);

        //dd($ticker);

        foreach ($ticker as $name => $price) {
            $ext = substr($name, -3, 3);


//            if (!in_array($ext, ['USD'])) {
//                continue;
//            }


            if (!in_array($ext, ['BTC', 'ETH','SDT', 'USD', 'BNB'])) {
                continue;
            }

            $name = $this->getName($name);
            if ($ext == 'BTC') {
                $fiatBase = $btcPrice;
            } elseif ($ext == 'ETH') {
                $fiatBase = $ETHPrice;
            } elseif ($ext == 'BNB') {
                $fiatBase = $bnbPrice;
            } else {
                $fiatBase = 1;
            }

            $coin = null;
            $lastCoin = $repository->findOneBy(['name' => $name]);
            if ($lastCoin) {

                $lastCoin->setBinancePrice($price * $fiatBase);
            }


//            else {
//                $coin = new Coin();
//                $coin->setName($name);
//                $coin->setBinancePrice($price * $fiatBase);
//                $entityManager->persist($coin);
//            }

//            if($name =='XEM') {
//                dd($price, $fiatBase, $price);
//            }


            $entityManager->flush();

        }


        dd('done');
    }

    /**
     * @Route("/coin/hitbtc", name="coin_import_hit_btc")
     * @param Request $request
     * @return Response
     */
    public function hitbtc(Request $request, EntityManagerInterface $entityManager, CoinRepository $repository)
    {


        $hitTicker = new HitTicker($this->getParameter('HITBC_KEY'), $this->getParameter('HITBTC_SECRET'));


        $ticker = $hitTicker->getTicker();
        $offlines = $hitTicker->getOfflineCatch();
        $feeList = $hitTicker->getCacheFees();

       //dd($feeList);


        $btcPrice = $ticker['BTC/USDT']['last'];
        $ETHPrice = $ticker['ETH/USDT']['last'];

        // dd($ticker['XEM/USDT']['last']);
        foreach ($ticker as $name => $item) {
            $ext = substr($name, -3, 3);


//            if (!in_array($ext, ['ETH'])) {
//                continue;
//            }


            if (!in_array($ext, ['BTC', 'SDT', 'USD', 'ETH'])) {
                continue;
            }


            if ($ext == 'BTC') {
                $fiatBase = $btcPrice;
            } elseif ($ext == 'ETH') {
                $fiatBase = $ETHPrice;
            } else {
                $fiatBase = 1;
            }


            $name = $this->getName(str_replace('/', '', $name));
//
            if (in_array($name, $offlines)) {
                continue;
            }
            //$fiatBase = $ext =='BTC' ? $btcPrice : 1;
            $coin = null;
            $lastCoin = $repository->findOneBy(['name' => $name]);


            if ($lastCoin) {
                $lastCoin->setHitBtcPrice($item['last'] * $fiatBase);
            }


//            else {
//                $coin = new Coin();
//                $coin->setName($name);
//                // dd($price, $fiatBase);
//                $coin->setHitBtcPrice($item['last'] * $fiatBase);
//                $entityManager->persist($coin);
//            }


            $entityManager->flush();

        }


        dd('done');
    }




    /**
     * @Route("/coin/polo", name="coin_import_polo")
     * @param Request $request
     * @return Response
     */
    public function polo(Request $request, EntityManagerInterface $entityManager, CoinRepository $repository)
    {


        $poloTicker = new PoloTicker($this->getParameter('POLO_KEY'), $this->getParameter('POLO_SECRET'));
        $ticker = $poloTicker->getTicker();

        $btcPrice = $ticker['BTC/USDT']['last'];
        $ETHPrice = $ticker['ETH/USDT']['last'];

        // dd($ticker['XEM/USDT']['last']);
        foreach ($ticker as $name => $item) {
            $ext = substr($name, -3, 3);


//            if (!in_array($ext, ['ETH'])) {
//                continue;
//            }


            if (!in_array($ext, ['BTC', 'SDT', 'USD', 'SDC', 'ETH'])) {
                continue;
            }


            if ($ext == 'BTC') {
                $fiatBase = $btcPrice;
            } elseif ($ext == 'ETH') {
                $fiatBase = $ETHPrice;
            } else {
                $fiatBase = 1;
            }


            $name = $this->getName(str_replace('/', '', $name));


            //$fiatBase = $ext =='BTC' ? $btcPrice : 1;
            $coin = null;
            $lastCoin = $repository->findOneBy(['name' => $name]);


            if ($lastCoin) {
                $lastCoin->setPoloPrice($item['last'] * $fiatBase);
            }

//            else {
//                $coin = new Coin();
//                $coin->setName($name);
//                // dd($price, $fiatBase);
//                $coin->setPoloPrice($item['last'] * $fiatBase);
//                $entityManager->persist($coin);
//            }


            $entityManager->flush();

        }

        dd('Done');
    }


        /**
     * @Route("/deviation", name="coin_deviation")
     * @param Request $request
     * @return Response
     */
    public function deviation(Request $request, EntityManagerInterface $entityManager, CoinRepository $repository)
    {
        $coins = $repository->findAll();
      //  dd($coins);
        $chooseCoins = [];
        /** @var Coin $coin */
        foreach ($coins as $coin) {

            $max = max($coin->getBinancePrice(), $coin->getHitBtcPrice(), $coin->getPoloPrice(), $coin->getBittrexPrice());
            $min = min($coin->getBinancePrice(), $coin->getHitBtcPrice(), $coin->getPoloPrice(), $coin->getBittrexPrice());

            $percentage = $min ==0 ? 0: ($max -$min)/$min *100;
            $coin->setPercentage($percentage);
            if ($percentage > 5) {
                $chooseCoins [] = $coin;
            }

        }

        $entityManager->flush();
        $coins = $chooseCoins;
       // dd($coins);
        //dd($coins);
        return $this->render('deviation/index.html.twig', [
            'coins' => $coins,

        ]);

    }




    private function getName(string $name)
    {


        $base = substr($name, -3, 3);
        $newName = '';
        switch ($base) {
            case 'BTC':
            case 'ETH':
            case 'DAI':
            case 'BNB':
            case 'PAX':
            case 'XRP':
            case 'TRX':
            case 'RUB':
            case 'NGN':
            case 'TRY':
                $newName = substr($name, 0, strlen($name) - 3);
                break;
            case 'SDT':
            case 'USD':
            case 'SDC':
            case 'SDS':
                $newName = substr($name, 0, strlen($name) - 4);
                break;

        }

        return $newName;
    }


}
