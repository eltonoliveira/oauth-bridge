<?php

namespace Preferans\Oauth\Server\Grant;

use DateInterval;
use Phalcon\Http\RequestInterface;
use Preferans\Oauth\Server\CryptKey;
use Phalcon\Events\EventsAwareInterface;
use Preferans\Oauth\Repositories\ScopeRepositoryInterface;
use Preferans\Oauth\Repositories\ClientRepositoryInterface;
use Preferans\Oauth\Server\RequestType\AuthorizationRequest;
use Preferans\Oauth\Server\ResponseType\ResponseTypeInterface;
use Preferans\Oauth\Repositories\AccessTokenRepositoryInterface;

/**
 * Preferans\Oauth\Server\Grant\GrantTypeInterface
 *
 * @package Preferans\Oauth\Server\Grant
 */
interface GrantTypeInterface extends EventsAwareInterface
{
    /**
     * Set refresh token TTL.
     *
     * @param DateInterval $refreshTokenTTL
     */
    public function setRefreshTokenTTL(DateInterval $refreshTokenTTL);

    /**
     * Return the grant identifier that can be used in matching up requests.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Respond to an incoming request.
     *
     * @param RequestInterface      $request
     * @param ResponseTypeInterface $responseType
     * @param DateInterval          $accessTokenTTL
     *
     * @return ResponseTypeInterface
     */
    public function respondToAccessTokenRequest(
        RequestInterface $request,
        ResponseTypeInterface $responseType,
        DateInterval $accessTokenTTL
    );

    /**
     * The grant type should return true if it is able to response to an authorization request
     *
     * @param RequestInterface $request
     *
     * @return bool
     */
    public function canRespondToAuthorizationRequest(RequestInterface $request);

    /**
     * If the grant can respond to an authorization request this method should be called to validate the parameters of
     * the request.
     *
     * If the validation is successful an AuthorizationRequest object will be returned. This object can be safely
     * serialized in a user's session, and can be used during user authentication and authorization.
     *
     * @param RequestInterface $request
     *
     * @return AuthorizationRequest
     */
    public function validateAuthorizationRequest(RequestInterface $request);

    /**
     * Once a user has authenticated and authorized the client the grant can complete the authorization request.
     * The AuthorizationRequest object's $userId property must be set to the authenticated user and the
     * $authorizationApproved property must reflect their desire to authorize or deny the client.
     *
     * @param AuthorizationRequest $authorizationRequest
     *
     * @return ResponseTypeInterface
     */
    public function completeAuthorizationRequest(AuthorizationRequest $authorizationRequest);

    /**
     * The grant type should return true if it is able to respond to this request.
     *
     * For example most grant types will check that the $_POST['grant_type'] property matches it's identifier property.
     *
     * @param RequestInterface $request
     *
     * @return bool
     */
    public function canRespondToAccessTokenRequest(RequestInterface $request);

    /**
     * Set the client repository.
     *
     * @param ClientRepositoryInterface $clientRepository
     */
    public function setClientRepository(ClientRepositoryInterface $clientRepository);

    /**
     * Set the access token repository.
     *
     * @param AccessTokenRepositoryInterface $accessTokenRepository
     */
    public function setAccessTokenRepository(AccessTokenRepositoryInterface $accessTokenRepository);

    /**
     * Set the scope repository.
     *
     * @param ScopeRepositoryInterface $scopeRepository
     */
    public function setScopeRepository(ScopeRepositoryInterface $scopeRepository);

    /**
     * Get the scope repository.
     *
     * @return ScopeRepositoryInterface
     */
    public function getScopeRepository(): ScopeRepositoryInterface;

    /**
     * Set the path to the private key.
     *
     * @param CryptKey $privateKey
     */
    public function setPrivateKey(CryptKey $privateKey);

    /**
     * Set the encryption key
     *
     * @param string|null $key
     */
    public function setEncryptionKey($key = null);

    /**
     * Sets the default scope for the current Grant Type.
     *
     * @param string $defaultScope
     * @return void
     */
    public function setDefaultScope(string $defaultScope);

    /**
     * Get the default scope for the current Grant Type.
     *
     * @return string|null
     */
    public function getDefaultScope();
}
