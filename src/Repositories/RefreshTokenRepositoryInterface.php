<?php

namespace Preferans\Oauth\Repositories;

use Preferans\Oauth\Entities\RefreshTokenEntityInterface;
use Preferans\Oauth\Exceptions\UniqueTokenIdentifierConstraintViolationException;

/**
 * Preferans\Oauth\Repositories\RefreshTokenRepositoryInterface
 *
 * @package Preferans\Oauth\Repositories
 */
interface RefreshTokenRepositoryInterface extends RepositoryInterface
{
    /**
     * Creates a new refresh token
     *
     * @return RefreshTokenEntityInterface
     */
    public function getNewRefreshToken();

    /**
     * Create a new refresh token_name.
     *
     * @param RefreshTokenEntityInterface $refreshTokenEntity
     *
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity);

    /**
     * Revoke the refresh token.
     *
     * @param string $tokenId
     */
    public function revokeRefreshToken($tokenId);

    /**
     * Check if the refresh token has been revoked.
     *
     * @param string $tokenId
     *
     * @return bool Return true if this token has been revoked
     */
    public function isRefreshTokenRevoked($tokenId);
}
