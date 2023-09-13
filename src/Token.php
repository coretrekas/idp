<?php

namespace Coretrek\Idp;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

class Token implements Arrayable, Jsonable
{
    /**
     * Create a Token instance.
     *
     * @return void
     */
    public function __construct(
        readonly string $type,
        readonly string $accessToken,
        readonly int $expiresIn = 0,
    ) {
    }

    /**
     * Make a new token.
     *
     * @param  array<string>  $scopes
     * @return \Coretrek\Idp\Token
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public static function make(string $baseUrl, string $id, string $secret, array $scopes = [])
    {
        try {
            $response = Http::asForm()
                ->post("{$baseUrl}/oauth/token", [
                    'grant_type' => 'client_credentials',
                    'client_id' => $id,
                    'client_secret' => $secret,
                    'scope' => implode(' ', $scopes),
                ])
                ->throw()
                ->json();
        } catch (Exception $e) {
            $response = (new \Illuminate\Http\Client\Factory())
                ->asForm()
                ->post("{$baseUrl}/oauth/token", [
                    'grant_type' => 'client_credentials',
                    'client_id' => $id,
                    'client_secret' => $secret,
                    'scope' => implode(' ', $scopes),
                ])
                ->throw()
                ->json();
        }

        return new static($response['token_type'], $response['access_token'], $response['expires_in']);
    }

    /**
     * Get the token represented as an authorization header.
     *
     * @return string
     */
    public function authorizationHeader()
    {
        return sprintf('%s %s', $this->type, $this->accessToken);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'token_type' => $this->type,
            'expires_in' => $this->expiresIn,
            'access_token' => $this->accessToken,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }
}
