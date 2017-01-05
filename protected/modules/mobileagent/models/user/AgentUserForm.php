<?php

class AgentUserForm extends EFormModel {

    public $avatar_url;
    public $email;
    public $password_raw;
    public $wechat_id;
    public $real_name;
    public $brokerage_name;
    public $cell;
    public $office_telephone;
    public $reco_number;
    public $user_role;
    public $subscribe;

    public function rules() {
        return array(
            array('avatar_url, email, password_raw, wechat_id, real_name, brokerage_name, cell, office_telephone, reco_number, user_role',
                'required', 'message' => "must be input {attribute}"),
            array('user_role', 'numerical', 'integerOnly' => true),
            array('wechat_id, email, real_name, brokerage_name, avatar_url', 'length', 'max' => 200),
            array('password_raw, cell, office_telephone, reco_number, subscribe', 'length', 'max' => 50),
            array('email', 'checkUnique'),
        );
    }

    public function checkUnique() {
        if (AgentUser::model()->exists('email=:email AND user_role=:user_role', array(':email' => $this->email, ':user_role' => $this->user_role))) {
            $this->addError('email', 'this email has been registered');
        }
    }

}
