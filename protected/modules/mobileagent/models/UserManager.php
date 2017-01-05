<?php

class UserManager {

    public function registerNewUser(AgentUserForm $form) {
        if ($form->validate()) {
            $model = new AgentUser();
            $model->setAttributes($form->getSafeAttributes());
            $model->createNewModel();
            if ($model->save() === false) {
                $form->addErrors($model->getErrors());
            }
        }
        return ($form->getErrors() === false);
    }

    /**
     * Login user.
     * @param UserLoginForm $form
     * @return type 
     */
    public function doLogin(UserLoginForm $form) {
        return ($form->validate() && $form->login());
    }

    /**
     * Auto login user.
     * @param type $username
     * @param type $password
     * @return type 
     */
    public function autoLoginUser($email, $password, $role) {
        $form = new UserLoginForm();
        $form->email = $email;
        $form->password = $password;
        $form->user_role = $role;
        $this->doLogin($form);

        return $form;
    }

}
