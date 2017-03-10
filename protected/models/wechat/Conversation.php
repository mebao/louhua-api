<?php

/**
 * This is the model class for table "conversation".
 *
 * The followings are the available columns in table 'conversation':
 * @property integer $id
 * @property integer $admin_id
 * @property string $admin_name
 * @property integer $user_id
 * @property string $wx_userid
 * @property string $user_name
 * @property string $channel
 * @property integer $is_closed
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 * @property integer $is_deleted
 */
class Conversation extends EActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'conversation';
    }

    const DB_ISCLOSED = 1;
    const DB_NOTCLOSED = 0;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('date_created', 'required'),
            array('admin_id, user_id, is_closed, is_deleted', 'numerical', 'integerOnly' => true),
            array('admin_name, wx_userid, user_name, channel', 'length', 'max' => 50),
            array('date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, admin_id, admin_name, user_id, wx_userid, user_name, channel, is_closed, date_created, date_updated, date_deleted, is_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'message' => array(self::HAS_ONE, 'AdminMessage', 'conversation_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'admin_id' => 'Admin',
            'admin_name' => 'Admin Name',
            'user_id' => 'User',
            'wx_userid' => 'Wx Userid',
            'user_name' => 'User Name',
            'channel' => 'Channel',
            'is_closed' => 'Is Closed',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
            'is_deleted' => 'Is Deleted',
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
        $criteria->compare('admin_id', $this->admin_id);
        $criteria->compare('admin_name', $this->admin_name, true);
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('wx_userid', $this->wx_userid, true);
        $criteria->compare('user_name', $this->user_name, true);
        $criteria->compare('channel', $this->channel, true);
        $criteria->compare('is_closed', $this->is_closed);
        $criteria->compare('date_created', $this->date_created, true);
        $criteria->compare('date_updated', $this->date_updated, true);
        $criteria->compare('date_deleted', $this->date_deleted, true);
        $criteria->compare('is_deleted', $this->is_deleted);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Conversation the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function checkUser($id) {
        $model = $this->getByAttributes(array('user_id' => $id, 'is_closed' => self::DB_NOTCLOSED));
        if (isset($model)) {
            return true;
        } else {
            return false;
        }
    }

    public function loadByIdAndAdminId($id, $adminId) {
        return $this->getByAttributes(array('id' => $id, 'admin_id' => $adminId));
    }

    public function loadByWxUserId($wxuserid, $with = null) {
        return $this->getByAttributes(array('wx_userid' => $wxuserid, 'is_closed' => self::$DB_NOTCLOSED), $with);
    }

}
