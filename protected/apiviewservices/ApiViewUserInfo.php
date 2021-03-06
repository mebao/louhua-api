<?php

class ApiViewUserInfo extends EApiViewService {

    private $user;

    public function __construct($user) {
        parent::__construct();
        $this->user = $user;
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
        $this->setUser();
    }

    private function setUser() {
        $std = new stdClass();
        $std->user_id = $this->user->id;
        $std->username = $this->user->username;
        $std->wechat_id = $this->user->wechat_id;
        $std->wechat_name = $this->user->wechat_name;
        $std->real_name = $this->user->real_name;
        $std->brokerage_name = $this->user->brokerage_name;
        $std->cell = $this->user->cell;
        $std->office_telephone = $this->user->office_telephone;
        $std->reco_number = $this->user->reco_number;
        $std->subscribe = $this->user->subscribe;
        $this->results->userinfo = $std;
    }

}
