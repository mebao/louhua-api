<?php

class AgentController extends MobileagentController {

    public function filterUserContext($filterChain) {
        $user = $this->loadUser();
        if (is_null($user)) {
            $redirectUrl = $this->createUrl('agent/login');
            $currentUrl = $this->getCurrentRequestUrl();
            $redirectUrl.='?returnUrl=' . $currentUrl;
            $this->redirect($redirectUrl);
        }
        $filterChain->run();
    }

    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST requestf           
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array('register', 'ajaxRegister', 'login', 'ajaxLogin', 'ajaxFiletoken'),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array(),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionAjaxFiletoken() {
        $fileMgr = new FileManager();
        $output = $fileMgr->getUploadToken();
        $this->renderJsonOutput($output);
    }

    //进入注册页面
    public function actionRegister() {
        $form = new AgentUserForm();
        $form->user_role = StatCode::ROLE_USER;
        $this->render('register', array(
            'model' => $form,
        ));
    }

    public function actionAjaxRegister() {
        $output = array('status' => 'no');
        if (isset($_POST['RegisterForm'])) {
            $form = new AgentUserForm();
            $form->attributes = $_POST['RegisterForm'];
            $userMgr = new UserManager();
            $userMgr->registerNewUser($form);
            if ($form->hasErrors() === false) {
                //自动登录
                $userMgr->autoLoginUser($form->email, $form->password, $form->user_role, 1);
                $output['status'] = 'ok';
            } else {
                $output['errors'] = $form->getErrors();
            }
        }
        $this->renderJsonOutput($output);
    }

    public function actionLogin() {
        $form = new UserLoginForm();
        $form->user_role = StatCode::ROLE_USER;
        $this->render('login', array(
            'model' => $form,
        ));
    }

    public function actionAjaxLogin() {
        $output = array('status' => 'no');
        if (isset($_POST['LoginForm'])) {
            $form = new UserLoginForm();
            $form->attributes = $_POST['LoginForm'];
            $userMgr = new UserManager();
            if ($userMgr->doLogin($form)) {
                //自动登录
                $output['status'] = 'ok';
            } else {
                $output['errors'] = $form->getErrors();
            }
        }
        $this->renderJsonOutput($output);
    }

}
