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
        $std->errorMsg = 'update failed';
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

    public function addPicture($id, $url) {
        $std = new stdClass();
        $std->status = 'no';
        $std->errorCode = 502;
        $std->errorMsg = 'created failed';
        $model = new AdvertisingPictures();
        $model->project_id = $id;
        $model->picture_url = $url;
        if ($model->save()) {
            if ($model->delete(false)) {
                $std->status = 'ok';
                $std->errorCode = 200;
                $std->errorMsg = 'success';
            }
        }
        return $std;
    }

}
