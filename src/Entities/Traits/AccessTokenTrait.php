<?php

namespace Preferans\Oauth\Entities\Traits;

use Lcobucci\JWT\Token;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Preferans\Oauth\Server\CryptKey;
use Preferans\Oauth\Entities\ClientEntityInterface;
use Preferans\Oauth\Entities\ScopeEntityInterface;

/**
 * Preferans\Oauth\Repositories\Traits\AccessTokenTrait
 *
 * @package Preferans\Oauth\Repositories\Traits
 */
trait AccessTokenTrait
{
    /**
     * Generate a JWT from the access token
     *
     * @param CryptKey $privateKey
     *
     * @return Token
     */
    public function convertToJWT(CryptKey $privateKey)
    {
        $builder = new Builder();

        $builder
            ->setAudience($this->getClient()->getIdentifier())
            ->setId($this->getIdentifier(), true)
            ->setIssuedAt(time())
            ->setNotBefore(time())
            ->setExpiration($this->getExpiryDateTime()->getTimestamp())
            ->setSubject($this->getUserIdentifier())
            ->set('scopes', $this->getScopes())
            ->sign(new Sha256(), new Key($privateKey->getKeyPath(), $privateKey->getPassPhrase()));

        return $builder->getToken();
    }

    /**
     * @return ClientEntityInterface
     */
    abstract public function getClient();

    /**
     * @return \DateTime
     */
    abstract public function getExpiryDateTime();

    /**
     * @return string|int
     */
    abstract public function getUserIdentifier();

    /**
     * @return ScopeEntityInterface[]
     */
    abstract public function getScopes();
}
