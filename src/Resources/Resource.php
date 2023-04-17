<?php

namespace Coretrek\Idp\Resources;

use Coretrek\Idp\Sdk;
use Illuminate\Support\Collection;

abstract class Resource
{
    /**
     * Create a resource instance.
     *
     * @return void
     */
    public function __construct(readonly Sdk $sdk)
    {
    }

    /**
     * Build HTTP query filter from the given array.
     *
     * @param  array<string, mixed>  $filter
     * @return array<string>
     */
    protected function buildFilter(array $filter)
    {
        return Collection::make($filter)->mapWithKeys(fn ($value, $key) => ["filter[{$key}]" => $value])->toArray();
    }
}
