<?php

class AgentSearch extends ESearchModel {

    public function __construct($searchInputs, $with = null) {
        $searchInputs['order'] = 't.id DESC';
        parent::__construct($searchInputs, $with);
    }

    public function model() {
        $this->model = new User();
    }

    public function getQueryFields() {
        return array('id', 'wechat_id', 'wechat_name', 'email', 'real_name', 'cell', 'brokerage_name', 'office_telephone', 'reco_number', 'user_role', 'subscribe', 'searchkey');
    }

    public function addQueryConditions() {
        $this->criteria->compare('t.is_deleted', StatCode::DB_ISNOT_DELETED);
        if (isset($this->queryParams['user_role'])) {
            $this->criteria->compare("t.user_role", $this->queryParams['user_role']);
        } else {
            $this->criteria->addCondition('t.user_role = ' . StatCode::ROLE_USER . ' or t.user_role = ' . StatCode::ROLE_OTHER);
        }
        if ($this->hasQueryParams()) {
            if (isset($this->queryParams['searchkey']) && strIsEmpty($this->queryParams['searchkey']) === false) {
                $searchkey = $this->queryParams['searchkey'];
                $searchsql = "t.wechat_id = '{$searchkey}' or t.wechat_name = '{$searchkey}' or t.username = '{$searchkey}' "
                        . "or t.real_name = '{$searchkey}' or t.cell = '{$searchkey}' or t.brokerage_name = '{$searchkey}' or t.office_telephone = '{$searchkey}' "
                        . "or t.reco_number = '{$searchkey}'";
                if (is_numeric($searchkey)) {
                    $searchsql.=" or t.id = {$searchkey}";
                }
                $this->criteria->addCondition($searchsql);
            } else {
                if (isset($this->queryParams['id'])) {
                    $this->criteria->compare('t.id', $this->queryParams['id']);
                }
                if (isset($this->queryParams['wechat_id'])) {
                    $this->criteria->addSearchCondition('t.wechat_id', $this->queryParams['wechat_id']);
                }
                if (isset($this->queryParams['wechat_name'])) {
                    $this->criteria->addSearchCondition('t.wechat_name', $this->queryParams['wechat_name']);
                }
                if (isset($this->queryParams['email'])) {
                    $this->criteria->addSearchCondition('t.username', $this->queryParams['email']);
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
                if (isset($this->queryParams['subscribe'])) {
                    $this->criteria->compare("t.subscribe", $this->queryParams['subscribe']);
                }
            }
        }
    }

}
