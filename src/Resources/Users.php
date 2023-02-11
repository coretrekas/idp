<?php

namespace Coretrek\Idp\Resources;

use Illuminate\Support\Collection;

class Users extends Resource
{
    /**
     * List Users.
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
            ->get('/api/users', Collection::make(func_get_args() ?? [])
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
     * Show the given user.
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
            ->get("/api/users/{$id}", array_filter([
                'includes' => implode(',', $includes),
            ]))
            ->throw()
            ->json();
    }

    /**
     * Create a new user.
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
            ->post('/api/users', $attributes)
            ->throw()
            ->json();
    }

    /**
     * Update the given user.
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
            ->patch("/api/users/{$id}", $attributes)
            ->throw();
    }

    /**
     * Delete the given user.
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
            ->delete("/api/users/{$id}")
            ->throw();
    }
}
