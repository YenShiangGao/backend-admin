<?php

namespace App\Http\Api\Resources;

use App\Http\Api\Resources\Traits\SuccessResourceTrait;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class AnonymousResourceCollection
 * @package App\Http\Api\Resources
 */
class AnonymousResourceCollection extends ResourceCollection
{
    use SuccessResourceTrait;

    /**
     * The name of the resource being collected.
     *
     * @var string
     */
    public $collects;

    /**
     * Create a new anonymous resource collection.
     *
     * @param mixed $resource
     * @param string $collects
     * @return void
     */
    public function __construct($resource, $collects)
    {
        $this->collects = $collects;

        parent::__construct($resource);
    }
}
