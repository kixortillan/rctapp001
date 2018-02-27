<?php

namespace App\Utilities\Rest;

use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Traversable;

class ResourceFactory
{

    public static function toArray($data, string $signature, array $meta = null)
    {
        if ($data instanceof LengthAwarePaginator) {

            $resource = new Collection($data->getCollection(), new $signature);

            $resource->setPaginator(new IlluminatePaginatorAdapter($data));

        } else if ($data instanceof Traversable) {

            $resource = new Collection($data, new $signature);

        } else {

            $resource = new Item($data, new $signature);

        }

        if ($meta) {
            $resource->setMeta($meta);
        }

        return (new Manager)->createData($resource)->toArray();
    }

}
