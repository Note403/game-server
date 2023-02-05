<?php

namespace Auxilium\Data;

class Request
{
    public function __construct(
        public string $URI,
        public string $method,
        public string $IP,
        public string $request_time,
        public array|null $request_data,
    )
    {
    }

    public function input(string $key) {
        return $this->request_data[$key];
    }
}