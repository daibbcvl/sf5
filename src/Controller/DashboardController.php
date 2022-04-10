<?php

namespace App\Controller;

use App\Entity\Balance;
use App\Entity\User;
use App\Form\DepositType;
use App\Form\UserType;
use App\Form\WithdrawType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     *
     * @return ResponseAlias
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        return $this->render('dashboard/index.html.twig', [
            'balance' => $user->getBalance() ? $user->getBalance()->getAmount() : 0
        ]);
    }

    /**
     * @Route("/", name="default")
     *
     * @return ResponseAlias
     */
    public function default(Request $request)
    {
        return $this->redirectToRoute('dashboard');
    }


    /**
     * @Route("/dashboard/generate", name="generate_balance")
     *
     * @return RedirectResponse
     */
    public function generateBalance(EntityManagerInterface  $entityManager)
    {
        $amount = rand(100,1000);
        /** @var User $user */
        $user = $this->getUser();
        $balance = $user->getBalance() ? $user->getBalance() : new Balance();
        $balance->setAmount($amount);
        $balance->setUser($user);
        $entityManager->persist($balance);
        $entityManager->flush();

        return $this->redirectToRoute('dashboard');

    }

    /**
     * @Route("/deposit", name="deposit")
     *
     * @return ResponseAlias
     */
    public function deposit(Request $request, EntityManagerInterface $entityManager)
    {

        $form = $this->createForm(DepositType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $amount = floatval($data['amount']);

            /** @var User $user */
            $user = $this->getUser();
            $balance = $user->getBalance() ? $user->getBalance() : new Balance();
            $balance->setAmount($balance->getAmount() + $amount);
            $balance->setUser($user);
            $entityManager->persist($balance);
            $entityManager->flush();

            $this->addFlash('success', "You've deposited {$amount} USD to your wallet successfully");
            return $this->redirectToRoute('dashboard');

        }

        return $this->render('dashboard/deposit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/withdraw", name="withdraw")
     *
     * @return ResponseAlias
     */
    public function withdraw(Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(WithdrawType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $amount = floatval($data['amount']);

            /** @var User $user */
            $user = $this->getUser();
            $balance = $user->getBalance() ? $user->getBalance() : new Balance();
            $balanceAmount = $balance->getAmount();

            if($balanceAmount < $amount) {
                $this->addFlash('error', "Your balance is not enough");
                return $this->redirectToRoute('dashboard');
            }
            $balance->setAmount($balance->getAmount() + $amount);
            $balance->setUser($user);
            $entityManager->persist($balance);
            $entityManager->flush();

            $this->addFlash('success', "You've withdrawn {$amount} USD successfully");
            return $this->redirectToRoute('dashboard');

        }

        return $this->render('dashboard/deposit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
