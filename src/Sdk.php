<?php

namespace Coretrek\Idp;

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
     * @param  string  $baseUrl
     * @return void
     */
    public function __construct(
        protected Token $token,
        protected string $baseUrl,
    ) {
        $this->request = Http::withOptions([
            'base_uri' => $baseUrl,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => $this->token->authorizationHeader(),
            ],
        ]);

        $this->users = new Users($this);
        $this->groups = new Groups($this);
    }

    /**
     * Generic GET request.
     *
     * @param  string  $url
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
