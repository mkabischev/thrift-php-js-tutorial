<?php

namespace Hellowords\Component;

class Security
{
    public function serialize($data, $key)
    {
        return hash_hmac('md5', $data, $key);
    }

    public function generateSalt()
    {
        return md5(uniqid('', true));
    }
}
