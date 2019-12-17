<?php

namespace App\Services\Msg;

class Message
{
    protected $data;

    public function __construct($data = [])
    {
        $this->data = $data;
    }
}
