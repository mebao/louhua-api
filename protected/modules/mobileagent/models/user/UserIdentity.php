<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity {

    private $email;
    private $role;

    public function __construct($email, $password, $role) {
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    public function authenticate() {
        $user = AgentUser::model()->loadByEmailAndRole($this->email, $this->role);
        if ($user === null) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        } else {
            if ($user->checkLoginPassword($this->password) === false) {
                $this->errorCode = self::ERROR_PASSWORD_INVALID; //Wrong password.
            } else {
                $this->errorCode = self::ERROR_NONE;
            }
        }
        return !$this->errorCode;
    }

    public function getRole() {
        return $this->role;
    }

}
