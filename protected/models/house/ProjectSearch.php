<?php

class ProjectSearch extends ESearchModel {

    public function __construct($searchInputs, $with = null) {
        $searchInputs['order'] = 't.id DESC';
        parent::__construct($searchInputs, $with);
    }

    public function model() {
        $this->model = new Project();
    }

    public function getQueryFields() {
        return array('id', 'name', 'open_time', 'close_time', 'searchkey');
    }

    public function addQueryConditions() {
        $this->criteria->compare('t.is_deleted', StatCode::DB_ISNOT_DELETED);
        if ($this->hasQueryParams()) {
            if (isset($this->queryParams['searchkey']) && strIsEmpty($this->queryParams['searchkey']) === false) {
                $searchkey = $this->queryParams['searchkey'];
                $searchsql = "t.name = '{$searchkey}'";
                if (is_numeric($searchkey)) {
                    $searchsql.=" or t.id = {$searchkey}";
                }
                $this->criteria->addCondition($searchsql);
            } else {
                if (isset($this->queryParams['id'])) {
                    $this->criteria->compare('t.id', $this->queryParams['id']);
                }
                if (isset($this->queryParams['name'])) {
                    $this->criteria->addSearchCondition('t.name', $this->queryParams['name']);
                }
                if (isset($this->queryParams['open_time'])) {
                    $this->criteria->addCondition("t.open_time >= '" . $this->queryParams['open_time'] . "'");
                }
                if (isset($this->queryParams['close_time'])) {
                    $this->criteria->addCondition("t.close_time <= '" . $this->queryParams['close_time'] . "'");
                }
            }
        }
    }

}
