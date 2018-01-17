<?php

namespace Preferans\Oauth\Tests\Server;

use PHPUnit\Framework\TestCase;
use Preferans\Oauth\Server\CodeChallengeVerifiers\S256Verifier;

class S256VerifierTest extends TestCase
{
    /** @test */
    public function shouldReturnCorrectMethos()
    {
        $verifier = new S256Verifier();
        $this->assertEquals('S256', $verifier->getMethod());
    }

    /** @test */
    public function shouldVerifyCodeChallenge()
    {
        $verifier = new S256Verifier();

        $this->assertTrue(
            $verifier->verifyCodeChallenge(
                'foo',
                hash('sha256', strtr(rtrim(base64_encode('foo'), '='), '+/', '-_'))
            )
        );

        $this->assertFalse(
            $verifier->verifyCodeChallenge(
                'foo',
                hash('sha256', strtr(rtrim(base64_encode('bar'), '='), '+/', '-_'))
            )
        );
    }
}
