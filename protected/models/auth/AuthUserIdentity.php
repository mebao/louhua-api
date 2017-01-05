<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class AuthUserIdentity extends CUserIdentity {

    const AUTH_TYPE_PASSWORD = 1; // authenticate by using password.
    const AUTH_TYPE_TOKEN = 2;    // authenticate by using token.

    public $auth_type;
    private $user;  // User model.
    public $password; //token
    private $token; // AuthTokenUser.

    public function __construct($username, $password, $authType = null) {
        $this->username = $username;
        $this->password = $password;
        $this->auth_type = $authType;
    }

    public function authenticate() {
        switch ($this->auth_type) {
            case self::AUTH_TYPE_PASSWORD:
                return $this->authenticatePassword();
            case self::AUTH_TYPE_TOKEN:
                return $this->authenticateToken();
            default:
                $this->errorCode = ErrorList::AUTH_UNKNOWN_TYPE;
                return false;
        }
    }

    /**
     * authenticates user by token and username.     
     */
    public function authenticateToken() {

        $this->token = AuthTokenUser::model()->verifyTokenUser($this->password, $this->username);

        if (is_null($this->token) || $this->token->isTokenValid() === false) {
            $this->errorCode = ErrorList::AUTH_TOKEN_INVALID;
        } else {
            $this->errorCode = ErrorList::ERROR_NONE;
            $this->user = $this->token->getUser();
        }
        return $this->errorCode === ErrorList::ERROR_NONE;
    }

    public function hasSuccess() {
        return $this->errorCode === ErrorList::ERROR_NONE;
    }

    public function getUser() {
        return $this->user;
    }

    public function getToken() {
        return $this->token;
    }

}
