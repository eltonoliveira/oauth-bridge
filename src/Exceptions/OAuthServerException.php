<?php

namespace Preferans\Oauth\Exceptions;

use Phalcon\Http\ResponseInterface;

/**
 * Preferans\Oauth\Exceptions\OAuthServerException
 *
 * @package Preferans\Oauth\Exceptions
 */
class OAuthServerException extends \Exception
{
    /**
     * @var int
     */
    private $httpStatusCode;

    /**
     * @var string
     */
    private $errorType;

    /**
     * @var null|string
     */
    private $hint;

    /**
     * @var null|string
     */
    private $redirectUri;

    /**
     * @var array
     */
    private $payload = [];

    /**
     * Throw a new exception.
     *
     * @param string      $message        Error message
     * @param int         $code           Error code
     * @param string      $errorType      Error type
     * @param int         $httpStatusCode HTTP status code to send (default = 400)
     * @param null|string $hint           A helper hint
     * @param null|string $redirectUri    A HTTP URI to redirect the user back to
     */
    public function __construct($message, $code, $errorType, $httpStatusCode = 400, $hint = null, $redirectUri = null)
    {
        parent::__construct($message, $code);
        $this->httpStatusCode = $httpStatusCode;
        $this->errorType = $errorType;
        $this->hint = $hint;
        $this->redirectUri = $redirectUri;

        $this->payload = [
            'error'   => $errorType,
            'message' => $message,
        ];

        if ($hint !== null) {
            $this->payload['hint'] = $hint;
        }
    }

