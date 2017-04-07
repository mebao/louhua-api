<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiViewOrderHaveList
 *
 * @author ShuMing
 */
class ApiViewMatchInfo extends EApiViewService {

    private $info;
    private $id;
    private $type;

    public function __construct($id, $type) {
        parent::__construct();
        $this->id = $id;
        $this->type = $type;
        $this->info = new stdClass();
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
        $this->loadUserPost();
    }

    private function loadUserPost() {
        //根据id查出want have  和house的user want id
        if ($this->type == 'have') {
            $house = HousingResources::model()->getAllByAttributes(array('have_id' => $this->id), array('userWant'));
        } else {
            $house = HousingResources::model()->getAllByAttributes(array('want_id' => $this->id), array('userHave'));
        }
        if (arrayNotEmpty($house)) {
            $this->setModel($house[0]);
            $this->setPostUser($house);
        }
        $this->results = $this->info;
    }

    private function setModel($v) {
        $std = new stdClass();
        $std->id = $v->id;
        if ($this->type == 'have') {
            $std->user_id = $v->user_have_id;
            $std->userName = $v->user_have_name;
            $std->showId = $v->have_id;
        } else {
            $std->user_id = $v->user_want_id;
            $std->userName = $v->user_want_name;
            $std->showId = $v->want_id;
        }
        $std->projectId = $v->project_id;
        $std->projectName = $v->project_name;
        $std->type = $v->unit_type;
        $std->floorLow = $v->expect_floor_low;
        $std->floorHigh = $v->expect_floor_high;
        $std->price = $v->price;
        $std->exposure = $v->exposure;
        if ($this->type == 'have') {
            $std->coop = $v->coop;
        }
        $std->action = $v->action;
        $std->unitStatus = $v->unit_status;
        $std->time = $v->getDateCreated('Y-m-d H:i:s');
        $this->info->post = $std;
    }

    private function setPostUser($models) {
        $roles = StatCode::loadRoleUser();
        $postusers = array();
        foreach ($models as $value) {
            if ($this->type == 'have') {
                $user = $value->userWant;
            } else {
                $user = $value->userHave;
            }
            if (isset($user) === false) {
                continue;
            }
            $std = new stdClass();
            $std->houseId = $value->id;
            $std->userId = $user->id;
            $std->username = $user->username;
            $std->wechatId = $user->wechat_id;
            $std->wechatName = $user->wechat_name;
            $std->wxUserid = $user->wx_userid;
            $std->realName = $user->real_name;
            $std->cell = $user->cell;
            $std->role = $user->user_role;
            $std->roleText = $roles[$user->user_role];
            $std->subscribe = $user->getSubscribeText();
            $postusers[] = $std;
        }
        $this->info->users = $postusers;
    }

}
