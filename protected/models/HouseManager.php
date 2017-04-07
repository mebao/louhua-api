<?php

class HouseManager {

    public function updateProject($id, $values) {
        $std = new stdClass();
        $std->status = 'no';
        $std->errorCode = 502;
        $std->errorMsg = 'update failed';
        $model = Project::model()->getById($id);
        if (isset($model)) {
            $model->setAttributes($values);
            if ($model->update(array_keys($values))) {
                $std->status = 'ok';
                $std->errorCode = 200;
                $std->errorMsg = 'success';
            }
        } else {
            $std->errorMsg = 'project is null';
        }
        return $std;
    }

    public function deleteProject($id) {
        $std = new stdClass();
        $std->status = 'no';
        $std->errorCode = 502;
        $std->errorMsg = 'delete failed';
        $model = Project::model()->getById($id);
        if (isset($model)) {
            if ($model->delete(false)) {
                $std->status = 'ok';
                $std->errorCode = 200;
                $std->errorMsg = 'success';
            }
        } else {
            $std->errorMsg = 'project is null';
        }
        return $std;
    }

    public function addPicture($values) {
        $std = new stdClass();
        $std->status = 'no';
        $std->errorCode = 502;
        $std->errorMsg = 'create failed';
        $model = new AdvertisingPictures();
        $model->setAttributes($values);
        if ($model->save()) {
            $std->status = 'ok';
            $std->errorCode = 200;
            $std->errorMsg = 'success';
        }
        return $std;
    }

    public function deletePicture($id) {
        $std = new stdClass();
        $std->status = 'no';
        $std->errorCode = 502;
        $std->errorMsg = 'delete failed';
        $model = AdvertisingPictures::model()->getById($id);
        if (isset($model)) {
            if ($model->delete(true)) {
                $std->status = 'ok';
                $std->errorCode = 200;
                $std->errorMsg = 'success';
            }
        }
        return $std;
    }

    public function taskFinish($id, $adminId) {
        $std = new stdClass();
        $std->status = 'no';
        $std->errorCode = 502;
        $std->errorMsg = 'finish failed';
        $house = HousingResources::model()->getByAttributes(array('id' => $id, 'admin_id' => $adminId));
        if (isset($house)) {
            $house->admin_id = null;
            $house->update(array('admin_id'));
            $std->status = 'ok';
            $std->errorCode = 200;
            $std->errorMsg = 'success';
        }
        return $std;
    }

    public function matchsuccess($values) {
        $std = new stdClass();
        $std->status = 'no';
        $std->errorCode = 502;
        $std->errorMsg = 'operation failed';
        $house = HousingResources::model()->getById($values['id']);
        $house->unit_status = StatCode::HOUSE_ACTION_DONE;
        $house->action = StatCode::UNIT_STATUS_MATCHED;
        $house->admin_id = null;
        if ($house->update(array('unit_status', 'action', 'admin_id'))) {
            //删除条件
            $criteria = new CDbCriteria;
            $criteria->addCondition("id !=" . $values['id']);
            if ($values['type'] === 'have') {
                $criteria->compare('have_id', $house->have_id);
            } else {
                $criteria->compare('want_id', $house->want_id);
            }
            $now = date('Y-m-d H:i:s');
            if (HousingResources::model()->updateAll(array('is_deleted' => StatCode::DB_IS_DELETED, 'date_deleted' => $now), $criteria)) {
                $std->status = 'ok';
                $std->errorCode = 200;
                $std->errorMsg = 'success';
            }
        }
        return $std;
    }

}
