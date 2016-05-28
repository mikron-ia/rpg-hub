<?php

namespace backend\models\security;

use yii\base\Exception;
use yii\web\HttpException;


/**
 * Class AuthenticationTokenSimple - simple key-based authentication
 * @package Mikron\HubBack\Infrastructure\Security
 */
final class AuthenticationTokenSimple implements AuthenticationToken
{
    /**
     * @var string Key stored in configuration
     */
    private $correctKey;

    public function __construct($configAuthenticationSettingsForMethod)
    {
        if (!isset($configAuthenticationSettingsForMethod['simple'])) {
            throw new HttpException(501, "No configuration for simple authentication set");
        }

        if (!isset($configAuthenticationSettingsForMethod['simple']['authenticationKey'])) {
            throw new HttpException(501, "No authentication key for simple authentication set");
        }

        if (self::isValid($configAuthenticationSettingsForMethod['simple']['authenticationKey'], 'internal')) {
            $this->correctKey = $configAuthenticationSettingsForMethod['simple']['authenticationKey'];
        }
    }

    public static function isValid($key, $identificationForErrors)
    {
        if (empty($key)) {
            throw new Exception("Authentication key incorrect: " . ucfirst($identificationForErrors) . " key must not be empty");
        }

        if (strlen($key) < 20) {
            throw new Exception("Authentication key incorrect: " . ucfirst($identificationForErrors) . " key is too short to be used");
        }

        return true;
    }

    public function checksOut($key)
    {
        if (self::isValid($key, 'received')) {
            return $key === $this->correctKey;
        }
    }

    public function provideKey()
    {
        return $this->correctKey;
    }

    public function provideMethod()
    {
        return 'simple';
    }
}
