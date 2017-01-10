<?php

class PostManager {

    public function createPost($values) {
        $isSuccess = false;
        $trans = Yii::app()->db->beginTransaction();
        try {
            if ($values['post_type'] == 'want') {
                $model = new UserWant();
            } else if ($values['post_type'] == 'have') {
                $model = new UserHave();
            }
            $model->setAttributes($values);
            if ($model->save() === false) {
                throw new CDbException("db save failed");
            }
            //创建房源
            $this->createHouseSoure($model);
            $trans->commit();
            $isSuccess = true;
        } catch (CDbException $cdb) {
            $trans->rollback();
        } catch (Exception $e) {
            $trans->rollback();
        }
        return $isSuccess;
    }

    public function createHouseSoure($model) {
        $house = new HousingResources();
        $house->project_id = $model->project_id;
        $house->project_name = $model->project_name;
        $house->unit_type = $model->unit_type;
        $house->price = $model->price;
        $house->coop = $model->coop;
        $house->exposure = $model->exposure;
        $house->action = StatCode::HOUSE_ACTION_PENDING;
        $house->unit_status = StatCode::UNIT_STATUS_PENDING;
        $user = AgentUser::model()->getById($model->user_id);
        if ($model instanceof UserWant) {
            $house->want_id = $model->id;
            $house->user_want_id = $user->id;
            $house->user_want_name = $user->real_name;
            $house->expect_floor_low = $model->expect_floor_low;
            $house->expect_floor_high = $model->expect_floor_high;
        } elseif ($model instanceof UserHave) {
            $house->have_id = $model->id;
            $house->floor_level = $model->floor_level;
            $house->user_have_id = $user->id;
            $house->user_have_name = $user->real_name;
        }
        if ($house->save() === false) {
            throw new CDbException("db save failed");
        }
    }

    public function deleteMyPost($id, $userId) {
        $std = new stdClass();
        $std->status = 'no';
        $std->errorCode = 502;
        $std->errorMsg = 'deleted faild!';
        $with = array('resoures');
        if (stripos($id, 'H') !== false) {
            $model = UserHave::model()->loadByIdAndUserId($id, $userId, $with);
        }
        if (stripos($id, 'W') !== false) {
            $model = UserWant::model()->loadByIdAndUserId($id, $userId, $with);
        }
        if (isset($model)) {
            $resoures = $model->resoures;
            $trans = Yii::app()->db->beginTransaction();
            try {
                if (isset($resoures) && $resoures->delete(false)) {
                    $model->delete(false);
                    $trans->commit();
                    $std->status = 'ok';
                    $std->errorCode = 200;
                    $std->errorMsg = 'success';
                }
            } catch (CDbException $cdb) {
                $trans->rollback();
            }
        }
        return $std;
    }

}
