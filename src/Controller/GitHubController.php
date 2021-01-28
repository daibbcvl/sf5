<?php

namespace App\Controller;


use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GitHubController extends AbstractController
{
    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/connect/github", name="connect_github")
     * @param ClientRegistry $clientRegistry
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('github')
            ->redirect();
    }

    /**
     * Facebook redirects to back here afterward
     *
     * @Route("/connect/github/check", name="connect_github_check")
     * @param ClientRegistry $clientRegistry
     * @param Request        $request
     * @return void
     */
    public function connectCheckAction(ClientRegistry $clientRegistry, Request $request)
    {
        $user =  $clientRegistry->getClient('github')->fetchUser();
        dd($user);

    }

}