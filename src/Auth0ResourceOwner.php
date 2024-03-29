<?php

namespace App;

use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\GenericOAuth2ResourceOwner;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Auth0ResourceOwner extends GenericOAuth2ResourceOwner
{
    protected $paths = [
        'identifier' => 'user_id',
        'nickname' => 'nickname',
        'realname' => 'name',
        'email' => 'email',
        'profilepicture' => 'picture',
    ];

    public function getAuthorizationUrl($redirectUri, array $extraParameters = [])
    {
        return parent::getAuthorizationUrl($redirectUri, array_merge([
            'audience' => $this->options['audience'],
        ], $extraParameters));
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'authorization_url' => '{base_url}/authorize',
            'access_token_url' => '{base_url}/oauth/token',
            'infos_url' => '{base_url}/userinfo',
            'audience' => '{base_url}/userinfo',
        ]);

        $resolver->setRequired([
            'base_url',
        ]);

        $normalizer = function (Options $options, $value) {
            return str_replace('{base_url}', $options['base_url'], $value);
        };

        $resolver->setNormalizer('authorization_url', $normalizer);
        $resolver->setNormalizer('access_token_url', $normalizer);
        $resolver->setNormalizer('infos_url', $normalizer);
        $resolver->setNormalizer('audience', $normalizer);
    }
}
