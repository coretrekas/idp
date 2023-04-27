<?php

namespace Coretrek\Idp;

use Exception;
use Coretrek\Idp\Resources\Users;
use Coretrek\Idp\Resources\Groups;
use Coretrek\Idp\Resources\Resource;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;

final class Sdk
{
    /**
     * Request instance.
     *
     * @param  \Illuminate\Http\Client\PendingRequest  $request
     */
    protected PendingRequest $request;

    /**
     * User resource.
     *
     * @param \Coretrek\Idp\Resources\Users
     */
    protected Resource $users;

    /**
     * Group resource.
     *
     * @param \Coretrek\Idp\Resources\Groups
     */
    protected Resource $groups;

    /**
     * Create an SDK instance.
     *
     * @param  \Coretrek\Idp\Token  $token
     * @return void
     */
    public function __construct(
        protected Token $token,
        protected string $baseUrl,
        protected string $locale = 'nb',
    ) {
        try {
            $this->request = Http::withOptions($this->options($baseUrl, $locale));
        } catch (Exception $e) {
            $this->request = (new \Illuminate\Http\Client\Factory())->withOptions($this->options($baseUrl, $locale));
        }

        $this->users = new Users($this);
        $this->groups = new Groups($this);
    }

    /**
     * Get formatted options
     *
     * @return array<string>
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    protected function options(string $baseUrl, string $locale)
    {
        return [
            'base_uri' => $baseUrl,
            'headers' => [
                'Accept-Language' => $locale,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => $this->token->authorizationHeader(),
            ],
        ];
    }

    /**
     * Generic GET request.
     *
     * @return array<mixed>
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function get(string $url)
    {
        return $this
            ->request
            ->get($url)
            ->throw()
            ->json();
    }

    /**
     * Get the request instance.
     *
     * @return \Illuminate\Http\Client\PendingRequest
     */
    public function request()
    {
        return $this->request;
    }

    /**
     * Get the users resource instance.
     *
     * @return \Coretrek\Idp\Resources\Users
     */
    public function users()
    {
        return $this->users;
    }

    /**
     * Get the groups resource instance.
     *
     * @return \Coretrek\Idp\Resources\Groups
     */
    public function groups()
    {
        return $this->groups;
    }
}
