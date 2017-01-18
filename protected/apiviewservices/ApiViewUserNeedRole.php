<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiViewUserNeedRole
 *
 * @author ShuMing
 */
class ApiViewUserNeedRole extends EApiViewService {

    private $userList;

    public function __construct() {
        parent::__construct();
        $this->userList = array();
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = new stdClass();
            $this->output->status = self::RESPONSE_OK;
            $this->output->errorCode = 0;
            $this->output->errorMsg = 'success';
            $this->output->results = $this->results;
        }
    }

    protected function loadData() {
        $this->loadNeedUser();
    }

    private function loadNeedUser() {
        $models = User::model()->loadAllNeedRole();
        if (arrayNotEmpty($models)) {
            $this->setNeedUser($models);
        }
        $this->results->needUsers = $this->userList;
    }

    private function setNeedUser($models) {
        foreach ($models as $m) {
            $std = new stdClass();
            $std->id = $m->id;
            $std->username = $m->username;
            $std->wechatId = $m->wechat_id;
            $std->realName = $m->real_name;
            $std->brokerageName = $m->brokerage_name;
            $std->cell = $m->cell;
            $std->officeTelephone = $m->office_telephone;
            $this->userList[] = $std;
        }
    }

}
