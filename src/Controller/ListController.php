<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends AbstractController
{
    /**
     * @Route("/list", name="list")
     *
     * @return Response
     */
    public function index(Request $request)
    {


//        $memcached = new \Memcached();
//        $memcached->addServer("165.22.108.72", 11211);
//        $message = $memcached->get("message");
//        if ($message) {
//            dd($message);
//        } else {
//            $memcached->set("message", "Hello World");
//
//        }
//        var_dump($memcached->getAllKeys());
        //$session = $request->getSession();

         //dd($this->getUser());
        $companies = [
            'Apple' => '$1.16 trillion USD',
            'Samsung' => '$298.68 billion USD',
            'Microsoft' => '$1.10 trillion USD',
            'Alphabet' => '$878.48 billion USD',
            'Intel Corporation' => '$245.82 billion USD',
            'IBM' => '$120.03 billion USD',
            'Facebook' => '$552.39 billion USD',
            'Hon Hai Precision' => '$38.72 billion USD',
            'Tencent' => '$3.02 trillion USD',
            'Oracle' => '$180.54 billion USD',
        ];

        return $this->render('list/index.html.twig', [
            'companies' => $companies,
        ]);
    }

    /**
     * @Route("/test", name="test")
     *
     * @return Response
     */
    public function test(Request $request)
    {
        return new Response('ok');
    }
}
