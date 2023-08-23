<?php

namespace Coretrek\Idp\Resources;

use Coretrek\Idp\Sdk;
use Illuminate\Support\Collection;

abstract class Resource
{
    /**
     * Only validates against create and update endpoints.
     */
    protected bool $onlyValidate = false;

    /**
     * Create a resource instance.
     *
     * @return void
     */
    public function __construct(readonly Sdk $sdk)
    {
    }

    /**
     * Enable only validate.
     *
     * @return Coretrek\Idp\Resources\Resource
     */
    public function onlyValidate()
    {
        $this->onlyValidate = true;

        return $this;
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

    /**
     * Build the uri.
     *
     * @return string
     */
    protected function buildUri(string $uri)
    {
        if ($this->onlyValidate) {
            return "{$uri}?only_validate=true";
        }

        return $uri;
    }
}
