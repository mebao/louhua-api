<?php

class PostManager {

    public function createPost($values) {
        $id = 0;
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
            $id = $model->id;
        } catch (CDbException $cdb) {
            $trans->rollback();
        } catch (Exception $e) {
            $trans->rollback();
        }
        return $id;
    }

    public function updatePost($id, $values) {
        $std = new stdClass();
        $std->status = 'no';
        $std->errorCode = 502;
        $std->errorMsg = 'update faild!';
        //检查该房源是否有人预定了
        if ($values['post_type'] == 'want') {
            //楼层高度不得超过本身楼层最高
            if (isset($values['expect_floor_high']) && strIsEmpty($values['expect_floor_high']) === false) {
                $project = Project::model()->getById($values['project_id']);
                if ($values['expect_floor_high'] > $project->level_limits) {
                    $std->errorMsg = 'this floor high must less than the project limits!';
                    return $std;
                }
            }
            $model = UserWant::model()->loadByIdAndUserId($id, $values['user_id']);
            $house = HousingResources::model()->loadWantNotUserHave($id);
        } else if ($values['post_type'] == 'have') {
            if (isset($values['floor_high']) && strIsEmpty($values['floor_high']) === false) {
                $project = Project::model()->getById($values['project_id']);
                if ($values['floor_high'] > $project->level_limits) {
                    $std->errorMsg = 'this floor level must less than the project limits!';
                    return $std;
                }
                $values['expect_floor_high'] = $values['floor_high'];
            }
            if (isset($values['floor_low']) && strIsEmpty($values['floor_low']) === false) {
                $values['expect_floor_low'] = $values['floor_low'];
            }
            $model = UserHave::model()->loadByIdAndUserId($id, $values['user_id']);
            $house = HousingResources::model()->loadHaveNotUserWant($id);
        }
        //若有 这能修改 若无 这已被预定
        if (isset($model) && isset($house)) {
            $model->setAttributes($values);
            $model->update(array_keys($values));
            $house->setAttributes($values);
            $house->update(array_keys($values));
            $std->status = 'ok';
            $std->errorCode = 200;
            $std->errorMsg = 'success';
        } else {
            $std->errorMsg = 'No authorization operation!';
        }
        return $std;
    }

    public function createMatch($values) {
        $isSuccess = 'no';
        $criteria = new CDbCriteria;
        $criteria->compare('t.is_deleted', StatCode::DB_ISNOT_DELETED);
        //可以查出来一个(修改)或者一个查不出来(创建)
        if ($values['post_type'] == 'want') {
            $criteria->addCondition("t.user_have_id is null");
            $criteria->compare('t.want_id', $values['id']);
            $updateArr = array("user_have_id" => $values['user_id'], 'user_have_name' => $values['real_name'], 'situation' => StatCode::HOUSE_SITUATION_THREE);
        } else {
            $criteria->addCondition("t.user_want_id is null");
            $criteria->compare('t.have_id', $values['id']);
            $updateArr = array("user_want_id" => $values['user_id'], 'user_want_name' => $values['real_name'], 'situation' => StatCode::HOUSE_SITUATION_THREE);
        }
        $house = HousingResources::model()->find($criteria);
        if (isset($house)) {
            $house->setAttributes($updateArr);
            if ($house->update(array_keys($updateArr))) {
                $isSuccess = 'ok';
            }
        } else {
            //重复预定
            if ($values['post_type'] == 'want') {
                $model = UserWant::model()->getById($values['id']);
                $oldmatch = HousingResources::model()->loadByWantIdAndUserHaveId($values['id'], $values['user_id']);
            } else {
                $model = UserHave::model()->getById($values['id']);
                $oldmatch = HousingResources::model()->loadByHaveIdAndUserWantId($values['id'], $values['user_id']);
            }

            if (isset($oldmatch) === false && isset($model)) {
                $newhouse = $this->createHouseSoure($model, false);
                $newhouse->setAttributes($updateArr);
                if ($newhouse->save()) {
                    $isSuccess = 'wait';
                }
            } else {
                $isSuccess = 'again';
            }
        }
        return $isSuccess;
    }

    public function createHouseSoure($model, $isSave = true) {
        $house = new HousingResources();
        $house->project_id = $model->project_id;
        $house->project_name = $model->project_name;
        $house->unit_type = $model->unit_type;
        $house->price = $model->price;
        $house->coop = $model->coop;
        $house->exposure = $model->exposure;
        $house->action = StatCode::HOUSE_ACTION_PENDING;
        $house->unit_status = StatCode::UNIT_STATUS_PENDING;
        $house->situation = StatCode::HOUSE_SITUATION_FOUR;
        $user = User::model()->getById($model->user_id);
        if ($model instanceof UserWant) {
            $house->want_id = $model->id;
            $house->user_want_id = $user->id;
            $house->user_want_name = $user->real_name;
            $house->expect_floor_low = $model->expect_floor_low;
            $house->expect_floor_high = $model->expect_floor_high;
        } elseif ($model instanceof UserHave) {
            $house->have_id = $model->id;
            $house->expect_floor_low = $model->floor_low;
            $house->expect_floor_high = $model->floor_high;
            $house->user_have_id = $user->id;
            $house->user_have_name = $user->real_name;
        }
        if ($isSave === false) {
            return $house;
        }
        if ($house->save() === false) {
            throw new CDbException("db save failed");
        }
    }

    public function deleteMyPost($type, $id, $userId) {
        $std = new stdClass();
        $std->status = 'no';
        $std->errorCode = 502;
        $std->errorMsg = 'deleted faild!';
        if ($type == 'have') {
            $model = UserHave::model()->loadByIdAndUserId($id, $userId);
            if (isset($model) && $model->delete(false) && HousingResources::model()->deleteByHaveId($id)) {
                $std->status = 'ok';
                $std->errorCode = 200;
                $std->errorMsg = 'success';
            }
        } else {
            $model = UserWant::model()->loadByIdAndUserId($id, $userId);
            if (isset($model) && $model->delete(false) && HousingResources::model()->deleteByWantId($id)) {
                $std->status = 'ok';
                $std->errorCode = 200;
                $std->errorMsg = 'success';
            }
        }
        return $std;
    }

    public function optionsProject() {
        $std = new stdClass();
        $std->status = 'ok';
        $std->errorCode = 200;
        $std->errorMsg = 'success';
        $results = new stdClass();
        $project = Project::model()->loadAllByTime();
        $results->project = arrayExtractKeyValue($project, 'id', 'name');
        $std->results = $results;
        return $std;
    }

    public function otherOptions() {
        $std = new stdClass();
        $std->status = 'ok';
        $std->errorCode = 200;
        $std->errorMsg = 'success';
        $results = new stdClass();
        $results->optionsExposure = StatCode::loadOptionsExposure();
        $results->optionsUnitType = StatCode::loadOptionsUnitType();
        $results->optionsAction = StatCode::loadOptionsHouseAction();
        $results->optionsUnitStatus = StatCode::loadOptionsHouseStatus();
        $std->results = $results;
        return $std;
    }

    public function crmpost($values) {
        $id = 0;
        $trans = Yii::app()->db->beginTransaction();
        try {
            $house = new HousingResources();
            $house->setAttributes($values);
            if ($values['post_type'] == 'want') {
                $model = new UserWant();
                $model->user_id = $values['user_want_id'];
            } else if ($values['post_type'] == 'have') {
                $model = new UserHave();
                $model->user_id = $values['user_have_id'];
                $model->floor_low = $values['expect_floor_low'];
                $model->floor_high = $values['expect_floor_high'];
            }
            $model->setAttributes($values);
            if ($model->save() === false) {
                throw new CDbException("db save failed");
            } else {
                if ($values['post_type'] == 'want') {
                    $house->want_id = $model->id;
                } else {
                    $house->have_id = $model->id;
                }
                if ($house->save()) {
                    $id = $house->id;
                }
            }
            $trans->commit();
        } catch (CDbException $cdb) {
            $trans->rollback();
        } catch (Exception $e) {
            $trans->rollback();
        }
        return $id;
    }

}
