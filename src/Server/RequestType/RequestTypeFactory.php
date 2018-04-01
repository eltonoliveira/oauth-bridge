<?php

namespace Preferans\Oauth\Server\RequestType;

use Phalcon\Di\Injectable;
use Phalcon\Http\RequestInterface;
use Preferans\Oauth\Http\RedirectUriAwareTrait;
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
    use RequestScopesAwareTrait, RedirectUriAwareTrait;

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
}
