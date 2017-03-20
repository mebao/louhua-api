<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiViewSearchAdmin
 *
 * @author ShuMing
 */
class ApiViewSearchAdmin extends EApiViewService {

    private $searchInputs;      // Search inputs passed from request url.
    private $getCount = false;  // whether to count no. of Doctors satisfying the search conditions.
    private $pageSize = 10;
    private $adminSearch;  // DoctorSearch model.
    private $admins = array();
    private $adminCount;     // count no. of Doctors.

    public function __construct($searchInputs) {
        parent::__construct();
        //page 当前页码
        $this->searchInputs = $searchInputs;
        $this->getCount = isset($searchInputs['getcount']) && $searchInputs['getcount'] == 1 ? true : false;
        $this->searchInputs['pagesize'] = isset($searchInputs['pagesize']) && $searchInputs['pagesize'] > 0 ? $searchInputs['pagesize'] : $this->pageSize;
        $this->adminSearch = new AdminSearch($this->searchInputs);
    }

    protected function loadData() {
        $this->loadAdmins();
        if ($this->getCount) {
            $this->adminCount = $this->adminSearch->count();
        }
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = new stdClass();
            $this->output->status = self::RESPONSE_OK;
            $this->output->errorCode = 0;
            $this->output->dataNum = $this->adminCount;
            $this->output->errorMsg = 'success';
            $this->output->results = $this->results;
        }
    }

    private function loadAdmins() {
        $models = $this->adminSearch->search();
        if (arrayNotEmpty($models)) {
            $this->setAdmin($models);
        }
        $this->results->admins = $this->admins;
    }

    private function setAdmin($models) {
        $list = array();
        foreach ($models as $v) {
            $std = new stdClass();
            $std->userId = $v->id;
            $std->username = $v->username;
            $std->wechatId = $v->wechat_id;
            $std->wechatName = $v->wechat_name;
            $std->wxUserid = $v->wx_userid;
            $std->realName = $v->real_name;
            $std->brokerageName = $v->brokerage_name;
            $std->cell = $v->cell;
            $std->officeTelephone = $v->office_telephone;
            $std->recoNumber = $v->reco_number;
            $std->subscribe = $v->subscribe;
            $list[] = $std;
        }
        $this->admins = $list;
    }

}
