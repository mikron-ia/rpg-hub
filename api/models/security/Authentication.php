<?php

namespace api\models\security;

use yii\web\HttpException;

/**
 * Class AuthenticationFactory
 * @package Mikron\HubBack\Infrastructure\Security
 */
class Authentication
{
    /**
     * @var AuthenticationToken
     */
    private $token;

    /**
     * @param array $config Configuration segment responsible for authentication references and constants
     * @param array $parameters Configuration segment responsible for authentication data for local deployment
     * @param string $direction Who is trying to talk to us and which keyset is used?
     * @param string $authenticationMethodReceived What method are they trying to use?
     * @throws HttpException
     */
    public function __construct($config, $parameters, $direction, $authenticationMethodReceived)
    {
        if (!isset($config['authenticationMethodReference'])) {
            throw new HttpException(
                501,
                "Authentication configuration error: missing reference table for authentication methods"
            );
        }

        if (!isset($config['authenticationMethodReference'][$authenticationMethodReceived])) {
            throw new HttpException(
                501,
                "Authentication configuration error: missing reference for '$authenticationMethodReceived' method"
            );
        }

        $authenticationMethod = $config['authenticationMethodReference'][$authenticationMethodReceived];

        if (!in_array($authenticationMethod, $parameters[$direction]['allowedStrategies'])) {
            throw new HttpException(
                405,
                "Authentication strategy '$authenticationMethod' ('$authenticationMethodReceived') not allowed"
            );
        }

        $this->token = $this->createToken($parameters[$direction], $authenticationMethod);
    }

    /**
     * @param array $configWithChosenDirection
     * @param string $authenticationMethod
     * @return AuthenticationToken
     * @throws HttpException
     */
    private function createToken($configWithChosenDirection, $authenticationMethod)
    {
        $className = 'api\models\security\AuthenticationToken' . ucfirst($authenticationMethod);

        if (!class_exists($className)) {
            throw new HttpException(
                501,
                "Authentication configuration error: class $className, despite being allowed, does not exist"
            );
        }

        return new $className($configWithChosenDirection['settingsByStrategy']);
    }

    /**
     * @param string $authenticationKey What is the key they present?
     * @return bool
     */
    public function isAuthenticated($authenticationKey)
    {
        return $this->token->checksOut($authenticationKey);
    }

    /**
     * Provides authentication method for use in outgoing message
     * @return string
     */
    public function provideAuthenticationMethod()
    {
        return 'auth-' . $this->token->provideMethod();
    }

    /**
     * Provides authentication key for use in outgoing message
     * @return string
     */
    public function provideAuthenticationKey()
    {
        return $this->token->provideKey();
    }
}
