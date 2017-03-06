<?php

class ApiViewGetTask extends EApiViewService {

    private $info;
    private $values;

    public function __construct($values) {
        parent::__construct();
        $this->values = $values;
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
        $model = HousingResources::model()->getById($this->values['id']);
        if (isset($model)) {
            $model->admin_id = $this->values['user_id'];
            $model->action = StatCode::HOUSE_ACTION_PROCESS;
            $model->update(array('admin_id', 'action'));
            $this->setModel($model);
        }
        $this->results = $this->info;
    }

    private function setModel($v) {
        $std = new stdClass();
        $std->id = $v->id;
        $std->projectId = $v->project_id;
        $std->projectName = $v->project_name;
        $std->haveId = $v->have_id;
        $std->userHaveId = $v->user_have_id;
        $std->userHaveName = $v->user_have_name;
        $std->wantId = $v->want_id;
        $std->userWantId = $v->user_want_id;
        $std->userWantName = $v->user_want_name;
        $std->unitType = $v->unit_type;
        $std->floorLow = $v->expect_floor_low;
        $std->floorHigh = $v->expect_floor_high;
        $std->price = $v->price;
        $std->exposure = $v->exposure;
        $std->coop = $v->coop;
        $std->action = $v->action;
        $std->unitStatus = $v->unit_status;
        $std->time = $v->getDateCreated('Y-m-d H:i:s');
        $std->postType = $this->values['post_type'];
        $this->info->post = $std;
    }

}
