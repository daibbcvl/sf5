<?php


namespace App\MessageHandler;


use App\Message\CoinIndexNotification;
use App\Repository\CoinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CoinIndexHandler implements MessageHandlerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * @var CoinRepository
     */
    private $repository;

    /**
     * CoinIndexHandler constructor.
     * @param EntityManagerInterface $entityManager
     * @param CoinRepository $repository
     */
    public function __construct(EntityManagerInterface $entityManager, CoinRepository $repository)
    {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }


    public function __invoke(CoinIndexNotification $message)
    {
        $slug = $message->getSlug();
        $coin = $this->repository->findOneBy(['slug' => $slug]);


        exec("curl -v 'https://api.coinmarketcap.com/data-api/v3/cryptocurrency/market-pairs/latest?slug=" . $slug . "&start=1&limit=1000&category=spot&sort=cmc_rank_advanced'", $output, $retval);
        //echo "Returned with status $retval and output:\n";
        $response = (json_decode($output[0]));
        //dd($response->data->marketPairs[0]->exchangeSlug, $response->data->marketPairs[0]->price);

        foreach ($response->data->marketPairs as $exchange) {

//            if($exchange->volumeUsd  <= 100  && $exchange->depthUsdNegativeTwo <=100 ) {
//                continue;
//            }
            if ($exchange->exchangeSlug == 'binance') {
                $coin->setBinancePrice($exchange->price);
            }
            if ($exchange->exchangeSlug == 'poloniex') {
                $coin->setPoloPrice($exchange->price);
            }

            if ($exchange->exchangeSlug == 'hitbtc') {
                $coin->setHitBtcPrice($exchange->price);
            }
            if ($exchange->exchangeSlug == 'bittrex') {
                $coin->setBittrexPrice($exchange->price);
            }

            if ($exchange->exchangeSlug == 'pancakeswap') {
                $coin->setCakePrice($exchange->price);
            }




        }

        $this->entityManager->flush();;
    }

}
