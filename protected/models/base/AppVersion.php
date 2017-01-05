<?php

/**
 * This is the model class for table "app_version".
 *
 * The followings are the available columns in table 'app_version':
 * @property integer $id
 * @property string $app_name
 * @property string $os
 * @property string $os_version
 * @property string $device
 * @property string $app_version
 * @property string $app_version_name
 * @property integer $is_force_update
 * @property string $date_active
 * @property string $change_log
 * @property string $app_dl_url
 * @property string $app_qq_url
 * @property string $app_wandoujia_url
 * @property string $app_baidu_url
 * @property string $app_360
 * @property string $app_xiaomi
 * @property string $remark
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class AppVersion extends EActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'app_version';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('date_active, date_created', 'required'),
            array('is_force_update', 'numerical', 'integerOnly' => true),
            array('app_name', 'length', 'max' => 50),
            array('os, os_version, device, app_version, app_version_name', 'length', 'max' => 20),
            array('app_dl_url, app_qq_url, app_wandoujia_url, app_baidu_url, app_360, app_xiaomi, remark', 'length', 'max' => 200),
            array('change_log, date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, app_name, os, os_version, device, app_version, app_version_name, is_force_update, date_active, change_log, app_dl_url, app_qq_url, app_wandoujia_url, app_baidu_url, app_360, app_xiaomi, remark, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'app_name' => 'App名称',
            'os' => '设备系统',
            'os_version' => '设备系统版本',
            'device' => '什么设备',
            'app_version' => 'App版本',
            'app_version_name' => 'App版本名',
            'is_force_update' => '是否强制更新',
            'date_active' => '版本上线日期',
            'change_log' => '修改日志',
            'app_dl_url' => '默认下载链接',
            'app_qq_url' => '应用宝下载链接',
            'app_wandoujia_url' => '豌豆荚下载链接',
            'app_baidu_url' => '百度下载链接',
            'app_360' => '360下载链接',
            'app_xiaomi' => '小米下载链接',
            'remark' => '备注',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('app_name', $this->app_name, true);
        $criteria->compare('os', $this->os, true);
        $criteria->compare('os_version', $this->os_version, true);
        $criteria->compare('device', $this->device, true);
        $criteria->compare('app_version', $this->app_version, true);
        $criteria->compare('app_version_name', $this->app_version_name, true);
        $criteria->compare('is_force_update', $this->is_force_update);
        $criteria->compare('date_active', $this->date_active, true);
        $criteria->compare('change_log', $this->change_log, true);
        $criteria->compare('app_dl_url', $this->app_dl_url, true);
        $criteria->compare('app_qq_url', $this->app_qq_url, true);
        $criteria->compare('app_wandoujia_url', $this->app_wandoujia_url, true);
        $criteria->compare('app_baidu_url', $this->app_baidu_url, true);
        $criteria->compare('app_360', $this->app_360, true);
        $criteria->compare('app_xiaomi', $this->app_xiaomi, true);
        $criteria->compare('remark', $this->remark, true);
        $criteria->compare('date_created', $this->date_created, true);
        $criteria->compare('date_updated', $this->date_updated, true);
        $criteria->compare('date_deleted', $this->date_deleted, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return AppVersion the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /*     * ****** Query Methods ******* */

    public function getLastestVersionByOSAndAppName($os, $app_name) {
        // TODO: add 'date_active'. $now > $date_active.
        $criteria = new CDbCriteria();
        $criteria->addCondition('t.date_deleted is NULL');
        $criteria->compare('t.app_name', $app_name);
        $criteria->compare('t.os', $os);
        $criteria->order = 't.app_version DESC';
        $criteria->limit = 1;

        $ret = $this->findAll($criteria);
        $model = array_shift($ret);

        return $model;
    }

    public function getLatestActiveVersionByOS($os) {
        // TODO: add 'date_active'. $now > $date_active.
        $now = new CDbExpression("NOW()");
        $criteria = new CDbCriteria();
        $criteria->addCondition('t.date_deleted is NULL');
        $criteria->compare('t.os', $os);
        $criteria->addCondition('t.date_active<=:now');
        $criteria->params[':now'] = $now;
        $criteria->order = 't.app_version DESC';
        $criteria->limit = 1;

        $ret = $this->findAll($criteria);
        $model = array_shift($ret);

        return $model;
    }

    /*     * ****** Accessors ******* */

    public function getOS() {
        return $this->os;
    }

    public function getOSVersion() {
        return $this->os_version;
    }

    public function getDevice() {
        return $this->device;
    }

    public function getAppVersion() {
        return $this->app_version;
    }

    public function getAppDownloadUrl() {
        return $this->app_dl_url;
    }

    public function getIsForceUpdate() {
        if ($this->is_force_update == 1) {
            return "1";
        } else {
            return "0";
        }
    }

    public function getChangeLog() {
        return $this->change_log;
    }

    public function getDateActive() {
        return $this->date_active;
    }

}
