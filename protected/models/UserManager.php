<?php

class UserManager {

    public function createUserByValue($values) {
        $model = new User();
        $model->username = $values['username'];
        $model->password_raw = $values['password'];
        $model->terms = 1;
        $model->createNewModel();
        $model->setActivated();
        if ($model->save()) {
            return $model;
        }
        return null;
    }

}
