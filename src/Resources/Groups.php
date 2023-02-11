<?php

namespace Coretrek\Idp\Resources;

use Illuminate\Support\Collection;

class Groups extends Resource
{
    /**
     * List Groups.
     *
     * @return array<mixed>
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function list()
    {
        return $this
            ->sdk
            ->request()
            ->get('/api/groups', Collection::make(func_get_args() ?? [])
                ->mapWithKeys(function ($value) {
                    if (is_int($value)) {
                        return ['per_page' => $value];
                    }

                    if (is_array($value)) {
                        return $this->buildFilter($value);
                    }
                })
                ->filter()
                ->all()
            )
            ->throw()
            ->json();
    }

    /**
     * Show the given group.
     *
     * @param  string  $id
     * @param  array<string>  $includes
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function show(string $id, array $includes = [])
    {
        return $this
            ->sdk
            ->request()
            ->get("/api/groups/{$id}", array_filter([
                'includes' => implode(',', $includes),
            ]))
            ->throw()
            ->json();
    }

    /**
     * Create a new group.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<mixed>
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function create(array $attributes = [])
    {
        return $this
            ->sdk
            ->request()
            ->post('/api/groups', $attributes)
            ->throw()
            ->json();
    }

    /**
     * Update the given group.
     *
     * @param  string  $id
     * @param  array<string, mixed>  $attributes
     * @return void
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function update(string $id, array $attributes = [])
    {
        $this
            ->sdk
            ->request()
            ->patch("/api/groups/{$id}", $attributes)
            ->throw();
    }

    /**
     * Delete the given group.
     *
     * @param  string  $id
     * @return void
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function delete(string $id)
    {
        $this
            ->sdk
            ->request()
            ->delete("/api/groups/{$id}")
            ->throw();
    }

    /**
     * Add a user the given group.
     *
     * @param  string  $groupId
     * @param  string  $userId
     * @param  array  $meta
     * @return void
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function addUser(string $groupId, string $userId, array $meta = [])
    {
        $this
            ->sdk
            ->request()
            ->post("/api/groups/{$groupId}/users/{$userId}", compact('meta'))
            ->throw();
    }

    /**
     * Update meta data in the group for the given user.
     *
     * @param  string  $groupId
     * @param  string  $userId
     * @param  array  $meta
     * @return void
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function updateUser(string $groupId, string $userId, array $meta = [])
    {
        $this
            ->sdk
            ->request()
            ->patch("/api/groups/{$groupId}/users/{$userId}", compact('meta'))
            ->throw();
    }

    /**
     * Remove the given user from the group.
     *
     * @param  string  $groupId
     * @param  string  $userId
     * @return void
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function removeUser(string $groupId, string $userId)
    {
        $this
            ->sdk
            ->request()
            ->delete("/api/groups/{$groupId}/users/{$userId}")
            ->throw();
    }
}
