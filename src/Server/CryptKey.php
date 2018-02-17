<?php

namespace Preferans\Oauth\Server;

use LogicException;
use RuntimeException;

/**
 * Preferans\Oauth\Server\CryptKey
 *
 * @author  Julián Gutiérrez <juliangut@gmail.com>
 * @package Preferans\Oauth\Server
 */
class CryptKey
{
    const RSA_KEY_PATTERN =
        '/^(-----BEGIN (RSA )?(PUBLIC|PRIVATE) KEY-----)\R.*(-----END (RSA )?(PUBLIC|PRIVATE) KEY-----)\R?$/s';

    /**
     * @var string
     */
    protected $keyPath;

    /**
     * @var null|string
     */
    protected $passPhrase;

    /**
     * CryptKey constructor.
     *
     * @param string      $keyPath
     * @param null|string $passPhrase
     * @param bool        $keyPermissionsCheck
     *
     * @throws LogicException
     */
    public function __construct($keyPath, $passPhrase = null, $keyPermissionsCheck = true)
    {
        if (preg_match(self::RSA_KEY_PATTERN, $keyPath)) {
            $keyPath = $this->saveKeyToFile($keyPath);
        }

        if (strpos($keyPath, 'file://') !== 0) {
            $keyPath = 'file://' . $keyPath;
        }

        if (!file_exists($keyPath) || !is_readable($keyPath)) {
            throw new LogicException(sprintf('Key path "%s" does not exist or is not readable', $keyPath));
        }

        if ($keyPermissionsCheck === true) {
            // Verify the permissions of the key
            $keyPathPerms = decoct(fileperms($keyPath) & 0777);
            if (in_array($keyPathPerms, ['400', '440', '600', '660'], true) === false) {
                trigger_error(sprintf(
                    'Key file "%s" permissions are not correct, recommend changing to 600 or 660 instead of %s',
                    $keyPath,
                    $keyPathPerms
                ), E_USER_NOTICE);
            }
        }

        $this->keyPath = $keyPath;
        $this->passPhrase = $passPhrase;
    }

    /**
     * Saves a key to a file.
     *
     * @param string $key
     *
     * @return string
     * @throws RuntimeException
     */
    private function saveKeyToFile($key)
    {
        $tmpDir = sys_get_temp_dir();
        $keyPath = $tmpDir . '/' . sha1($key) . '.key';

        if (file_exists($keyPath)) {
            return 'file://' . $keyPath;
        }

        if (!touch($keyPath)) {
            throw new RuntimeException(sprintf('"%s" key file could not be created', $keyPath));
        }

        if (file_put_contents($keyPath, $key) === false) {
            throw new RuntimeException(sprintf('Unable to write key file to temporary directory "%s"', $tmpDir));
        }

        if (chmod($keyPath, 0600) === false) {
            throw new RuntimeException(
                sprintf('The key file "%s" file mode could not be changed with chmod to 600', $keyPath)
            );
        }

        return 'file://' . $keyPath;
    }

    /**
     * Retrieve key path.
     *
     * @return string
     */
    public function getKeyPath()
    {
        return $this->keyPath;
    }

    /**
     * Retrieve key pass phrase.
     *
     * @return null|string
     */
    public function getPassPhrase()
    {
        return $this->passPhrase;
    }
}
