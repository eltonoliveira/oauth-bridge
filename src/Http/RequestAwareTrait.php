<?php

namespace Preferans\Oauth\Http;

use Phalcon\Http\RequestInterface;
use Phalcon\Events\EventsAwareInterface;
use Preferans\Oauth\Server\RequestEvent;
use Preferans\Oauth\Entities\ClientEntityInterface;
use Preferans\Oauth\Exceptions\OAuthServerException;

/**
 * Preferans\Oauth\Http\RedirectUriAwareTrait
 *
 * @package Preferans\Oauth\Http
 */
trait RedirectUriAwareTrait
{
    /**
     * Normalize Request Uri
     *
     * @param ClientEntityInterface $client
     * @param RequestInterface      $request
     * @param string|null           $redirectUri
     * @return string
     * @throws OAuthServerException
     */
    protected function normalizeRequestUri(
        ClientEntityInterface $client,
        RequestInterface $request,
        string $redirectUri = null
    ) : string
    {
        $clientRedirect = $client->getRedirectUri();

        if ($redirectUri !== null) {
            if (is_string($clientRedirect) && (strcmp($clientRedirect, $redirectUri) !== 0)) {
                $this->throwInvalidClientException($request);
            } elseif (is_array($clientRedirect) && in_array($redirectUri, $clientRedirect) === false) {
                $this->throwInvalidClientException($request);
            }
        } elseif (is_array($clientRedirect) && count($clientRedirect) !== 1 || empty($clientRedirect)) {
            $this->throwInvalidClientException($request);
        } else {
            $redirectUri = is_array($clientRedirect) ? $clientRedirect[0] : $clientRedirect;
        }

        return $redirectUri;
    }

    /**
     * @param RequestInterface $request
     * @throws OAuthServerException
     */
    protected function throwInvalidClientException(RequestInterface $request)
    {
        if ($this instanceof EventsAwareInterface) {
            $this->getEventsManager()->fire(RequestEvent::CLIENT_AUTHENTICATION_FAILED, $request);
        }

        throw OAuthServerException::invalidClient();
    }
}
