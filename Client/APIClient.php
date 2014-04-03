<?php

namespace Gigya\Client;

/**
 * Wrapper for Gigya Client SDK
 */

class APIClient
{

    private $apiKey;
    private $secretKey;
    private $methodNamespace;

    public function __construct($apiKey, $secretKey)
    {
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;

    }

    public function sendRequest($method, $parameters = array())
    {

        $request = new GSRequest($this->apiKey, $this->secretKey, $method);

        foreach ($parameters as $value) {

            $request->setParam($value[0], $value[1]);

        }

        $response = $request->send();

        if ($response->getErrorCode() == 0) {

            $valid = SigUtils::validateUserSignature(
                $response->getString("UID", ""),
                $response->getString("signatureTimestamp", ""),
                $this->secretKey,
                $response->getString("UIDSignature", "")
            );

            if (!$valid) {

                throw new Exception\InvalidResponseSignature('Gigya response signature invalid.');

            }

            return $response;

        } else {

            throw new Exception\InvalidResponse($response->getErrorMessage());

        }

    }

    public function getArray()
    {
        return new GSArray();
    }

    public function getObject()
    {
        return new GSObject;
    }

    public function accounts()
    {
        $this->methodNamespace = 'accounts';
        return $this;
    }

    public function socialize()
    {
        $this->methodNamespace = 'socialize';
        return $this;
    }

    public function comments()
    {
        $this->methodNamespace = 'comments';
        return $this;
    }

    public function gameMechanics()
    {
        $this->methodNamespace = 'gm';
        return $this;
    }

    public function reports()
    {
        $this->methodNamespace = 'reports';
        return $this;
    }

    public function chat()
    {
        $this->methodNamespace = 'chat';
        return $this;
    }

    public function dataStore()
    {
        $this->methodNamespace = 'ds';
        return $this;
    }

    public function identityStorage()
    {
        $this->methodNamespace = 'ids';
        return $this;
    }

    public function gcs()
    {
        $this->methodNamespace = 'gcs';
        return $this;
    }

    public function __call($name, array $arguments)
    {

        if (false === strpos($name, '.') && !is_null($this->methodNamespace)) {
            $name = $this->methodNamespace . '.' . $name;
        }

        return $this->sendRequest($name, $arguments);

    }
}
