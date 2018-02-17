<?php

namespace Preferans\Oauth\Tests\Helpers;

trait KeysAwareTrait
{
    protected $privateCrlfKey;
    protected $privateKey;
    protected $publicKey;

    protected function setUp()
    {
        $this->privateCrlfKey = __DIR__ . '/../_data/private.key.crlf';
        $this->privateKey = __DIR__ . '/../_data/private.key';
        $this->publicKey = __DIR__ . '/../_data/public.key';

        chmod($this->privateCrlfKey, 0600);
        chmod($this->privateKey, 0600);
        chmod($this->publicKey, 0600);
    }
}
