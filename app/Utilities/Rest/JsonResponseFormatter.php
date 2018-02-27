<?php

namespace App\Utilities\Rest;

use DateTime;

class JsonResponseFormatter
{
    /**
     *
     * @var array
     */
    protected $response = [
        'data' => [],
        'meta' => [],
    ];

    public function __construct(array $data, array $meta = [])
    {
        $this->response['data'] = $data;
        $this->response['meta'] = $meta;
    }

    public function setMeta(array $meta)
    {
        $this->response['meta'] = $meta;

        return $this;
    }

    public function format()
    {
        $response['meta']['datetime'] = (new DateTime('now'))->format('Y-m-d H:i:s');

        return $this->response;
    }

}
