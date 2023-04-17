<?php

namespace Coretrek\Idp;

use Laravel\Socialite\Two\User;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;

class SocialiteProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase($this->resolveEndpointUrl('oauth/authorize'), $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return $this->resolveEndpointUrl('oauth/token');
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get($this->resolveEndpointUrl('api/user?includes=group,users&per_page=100'), [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'email' => $user['email'],
            'name' => sprintf('%s %s', $user['first_name'], $user['last_name']),
        ]);
    }

    /**
     * Resolve the full url for the given end point.
     *
     * @return string
     */
    private function resolveEndpointUrl(string $endPoint)
    {
        return sprintf('%s/%s', config('coretrek-idp.base_url'), $endPoint);
    }
}
