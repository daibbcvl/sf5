<?php

namespace App\Controller;

use Auth0\SDK\API\Authentication;
use Auth0\SDK\Auth0;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class Auth0Controller extends AbstractController
{
    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/connect/auth0", name="connect_auth0_start")
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {


        $auth0 = new Auth0([
            'domain' => 'dev-eu0ikx9f.us.auth0.com',
            'client_id' => 'ceEoZlhWkfZDraZgNw6I4EccxA75RWdH',
            'redirect_uri' => 'https://localhost:8000/auth/callback/',
            'audience' => null,
            'scope' => 'openid profile email',
            'persist_access_token' => true,
            'persist_id_token' => true
        ]);

//        AUTH0_CALLBACK_URL=http://localhost:3000/
//
//
//
//        $accessToken = new Authentication(
//            'dev-eu0ikx9f.us.auth0.com',
//            'ceEoZlhWkfZDraZgNw6I4EccxA75RWdH',
//            'UigscnqdSCfsiFuE0hjQ9cVIl6DM-EawIXnD8NoSAUSip_FpK_zLub1J5kxLyV8K',
//            'https://dev-eu0ikx9f.us.auth0.com/api/v2/'
//        );


        $auth0->login();
    }

    /**
     * After going to Facebook, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @Route("/auth/callback", name="connect_auth0_check")
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {

        dd('hello');
        return $this->redirectToRoute('list');
    }

    /**
     * @Route("/test", name="test")
     */
    public function test()
    {
        dd($this->getUser());
    }
}