    /**
     * Returns the current payload.
     *
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Updates the current payload.
     *
     * @param array $payload
     */
    public function setPayload(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Unsupported grant type error.
     *
     * @return static
     */
    public static function unsupportedGrantType(): OAuthServerException
    {
        $errorMessage = 'The authorization grant type is not supported by the authorization server.';
        $hint = 'Check the `grant_type` parameter';

        return new static($errorMessage, 2, 'unsupported_grant_type', 400, $hint);
    }

    /**
     * Invalid request error.
     *
     * @param string      $parameter The invalid parameter
     * @param null|string $hint
     *
     * @return static
     */
    public static function invalidRequest($parameter, $hint = null): OAuthServerException
    {
        $errorMessage = 'The request is missing a required parameter, includes an invalid parameter value, ' .
            'includes a parameter more than once, or is otherwise malformed.';
        $hint = ($hint === null) ? sprintf('Check the `%s` parameter', $parameter) : $hint;

        return new static($errorMessage, 3, 'invalid_request', 400, $hint);
    }

    /**
     * Invalid client error.
     *
     * @return static
     */
    public static function invalidClient() : OAuthServerException
    {
        $errorMessage = 'Client authentication failed';

        return new static($errorMessage, 4, 'invalid_client', 401);
    }

    /**
     * Invalid scope error.
     *
     * @param string      $scope       The bad scope.
     * @param null|string $redirectUri A HTTP URI to redirect the user back to.
     *
     * @return static
     */
    public static function invalidScope(string $scope, $redirectUri = null): OAuthServerException
    {
        $errorMessage = 'The requested scope is invalid, unknown, or malformed';

        if (empty($scope)) {
            $hint = 'Specify a scope in the request or set a default scope';
        } else {
            $hint = sprintf(
                'Check the `%s` scope',
                htmlspecialchars($scope, ENT_QUOTES, 'UTF-8', false)
            );
        }

        return new static($errorMessage, 5, 'invalid_scope', 400, $hint, $redirectUri);
    }

    /**
     * Invalid credentials error.
     *
     * @return static
     */
    public static function invalidCredentials(): OAuthServerException
    {
        return new static(
            'The user credentials were incorrect.',
            6,
            'invalid_credentials',
            401
        );
    }

    /**
     * Server error.
     *
     * @param string $hint
     * @return static
     */
    public static function serverError(string $hint): OAuthServerException
    {
        return new static(
            'The authorization server encountered an unexpected condition which prevented it from fulfilling'
            . ' the request: ' . $hint,
            7,
            'server_error',
            500
        );
    }

    /**
     * Invalid refresh token.
     *
     * @param null|string $hint
     *
     * @return static
     */
    public static function invalidRefreshToken($hint = null): OAuthServerException
    {
        return new static('The refresh token is invalid.', 8, 'invalid_request', 401, $hint);
    }

    /**
     * Access denied.
     *
     * @param null|string $hint
     * @param null|string $redirectUri
     *
     * @return static
     */
    public static function accessDenied($hint = null, $redirectUri = null): OAuthServerException
    {
        return new static(
            'The resource owner or authorization server denied the request.',
            9,
            'access_denied',
            401,
            $hint,
            $redirectUri
        );
    }

    /**
     * Invalid grant.
     *
     * @param string $hint
     *
     * @return static
     */
    public static function invalidGrant($hint = ''): OAuthServerException
    {
        return new static(
            'The provided authorization grant (e.g., authorization code, resource owner credentials) or refresh token '
            . 'is invalid, expired, revoked, does not match the redirection URI used in the authorization request, '
            . 'or was issued to another client.',
            10,
            'invalid_grant',
            400,
            $hint
        );
    }

    /**
     * @return string
     */
    public function getErrorType()
    {
        return $this->errorType;
    }

    /**
     * Generate a HTTP response.
     *
     * @param ResponseInterface $response
     * @param bool              $useFragment True if errors should be in the URI fragment instead of query string
     * @param int               $jsonOptions The options passed to json_encode.
     *
     * @return ResponseInterface
     */
    public function generateHttpResponse(ResponseInterface $response, $useFragment = false, int $jsonOptions = 0)
    {
        $headers = $this->getHttpHeaders();
        $payload = $this->getPayload();

        if ($this->redirectUri !== null) {
            if ($useFragment === true) {
                $this->redirectUri .= (strstr($this->redirectUri, '#') === false) ? '#' : '&';
            } else {
                $this->redirectUri .= (strstr($this->redirectUri, '?') === false) ? '?' : '&';
            }

            return $response
                ->setStatusCode(302)
                ->setHeader('Location', $this->redirectUri . http_build_query($payload));
        }

        foreach ($headers as $header => $content) {
            $response->setHeader($header, $content);
        }

        return $response
            ->setStatusCode($this->getHttpStatusCode())
            ->setContentType('application/json', 'UTF-8')
            ->setContent(json_encode($payload, $jsonOptions));
    }

    /**
     * Get all headers that have to be send with the error response.
     *
     * @return array Array with header values
     */
    public function getHttpHeaders()
    {
        $headers = [
            'Content-type' => 'application/json',
        ];

        // Add "WWW-Authenticate" header
        //
        // RFC 6749, section 5.2.:
        // "If the client attempted to authenticate via the 'Authorization'
        // request header field, the authorization server MUST
        // respond with an HTTP 401 (Unauthorized) status code and
        // include the "WWW-Authenticate" response header field
        // matching the authentication scheme used by the client.
        if ($this->errorType !== 'invalid_client') {
            return $headers;
        }

        $authScheme = 'Basic';

        if (isset($_SERVER['HTTP_AUTHORIZATION']) && strpos($_SERVER['HTTP_AUTHORIZATION'], 'Bearer') === 0) {
            $authScheme = 'Bearer';
        }

        $headers['WWW-Authenticate'] = $authScheme . ' realm="OAuth"';

        return $headers;
    }

    /**
     * Returns whether the exception includes a redirect, since
     * getHttpStatusCode() doesn't return a 302 when there's a
     * redirect enabled. This helps when you want to override local
     * error pages but want to let redirects through.
     *
     * @return bool
     */
    public function hasRedirect()
    {
        return $this->redirectUri !== null;
    }

    /**
     * Returns the HTTP status code to send when the exceptions is output.
     *
     * @return int
     */
    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }

    /**
     * @return null|string
     */
    public function getHint()
    {
        return $this->hint;
    }
}
