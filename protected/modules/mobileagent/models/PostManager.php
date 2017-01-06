<?php

class PostManager {

    public function createPost(PostForm $form) {
        if ($form->validate()) {
            $trans = Yii::app()->db->beginTransaction();
            try {
                if ($form->post_type == 'want') {
                    $model = new UserWant();
                } else if ($form->post_type == 'have') {
                    $model = new UserHave();
                } else {
                    throw new CException("choose errors");
                }
                $model->setAttributes($form->getSafeAttributes());
                if ($model->save() === false) {
                    $form->addErrors($model->getErrors());
                    throw new CDbException("db save failed");
                }
                //创建房源
                $this->createHouseSoure($model);
                $trans->commit();
            } catch (CDbException $cdb) {
                $trans->rollback();
            } catch (Exception $e) {
                $trans->rollback();
            }
        }
        return ($form->hasErrors() === false);
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

}
