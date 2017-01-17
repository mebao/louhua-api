<?php

/**
 * This is the model class for table "auth_token_user".
 *
 * The followings are the available columns in table 'auth_token_user':
 * @property integer $id
 * @property string $token
 * @property string $username
 * @property integer $role
 * @property integer $user_id
 * @property integer $is_active
 * @property integer $time_expiry
 * @property string $user_host_ip
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class AuthTokenUser extends EActiveRecord {

    const EXPIRY_DEFAULT = 31536000;    //one year
    const ERROR_NONE = 0;
    const ERROR_NOT_FOUND = 1;
    const ERROR_INACTIVE = 2;
    const ERROR_EXPIRED = 3;

    public $error_code;
    private $verified = false;  // flag indicating if token is verified.

    /**
     * @return string the associated database table name
     */

    public function tableName() {
        return 'auth_token_user';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('date_created', 'required'),
            array('user_id, role, is_active, time_expiry', 'numerical', 'integerOnly' => true),
            array('token', 'length', 'max' => 64),
            array('username, user_host_ip', 'length', 'max' => 50),
            array('date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, token, username, role, user_id, is_active, time_expiry, user_host_ip, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'token' => 'App用户权限',
            'username' => '用户名',
            'role' => '用户角色',
            'user_id' => '用户id',
            'is_active' => '是否可用',
            'time_expiry' => '到期时间',
            'user_host_ip' => '用户ip地址',
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
        $criteria->compare('token', $this->token, true);
        $criteria->compare('username', $this->username, true);
        $criteria->compare('role', $this->role);
        $criteria->compare('user_id', $this->user_id);
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
     * @return AuthTokenUser the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function deActivateToken() {
        $this->setIsActive(false);
        $this->date_updated = new CDbExpression("NOW()");
        return $this->update(array('is_active', 'date_updated'));
    }

    public function checkExpiry() {
        if (is_null($this->time_expiry)) {
            return true;
        } else {
            $now = time();
            return ($this->time_expiry > $now);
        }
    }

    private function strRandom($length = 40) {
        $chars = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        shuffle($chars);
        $ret = implode(array_slice($chars, 0, $length));

        return ($ret);
    }

    private function setToken() {
        $this->token = strtoupper(substr(str_shuffle(MD5(microtime())), 0, 32));   // refer to helper.php
    }

    private function setIsActive($v) {
        $this->is_active = $v === true ? 1 : 0;
    }

    public function deActivateAllUserOldTokens() {
        $now = new CDbExpression("NOW()");
        return $this->updateAllByAttributes(array('is_active' => 0, 'date_updated' => $now), array('user_id' => $this->user_id, 'role' => $this->role, 'is_active' => '1'));
    }

    public function isTokenValid() {
        return ($this->verified && $this->error_code === self::ERROR_NONE);
    }

    public function createTokenUser($userId, $username, $userRole, $userHostIp) {
        return $this->createToken($userId, $username, $userRole, $userHostIp);
    }

    public function createToken($userId, $username, $userRole, $userHostIp) {
        $this->user_id = $userId;
        $this->username = $username;
        $this->role = $userRole;
        $this->setToken();
        $this->setTimeExpiry();
        $this->user_host_ip = $userHostIp;
        $this->setIsActive(true);
    }

    private function setTimeExpiry() {
        $duration = self::EXPIRY_DEFAULT;
        $now = time();
        $this->time_expiry = $now + $duration;
    }

    // 验证 token。
    public function verifyTokenUser($token, $username) {
        return $this->verifyByTokenAndUsernameAndRole($token, $username, StatCode::ROLE_USER);
    }

    public function verifyByTokenAndUsernameAndRole($token, $username, $userRole) {
        $model = $this->getByTokenAndUsernameAndRole($token, $username, $userRole, true);
        if (isset($model)) {
            $model->verifyToken();
            return $model;
        } else {
            return null;
        }
    }

    private function getByTokenAndUsernameAndRole($token, $username, $userRole, $isActiveFlag = true) {
        $isActive = $isActiveFlag === true ? 1 : 0;
        $model = $this->getByAttributes(array('token' => $token, 'username' => $username, 'role' => $userRole, 'is_active' => $isActive));
        if (isset($model)) {
            return $model;
        }
        return null;
    }

    public function verifyToken() {
        if ($this->checkExpiry()) {
            $this->error_code = self::ERROR_NONE;
        } else {
            $this->error_code = self::ERROR_EXPIRED;
        }
        $this->verified = true;
    }

    public function getFirstActiveByUserIdAndRole($userId, $role = StatCode::ROLE_USER) {
        return $this->getByAttributes(array('user_id' => $userId, 'is_active' => '1', 'role' => $role));
    }

    public function getUser() {
        return $this->user;
    }

}
