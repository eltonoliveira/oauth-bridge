<?php

namespace Preferans\Oauth\Server\RequestType;

use Preferans\Oauth\Entities\UserEntityInterface;
use Preferans\Oauth\Entities\ScopeEntityInterface;
use Preferans\Oauth\Entities\ClientEntityInterface;

/**
 * Preferans\Oauth\Server\RequestType\AuthorizationRequest
 *
 * @package Preferans\Oauth\Server\RequestType
 */
class AuthorizationRequest
{
    /**
     * The grant type identifier
     *
     * @var string
     */
    protected $grantTypeId;

    /**
     * The client identifier
     *
     * @var ClientEntityInterface
     */
    protected $client;

    /**
     * The user identifier
     *
     * @var UserEntityInterface
     */
    protected $user;

    /**
     * An array of scope identifiers
     *
     * @var ScopeEntityInterface[]
     */
    protected $scopes = [];

    /**
     * Has the user authorized the authorization request
     *
     * @var bool
     */
    protected $authorizationApproved = false;

    /**
     * The redirect URI used in the request
     *
     * @var string
     */
    protected $redirectUri;

    /**
     * The state parameter on the authorization request
     *
     * @var string
     */
    protected $state;

    /**
     * The code challenge (if provided)
     *
     * @var string
     */
    protected $codeChallenge;

    /**
     * The code challenge method (if provided)
     *
     * @var string
     */
    protected $codeChallengeMethod;

    /**
     * @return string
     */
    public function getGrantTypeId()
    {
        return $this->grantTypeId;
    }

    /**
     * @param string $grantTypeId
     */
    public function setGrantTypeId($grantTypeId)
    {
        $this->grantTypeId = $grantTypeId;
    }

    /**
     * @return ClientEntityInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param ClientEntityInterface $client
     */
    public function setClient(ClientEntityInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @return UserEntityInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserEntityInterface $user
     */
    public function setUser(UserEntityInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @return ScopeEntityInterface[]
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * @param ScopeEntityInterface[] $scopes
     */
    public function setScopes(array $scopes)
    {
        $this->scopes = $scopes;
    }

    /**
     * @return bool
     */
    public function isAuthorizationApproved()
    {
        return  $this->authorizationApproved;
    }

    /**
     * @param bool $authorizationApproved
     */
    public function setAuthorizationApproved($authorizationApproved)
    {
        $this->authorizationApproved = $authorizationApproved;
    }

    /**
     * @return string|null
     */
    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    /**
     * @return string
     */
    public function getFinalRedirectUri()
    {
        if ($this->redirectUri === null) {
            if (is_array($this->getClient()->getRedirectUri())) {
                return $this->getClient()->getRedirectUri()[0];
            }

            return $this->getClient()->getRedirectUri();
        }

        return $this->redirectUri;
    }

    /**
     * @param string $redirectUri
     */
    public function setRedirectUri(string $redirectUri)
    {
        $this->redirectUri = $redirectUri;
    }

    /**
     * @return string|null
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return string|null
     */
    public function getCodeChallenge()
    {
        return $this->codeChallenge;
    }

    /**
     * @param string $codeChallenge
     */
    public function setCodeChallenge($codeChallenge)
    {
        $this->codeChallenge = $codeChallenge;
    }

    /**
     * @return string|null
     */
    public function getCodeChallengeMethod()
    {
        return $this->codeChallengeMethod;
    }

    /**
     * @param string $codeChallengeMethod
     */
    public function setCodeChallengeMethod($codeChallengeMethod)
    {
        $this->codeChallengeMethod = $codeChallengeMethod;
    }
}
