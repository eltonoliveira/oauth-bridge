<?php

namespace Preferans\Oauth\Server\RequestType;

use Phalcon\Di\Injectable;
use Phalcon\Http\RequestInterface;
use Preferans\Oauth\Entities\ScopeEntityInterface;
use Preferans\Oauth\Http\RedirectUriAwareTrait;
use Preferans\Oauth\Exceptions\OAuthServerException;
use Preferans\Oauth\Server\Grant\GrantTypeInterface;
use Preferans\Oauth\Entities\ClientEntityInterface;

/**
 * Preferans\Oauth\Server\RequestType\RequestTypeFactory
 *
 * @package Preferans\Oauth\Server\RequestType
 */
class RequestTypeFactory extends Injectable
{
    use RedirectUriAwareTrait;

    /**
     * @param GrantTypeInterface     $grantType
     * @param ClientEntityInterface  $client
     * @param RequestInterface       $request
     * @param string|null            $redirectUri
     * @param string|null            $state
     * @param ScopeEntityInterface[] $scopes
     *
     * @return AuthorizationRequest
     *
     * @throws OAuthServerException
     */
    public function createAuthorizationRequest(
        GrantTypeInterface $grantType,
        ClientEntityInterface $client,
        RequestInterface $request,
        string $redirectUri = null,
        string $state = null,
        array $scopes = []
    ) : AuthorizationRequest {
        $this->validateRequestUri($client, $request, $redirectUri);

        if ($redirectUri === null) {
            $clientRedirect = $client->getRedirectUri();

            if (is_array($clientRedirect) && count($clientRedirect) !== 1 || empty($clientRedirect)) {
                $this->throwInvalidClientException($request);
            }

            $redirectUri = is_array($clientRedirect) ? $clientRedirect[0] : $clientRedirect;
        }

        $authorizationRequest = new AuthorizationRequest();
        $authorizationRequest->setGrantTypeId(
            $grantType->getIdentifier()
        );

        $authorizationRequest->setClient($client);
        $authorizationRequest->setScopes($scopes);
        $authorizationRequest->setRedirectUri($redirectUri);

        if (!empty($state)) {
            $authorizationRequest->setState($state);
        }

        return $authorizationRequest;
    }
}
