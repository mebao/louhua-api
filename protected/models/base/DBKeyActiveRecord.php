<?php

class DBKeyActiveRecord extends EActiveRecord {

    public function getDbConnection() {
        return Yii::app()->dbkey;
    }

}
