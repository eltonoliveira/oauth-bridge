<?php

namespace Preferans\Oauth\AuthorizationValidators;

use Phalcon\Http\RequestInterface;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\Events\EventsAwareInterface;
use Preferans\Oauth\Server\CryptKey;

/**
 * Preferans\Oauth\AuthorizationValidators\AuthorizationValidatorInterface
 *
 * @package Preferans\Oauth\AuthorizationValidators
 */
interface AuthorizationValidatorInterface extends InjectionAwareInterface, EventsAwareInterface
{
    /**
     * Determine the access token in the authorization header
     * and append oAuth properties to the request as attributes.
     *
     * @param RequestInterface $request
     *
     * @return RequestInterface
     */
    public function validateAuthorization(RequestInterface $request);

    /**
     * Set the public key
     *
     * @param CryptKey $key
     */
    public function setPublicKey(CryptKey $key);
}
