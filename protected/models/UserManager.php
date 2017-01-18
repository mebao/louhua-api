<?php

class UserManager {

    public function adminAuth($values) {
        $std = new stdClass();
        $std->status = 'no';
        $std->errorCode = 502;
        $std->errorMsg = 'authorize faild!';
        $admin = User::model()->getById($values['id']);
        if (isset($admin) && strIsEmpty($admin->user_role)) {
            $admin->user_role = StatCode::ROLE_ADMIN;
            if ($admin->update(array('user_role'))) {
                $std->status = 'ok';
                $std->errorCode = 200;
                $std->errorMsg = 'success';
            }
        }
        return $std;
    }

}
