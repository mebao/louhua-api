<?php

class MobileagentModule extends CWebModule {

    public function init() {
        parent::init();
        Yii::setPathOfAlias('mobileagent', dirname(__FILE__));

        // this method is called when the module is being created
        // you may place code here to customize the module or the application
        // import the module-level models and components

        $this->setImport(array(
            'mobileagent.components.*',
            'mobileagent.models.*',
            'mobileagent.models.user.*',
        ));

        Yii::app()->setComponents(array(
            'errorHandler' => array(
                'class' => 'CErrorHandler',
                'errorAction' => $this->getId() . '/home/error',
            ),
            'user' => array(
                // enable cookie-based authentication
                //'allowAutoLogin' => true,
                'loginUrl' => array('/mobileagent/agent/mobileLogin'),
            ),
                ), true);

        $this->setTheme('mobile');
        $this->defaultController = 'agent';
    }

    public function beforeControllerAction($controller, $action) {
        if (parent::beforeControllerAction($controller, $action)) {
            // this method is called before any module controller action is performed
            // you may place customized code here
            return true;
        } else
            return false;
    }

    private function setTheme($theme, $setViewPath = true) {
        // set theme.
        Yii::app()->theme = $theme;
        // set theme url & path.
        Yii::app()->themeManager->setBaseUrl(Yii::app()->theme->baseUrl);
        Yii::app()->themeManager->setBasePath(Yii::app()->theme->basePath);
        // set module viewPath.
        if ($setViewPath) {
            $this->setViewPath(Yii::app()->theme->basePath . '/views');
        }
    }

}
