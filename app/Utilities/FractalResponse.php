<?php

namespace App\Utilities;

use App\Utilities\WebService\Contract\ApiResponseFormatterInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class FractalResponse implements ApiResponseFormatterInterface
{
    protected $fractal;

    protected $data;

    protected $includes;

    protected $signature;

    public function __construct($data, string $transformerClass, $includes = null)
    {
        $this->fractal = new Manager();
        $this->data = $data;
        $this->includes = $includes;
        $this->signature = $transformerClass;
    }

    public function output()
    {
        if ($this->data instanceof LengthAwarePaginator) {

            $resource = new Collection($this->data->getCollection(), new $this->signature);

            $resource->setPaginator(new IlluminatePaginatorAdapter($this->data));

        } else if (is_array($this->data)) {

            $resource = new Collection($this->data, new $this->signature);

        } else {

            $resource = new Item($this->data, new $this->signature);

        }

        if ($this->includes) {
            $this->fractal->parseIncludes($this->includes);
        }

        return $this->fractal->createData($resource)->toArray();
    }
}
