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
class ApiViewSearchAgent extends EApiViewService {

    private $searchInputs;      // Search inputs passed from request url.
    private $getCount = false;  // whether to count no. of Doctors satisfying the search conditions.
    private $pageSize = 10;
    private $agentSearch;
    private $agents = array();
    private $agentCount;
    private $postList;

    public function __construct($searchInputs) {
        parent::__construct();
        //page 当前页码
        $this->searchInputs = $searchInputs;
        $this->getCount = isset($searchInputs['getcount']) && $searchInputs['getcount'] == 1 ? true : false;
        $this->searchInputs['pagesize'] = isset($searchInputs['pagesize']) && $searchInputs['pagesize'] > 0 ? $searchInputs['pagesize'] : $this->pageSize;
        $this->agentSearch = new AgentSearch($this->searchInputs, array('want', 'have'));
    }

    protected function loadData() {
        $this->loadAgents();
        if ($this->getCount) {
            $this->agentCount = $this->agentSearch->count();
        }
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = new stdClass();
            $this->output->status = self::RESPONSE_OK;
            $this->output->errorCode = 0;
            $this->output->dataNum = $this->agentCount;
            $this->output->errorMsg = 'success';
            $this->output->results = $this->results;
        }
    }

    private function loadAgents() {
        $models = $this->agentSearch->search();
        if (arrayNotEmpty($models)) {
            $this->setAgent($models);
        }
        $this->results->agents = $this->agents;
    }

    private function setAgent($models) {
        $list = array();
        $roles = StatCode::loadRoleUser();
        foreach ($models as $v) {
            $this->postList = array();
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
            $std->role = $v->user_role;
            $std->roleText = $roles[$v->user_role];
            $std->recoNumber = $v->reco_number;
            $std->subscribe = $v->subscribe;
            $this->setHave($v->have);
            $this->setWant($v->want);
            $std->projectNum = $this->listorder();
            $std->postList = $this->postList;
            $list[] = $std;
        }
        $this->agents = $list;
    }

    private function setHave($models) {
        if (arrayNotEmpty($models)) {
            foreach ($models as $v) {
                $std = new stdClass();
                $std->id = $v->id;
                $std->projectId = $v->project_id;
                $std->projectName = $v->project_name;
//            $std->type = $v->unit_type;
//            $std->exposure = $v->exposure;
//            $std->coop = $v->coop;
//            $std->floor = $v->floor_level;
//            $std->price = $v->price;
                $std->time = $v->getDateCreated('Y/m/d H:i a');
                $std->postType = 'have';
                $this->postList[] = $std;
            }
        }
    }

    private function setWant($models) {
        if (arrayNotEmpty($models)) {
            foreach ($models as $v) {
                $std = new stdClass();
                $std->id = $v->id;
                $std->projectId = $v->project_id;
                $std->projectName = $v->project_name;
//            $std->type = $v->unit_type;
//            $std->exposure = $v->exposure;
//            $std->floor = $v->expect_floor_low . "-" . $v->expect_floor_high;
//            $std->price = $v->price;
                $std->time = $v->getDateCreated('Y/m/d H:i a');
                $std->postType = 'want';
                $this->postList[] = $std;
            }
        }
    }

    private function listorder() {
        $postList = array();
        $projectList = array();
        foreach ($this->postList as $v) {
            if (isset($projectList[$v->projectId]) === false) {
                $projectList[$v->projectId] = $v->projectName;
            }
            $postList[] = $v->time;
        }
        array_multisort($postList, SORT_DESC, $this->postList);
        return count($projectList);
    }

}
