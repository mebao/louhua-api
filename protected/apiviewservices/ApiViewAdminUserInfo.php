<?php

class ApiViewAdminUserInfo extends EApiViewService {

    private $id;
    private $info;

    public function __construct($id) {
        parent::__construct();
        $this->id = $id;
        $this->info = null;
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
        $user = User::model()->getById($this->id);
        if (isset($user)) {
            $this->setUser($user);
        }
        $this->results->info = $this->info;
    }

    private function setUser($user) {
        $std = new stdClass();
        $std->userId = $user->id;
        $std->username = $user->username;
        $std->wechatId = $user->wechat_id;
        $std->wechatName = $user->wechat_name;
        $std->wxUserid = $user->wx_userid;
        $std->realName = $user->real_name;
        $std->brokerageName = $user->brokerage_name;
        $std->cell = $user->cell;
        $std->officeTelephone = $user->office_telephone;
        $std->role = $user->user_role;
        $std->recoNumber = $user->reco_number;
        $std->subscribe = $user->subscribe;
        $this->info = $std;
    }

}
