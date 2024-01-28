<?php

namespace AWSCognitoApp;

use Aws\Result;
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;

/**
 * Class AWSCognitoWrapper
 *
 * This class serves as a wrapper for interacting with the AWS Cognito service.
 */
class AWSCognitoWrapper
{
    /**
     * The name of the cookie used to store the AWS Cognito app access token.
     *
     * @var string
     */
    private const COOKIE_NAME = 'aws-cognito-app-access-token';

    /**
     * @var string $region The AWS region for the Cognito service.
     */
    private $region;

    /**
     * @var string $client_id The client ID for AWS Cognito.
     */
    private $client_id;

    /**
     * @var string $userpool_id The ID of the AWS Cognito user pool.
     */
    private $userpool_id;

    /**
     * @var string $aws_access_key_id The AWS access key ID.
     */
    private $aws_access_key_id;

    /**
     * @var string $aws_secret_access_key The AWS secret access key.
     */
    private $aws_secret_access_key;

    /**
     * @var object $client The AWS Cognito client.
     */
    private $client;

    /**
     * @var null $user The user object.
     */
    private $user = null;

    /**
     * AWSCognitoWrapper constructor.
     */
    public function __construct()
    {
        if (!$_ENV['REGION'] || !$_ENV['CLIENT_ID'] || !$_ENV['USERPOOL_ID']) {
            $message = "Please provide the region, client_id and userpool_id variables in the .env file";
            throw new \InvalidArgumentException($message);
        }

        $this->region = $_ENV['REGION'];
        $this->client_id = $_ENV['CLIENT_ID'];
        $this->userpool_id = $_ENV['USERPOOL_ID'];
        $this->aws_access_key_id = $_ENV['AWS_ACCESS_KEY_ID'];
        $this->aws_secret_access_key = $_ENV['AWS_SECRET_ACCESS_KEY'];
    }


