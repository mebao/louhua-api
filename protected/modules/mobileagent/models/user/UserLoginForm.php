<?php

class UserLoginForm extends EFormModel {

    public $email;
    public $password;
    public $duration = 2590222; // 30 days.
    public $user_role = StatCode::ROLE_USER;
    private $_identity;

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules() {
        return array(
            array('email, password', 'required', 'message' => 'must be input {attribute}'),
            array('password', 'authenticate'),
        );
    }

    /**
     * Authenticates the password.
     * This is the 'authenticate' validator as declared in rules().
     */
    public function authenticate() {
        if ($this->hasErrors() === false) {
            $this->_identity = new UserIdentity($this->email, $this->password, $this->user_role);
            if ($this->_identity->authenticate() === false) {
                $errorCode = $this->_identity->errorCode;
                if ($errorCode == UserIdentity::ERROR_USERNAME_INVALID) {
                    $this->addError('email', 'this email is not registered');
                } else {
                    $this->addError('password', 'incorrect password');
                }
            }
        }
    }

    /**
     * Logs in the user using the given username and password in the model.
     * @return boolean whether login is successful
     */
    public function login() {
        if ($this->_identity === null) {
            $this->_identity = new UserIdentity($this->email, $this->password, $this->user_role);
            $this->_identity->authenticate();
        }
        if ($this->_identity->errorCode === UserIdentity::ERROR_NONE) {
            Yii::app()->user->login($this->_identity, $this->duration);
            return true;
        } else
            return false;
    }

}
