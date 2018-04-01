<?php

namespace Preferans\Oauth\Server\RequestType;

use Phalcon\Di\Injectable;
use Phalcon\Http\RequestInterface;
use Preferans\Oauth\Server\RequestEvent;
use Preferans\Oauth\Exceptions\OAuthServerException;
use Preferans\Oauth\Server\Grant\GrantTypeInterface;
use Preferans\Oauth\Entities\ClientEntityInterface;
use Preferans\Oauth\Traits\RequestScopesAwareTrait;

/**
 * Preferans\Oauth\Server\RequestType\RequestTypeFactory
 *
 * @package Preferans\Oauth\Server\RequestType
 */
class RequestTypeFactory extends Injectable
{
    use RequestScopesAwareTrait;

    /**
     * @param GrantTypeInterface    $grantType
     * @param ClientEntityInterface $client
     * @param RequestInterface      $request
     * @param string|null           $redirectUri
     * @param string|null           $state
     * @return AuthorizationRequest
     * @throws OAuthServerException
     */
    public function createAuthorizationRequest(
        GrantTypeInterface $grantType,
        ClientEntityInterface $client,
        RequestInterface $request,
        string $redirectUri = null,
        string $state = null
    ) : AuthorizationRequest
    {

        $redirectUri = $this->normalizeRequestUri($client, $request, $redirectUri);

        $authorizationRequest = new AuthorizationRequest();
        $authorizationRequest->setGrantTypeId(
            $grantType->getIdentifier()
        );

        $authorizationRequest->setClient($client);

        $defaultScope = $grantType->getDefaultScope();
        $authorizationRequest->setScopes(
            $this->getScopesFromRequest($request, true, $redirectUri, $defaultScope)
        );

        $authorizationRequest->setRedirectUri($redirectUri);

        if (!empty($state)) {
            $authorizationRequest->setState($state);
        }

        return $authorizationRequest;
    }

    protected function normalizeRequestUri(
        ClientEntityInterface $client,
        RequestInterface $request,
        string $redirectUri = null
    ) : string
    {
        $clientRedirect = $client->getRedirectUri();

        if ($redirectUri !== null) {
            if (is_string($clientRedirect) && (strcmp($clientRedirect, $redirectUri) !== 0)) {
                $this->getEventsManager()->fire(RequestEvent::CLIENT_AUTHENTICATION_FAILED, $request);
                throw OAuthServerException::invalidClient();
            } elseif (is_array($clientRedirect) && in_array($redirectUri, $clientRedirect) === false) {
                $this->getEventsManager()->fire(RequestEvent::CLIENT_AUTHENTICATION_FAILED, $request);
                throw OAuthServerException::invalidClient();
            }
        } elseif (is_array($clientRedirect) && count($clientRedirect) !== 1 || empty($clientRedirect)) {
            $this->getEventsManager()->fire(RequestEvent::CLIENT_AUTHENTICATION_FAILED, $request);
            throw OAuthServerException::invalidClient();
        } else {
            $redirectUri = is_array($clientRedirect) ? $clientRedirect[0] : $clientRedirect;
        }

        return $redirectUri;
    }
}