     /**
     * Initializes the AWS Cognito wrapper.
     *
     * This method is responsible for initializing the AWS Cognito wrapper.
     * It should be called before any other methods are used.
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->client = new CognitoIdentityProviderClient([
        'version' => '2016-04-18',
        'region' => $this->region,
        'credentials' => [
        'key' => $this->aws_access_key_id,
        'secret'  => $this->aws_secret_access_key,
        ],
        ]);

        try {
            $this->user = $this->client->getUser([
            'AccessToken' => $this->getAuthenticationCookie(),
            ]);
        } catch (\Exception $e) {
          // An exception indicates the accesstoken is incorrect - $this->user will still be null.
        }
    }


    /**
     * Authenticates a user with the given username and password.
     *
     * @param string $username The username of the user.
     * @param string $password The password of the user.
     * @return string The authentication result.
     */
    public function authenticate(string $username, string $password): string
    {
        try {
            $result = $this->client->adminInitiateAuth([
            'AuthFlow' => 'ADMIN_NO_SRP_AUTH',
            'ClientId' => $this->client_id,
            'UserPoolId' => $this->userpool_id,
            'AuthParameters' => [
            'USERNAME' => $username,
            'PASSWORD' => $password,
            ],
            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        $this->setAuthenticationCookie($result->get('AuthenticationResult')['AccessToken']);

        return '';
    }

    /**
     * Signs up a user with the provided username, email, and password.
     *
     * @param string $username The username of the user.
     * @param string $email The email address of the user.
     * @param string $password The password of the user.
     * @return string The result of the signup operation.
     */
    public function signup(string $username, string $email, string $password): string
    {
        try {
            $this->client->signUp([
            'ClientId' => $this->client_id,
            'Username' => $username,
            'Password' => $password,
            'UserAttributes' => [
                [
                  'Name' => 'name',
                  'Value' => $username,
                ],
                [
                  'Name' => 'email',
                  'Value' => $email,
                ],
            ],
            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return '';
    }


    /**
     * Confirms the signup of a user with the provided username and confirmation code.
     *
     * @param string $username The username of the user to confirm the signup for.
     * @param string $code The confirmation code received by the user.
     * @return string The result of the signup confirmation.
     */
    public function confirmSignup(string $username, string $code): string
    {
        try {
            $this->client->confirmSignUp([
            'ClientId' => $this->client_id,
            'Username' => $username,
            'ConfirmationCode' => $code,
            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return '';
    }


    /**
     * Resends the confirmation code for a user.
     *
     * @param string $username The username of the user.
     * @return string The result of the operation.
     */
    public function resendConfirmationCode(string $username): string
    {
        try {
            $this->client->resendConfirmationCode([
            'ClientId' => $this->client_id,
            'Username' => $username,
            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return '';
    }

    /**
     * Sends a password reset mail to the specified username.
     *
     * @param string $username The username of the user.
     * @return string The result of the operation.
     */
    public function sendPasswordResetMail(string $username): string
    {
        try {
            $this->client->forgotPassword([
            'ClientId' => $this->client_id,
            'Username' => $username,
            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return '';
    }

    /**
     * Resets the password for a user in AWS Cognito.
     *
     * @param string $code The verification code sent to the user.
     * @param string $password The new password for the user.
     * @param string $username The username of the user.
     * @return string The result of the password reset operation.
     */
    public function resetPassword(string $code, string $password, string $username): string
    {
        try {
            $this->client->confirmForgotPassword([
            'ClientId' => $this->client_id,
            'ConfirmationCode' => $code,
            'Password' => $password,
            'Username' => $username,
            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return '';
    }

    /**
     * Checks if the user is authenticated.
     *
     * @return bool Returns true if the user is authenticated, false otherwise.
     */
    public function isAuthenticated(): bool
    {
        return null !== $this->user;
    }

    /**
     * Retrieves the metadata of the Cognito user pool.
     *
     * @return array The metadata of the Cognito user pool.
     */
    public function getPoolMetadata(): array
    {
        $result = $this->client->describeUserPool([
        'UserPoolId' => $this->userpool_id,
        ]);

        return $result->get('UserPool');
    }

    /**
     * Retrieves an array of pool users.
     *
     * @return array The array of pool users.
     */
    public function getPoolUsers(): array
    {
        $result = $this->client->listUsers([
        'UserPoolId' => $this->userpool_id,
        ]);

        return $result->get('Users');
    }

    /**
     * Retrieves the user information.
     *
     * @return Result|null The user information if found, null otherwise.
     */
    public function getUser(): ?Result
    {
        return $this->user;
    }

    /**
     * Retrieves the user information for the specified username.
     *
     * @param string $username The username of the user to retrieve.
     * @return array|null The user information if found, null otherwise.
     */
    public function adminGetUser($username)
    {
      // Your Cognito user pool configuration.
        $userPoolId = $this->userpool_id;

      // Call adminGetUser to check if the user exists.
        try {
            $result = $this->client->adminGetUser([
            'UserPoolId' => $userPoolId,
            'Username'   => $username,
            ]);

          // If the user exists, $result will contain user attributes.
          // var_dump($result);
            $returnResult['status'] = 'SUCCESS';
            $returnResult['userExistsInCognitoPool'] = true;
            $returnResult['Username'] = $result['Username'];
            $returnResult['UserStatus'] = $result['UserStatus'];
            $returnResult['exceptionMessage'] = null;

            return $returnResult;
        } catch (\Exception $e) {
          // If the user does not exist,
          // CognitoIdentityProviderException will be thrown.
          // echo 'User does not exist: ' . $e->getMessage();
            $returnResult['status'] = 'FAIL';
            $returnResult['userExistsInCognitoPool'] = false;
            $returnResult['Username'] = 'N/A';
            $returnResult['UserStatus'] = 'N/A';
            $returnResult['exceptionMessage'] = $e->getMessage();
            return $returnResult;
        }
    }

    /**
     * Logs out the user from the AWS Cognito service.
     *
     * @return void
     */
    public function logout()
    {
        if (isset($_COOKIE[self::COOKIE_NAME])) {
            unset($_COOKIE[self::COOKIE_NAME]);
            setcookie(self::COOKIE_NAME, '', time() - 3600);
        }
    }


    /**
     * Sets the authentication cookie using the provided access token.
     *
     * @param string $accessToken The access token to set the authentication cookie with.
     * @return void
     */
    private function setAuthenticationCookie(string $accessToken): void
    {
      /*
       * Please note that plain-text storage of the access token is insecure and
       * not recommended by AWS. This is only done to keep this example
       * application as easy as possible. Read the AWS docs for more info:
       * http://docs.aws.amazon.com/cognito/latest/developerguide/amazon-cognito-user-pools-using-tokens-with-identity-providers.html
       */
        setcookie(self::COOKIE_NAME, $accessToken, time() + 3600);
    }

    /**
     * Retrieves the authentication cookie.
     *
     * @return string The authentication cookie.
     */
    private function getAuthenticationCookie(): string
    {
        return $_COOKIE[self::COOKIE_NAME] ?? '';
    }
}
