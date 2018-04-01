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
     * Validate Request URI.
     *
     * If a redirect URI is provided ensure it matches what is pre-registered.
     *
     * @param ClientEntityInterface $client
     * @param RequestInterface      $request
     * @param string|null           $redirectUri
     *
     * @return void
     *
     * @throws OAuthServerException
     */
    public function validateRequestUri(
        ClientEntityInterface $client,
        RequestInterface $request,
        string $redirectUri = null
    )
    {
        $clientRedirect = $client->getRedirectUri();

        if ($redirectUri !== null) {
            if (is_string($clientRedirect) && (strcmp($clientRedirect, $redirectUri) !== 0)) {
                $this->throwInvalidClientException($request);
            } elseif (is_array($clientRedirect) && in_array($redirectUri, $clientRedirect) === false) {
                $this->throwInvalidClientException($request);
            }
        }
    }

    /**
     * Throws OAuthServerException and fires an RequestEvent::CLIENT_AUTHENTICATION_FAILED event.
     *
     * @param RequestInterface $request
     *
     * @return void
     *
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
