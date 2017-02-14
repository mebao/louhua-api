<?php

/**
 * This is the model class for table "auth_sms_verify".
 *
 * The followings are the available columns in table 'auth_sms_verify':
 * @property integer $id
 * @property string $email
 * @property string $code
 * @property integer $is_active
 * @property integer $time_expiry
 * @property string $user_host_ip
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class AuthSmsVerify extends EActiveRecord {

    const SMS_ACTION = 1;
    const SMS_NOTACTION = 0;
    const EXPIRY_DEFAULT = 1200; //20分钟

    private $verified = false;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'auth_sms_verify';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('email, code', 'required'),
            array('is_active, time_expiry', 'numerical', 'integerOnly' => true),
            array('email', 'length', 'max' => 50),
            array('code', 'length', 'max' => 32),
            array('user_host_ip', 'length', 'max' => 15),
            array('date_created, date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, email, code, is_active, time_expiry, user_host_ip, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
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
            'email' => 'Email',
            'code' => 'Code',
            'is_active' => 'Is Active',
            'time_expiry' => 'Time Expiry',
            'user_host_ip' => 'User Host Ip',
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
        $criteria->compare('email', $this->email, true);
        $criteria->compare('code', $this->code, true);
        $criteria->compare('is_active', $this->is_active);
        $criteria->compare('time_expiry', $this->time_expiry);
        $criteria->compare('user_host_ip', $this->user_host_ip, true);
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
     * @return AuthSmsVerify the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function initModel($email, $userIp = null) {
        $this->email = $email;
        $this->createCode();
        $this->setExpiryTime();
        $this->user_host_ip = $userIp;
        $this->is_active = self::SMS_ACTION;
    }

    private function createCode() {
        $this->code = md5(time());
        //$this->code = rand(100000, 1000000);
    }

    private function setExpiryTime() {
        $duration = self::EXPIRY_DEFAULT;
        $now = time();
        $this->time_expiry = $now + $duration;  // ms
    }

    private function checkExpiry() {
        if (is_null($this->time_expiry)) {
            return true;
        } else {
            $now = time();
            return ($this->time_expiry > $now);
        }
    }

    public function isActive() {
        return $this->is_active == self::SMS_ACTION;
    }

    public function deActivateRecord() {
        $this->is_active = self::SMS_NOTACTION;
        $this->date_updated = new CDbExpression("NOW()");
        return $this->update(array('is_active', 'date_updated'));
    }

    public function deActivateAllRecords() {
        $now = new Datetime();
        $nowDatestr = $now->format(self::DB_FORMAT_DATETIME);
        //$now = time();
        $this->updateAllByAttributes(array('is_active' => self::SMS_NOTACTION, 'date_updated' => $nowDatestr), array('email' => $this->email, 'is_active' => self::SMS_ACTION));
    }

    public function createRecord($email, $deActivate = false, $userIp = null) {
        $this->initModel($email, $userIp);
        if ($deActivate) {
            $this->deActivateAllRecords();
        }
        return $this->save();
    }

    public function isValid() {
        return ($this->verified && $this->hasErrors() === false);
    }

    /**
     * 
     * @param type $deactivateExpired   冻结该记录,如果已过期
     * @param type $deactivate          冻结所有属于该eamil的记录,如果没过期
     */
    public function checkValidity($deactivateExpired = true, $deactivate = false) {
        if ($this->isActive()) {
            $notExpired = $this->checkExpiry(); // true means not expired.
            if ($notExpired) {    //没过期
                if ($deactivate) {
                    // 冻结所有该 action_type 的验证码
                    $this->deActivateAllRecords();
                }
            } else {  // 已过期
                if ($deactivateExpired) {    // 冻结该验证码.
                    $this->deActivateRecord();
                }
                $this->addError('code', 'expired');
            }
        } else {
            $this->addError('code', 'used');
        }
        $this->verified = true;
    }

    public function loadByEmailAndCode($email, $code) {
        return $this->getByAttributes(array("email" => $email, 'code' => $code));
    }

}
