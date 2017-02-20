<?php

class AdminSearch extends ESearchModel {

    public function __construct($searchInputs, $with = null) {
        $searchInputs['order'] = 't.id DESC';
        parent::__construct($searchInputs, $with);
    }

    public function model() {
        $this->model = new User();
    }

    public function getQueryFields() {
        return array('id', 'wechat_id', 'wechat_name', 'admin_name', 'real_name', 'cell', 'brokerage_name', 'office_telephone', 'reco_number', 'user_role', 'subscribe');
    }

    public function addQueryConditions() {
        $this->criteria->compare('t.is_deleted', StatCode::DB_ISNOT_DELETED);
        $this->criteria->compare('t.user_role', StatCode::ROLE_ADMIN);
        if ($this->hasQueryParams()) {
            if (isset($this->queryParams['id'])) {
                $this->criteria->compare('id', $this->queryParams['id']);
            }
            if (isset($this->queryParams['wechat_id'])) {
                $this->criteria->addSearchCondition('wechat_id', $this->queryParams['wechat_id']);
            }
            if (isset($this->queryParams['wechat_name'])) {
                $this->criteria->addSearchCondition('t.wechat_name', $this->queryParams['wechat_name']);
            }
            if (isset($this->queryParams['admin_name'])) {
                $this->criteria->addSearchCondition('t.username', $this->queryParams['admin_name']);
            }
            if (isset($this->queryParams['real_name'])) {
                $this->criteria->addSearchCondition('t.real_name', $this->queryParams['real_name']);
            }
            if (isset($this->queryParams['cell'])) {
                $this->criteria->addSearchCondition("t.cell", $this->queryParams['cell']);
            }
            if (isset($this->queryParams['brokerage_name'])) {
                $this->criteria->addSearchCondition("t.brokerage_name", $this->queryParams['brokerage_name']);
            }
            if (isset($this->queryParams['office_telephone'])) {
                $this->criteria->addSearchCondition("t.office_telephone", $this->queryParams['office_telephone']);
            }
            if (isset($this->queryParams['reco_number'])) {
                $this->criteria->addSearchCondition("t.state_id", $this->queryParams['cell']);
            }
            if (isset($this->queryParams['user_role'])) {
                $this->criteria->compare("t.user_role", $this->queryParams['user_role']);
            }
            if (isset($this->queryParams['subscribe'])) {
                $this->criteria->compare("t.subscribe", $this->queryParams['subscribe']);
            }
        }
    }

}
