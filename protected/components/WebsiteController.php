<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
abstract class WebsiteController extends Controller {

    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout = '//layouts/layoutMain';
    public $defaultAction = 'index';

    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = array();
    public $pageTitle = '新楼花';
    public $htmlMetaKeywords = '新楼花';
    public $htmlMetaDescription = "新楼花";

    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs = array();
    public $content_container = 'container page-container';
    public $site_menu = null;
    public $show_header = true;
    public $show_header_navbar = true;
    public $show_footer = true;
    public $show_traffic_script = true;
    public $show_baidushangqiao = true;

    public function init() {
        if (isset(Yii::app()->theme)) {
            Yii::app()->clientScript->scriptMap = array(
            );
        }

        Yii::app()->clientScript->registerCoreScript('jquery');

        // show header.
        if (isset($_GET['header']) && $_GET['header'] != 1) {
            $this->show_header = false;
        }
        // show footer.
        if (isset($_GET['footer']) && $_GET['footer'] != 1) {
            $this->show_footer = false;
        }
        $this->storeUserAccessInfo();
        return parent::init();
    }

    /**
     * Performs the AJAX validation.
     * @param User $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        //if (isset($_POST['ajax']) && $_POST['ajax'] === 'user-form') {
        if (isset($_POST['ajax'])) {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function showHeader() {
        return $this->show_header;
    }

    public function showFooter() {
        return $this->show_footer;
    }

    public function getHomeUrl() {
        return Yii::app()->homeUrl;
    }

    public function setPageTitle($title, $siteName = true) {
        if ($siteName) {
            $this->pageTitle = Yii::app()->name . ' - ' . $title;
        } else {
            $this->pageTitle = $title;
        }
    }

    public function getFlashMessage($key) {
        if (Yii::app()->user->hasFlash($key))
            return Yii::app()->user->getFlash($key);
        else
            return null;
    }

    public function setFlashMessage($key, $msg) {
        Yii::app()->user->setFlash($key, $msg);
    }

    public function hasFlashMessage($key) {
        return Yii::app()->user->hasFlash($key);
    }

    public function getSession($key, $unset = false) {
        $value = Yii::app()->session[$key];
        if ($unset) {
            unset(Yii::app()->session[$key]);
        }
        return $value;
    }

    public function setSession($key, $value) {
        Yii::app()->session[$key] = $value;
    }

    public function clearSession($key) {
        unset(Yii::app()->session[$key]);
    }

    public function addHttpSession($key, $value) {
        $session = new CHttpSession;
        $session->open();
        $session[$key] = $value;
    }

    /*
      public function renderJsonOutput($data) {
      header('Content-Type: application/json; charset=utf-8');
      echo CJSON::encode($data);
      foreach (Yii::app()->log->routes as $route) {
      if ($route instanceof CWebLogRoute) {
      $route->enabled = false; // disable any weblogroutes
      }
      }
      Yii::app()->end();
      }
     */

    public function isAjaxRequest() {
        if (Yii::app()->request->isAjaxRequest) {
            return true;
        } else {
            return ((isset($_GET['ajax']) && $_GET['ajax'] == 1) || (isset($_POST['ajax']) && $_POST['ajax'] == 1));
        }
    }

    public function throwPageNotFoundException($code = 404) {
        throw new CHttpException($code, 'The requested page does not exist.');
    }

    public function loadTrafficAnalysisScript($filterDomain = true) {
        $show = true;
        if ($filterDomain) {
            $baseUrl = Yii::app()->getBaseUrl(true);
            if (strStartsWith($baseUrl, 'http://localhost') || strStartsWith($baseUrl, 'http://127.0.0.1')) {
                $show = false;
            }
        }
        if ($show) {
            $this->renderPartial('//layouts/_scriptTrafficAnalysis');
        }
    }

    public function handleMobileBrowserRedirect($defaultUrl = null) {
        $detect = Yii::app()->mobileDetect;
        // client is mobile and url is not mobile.
        if ($detect->isMobile()) {
            $this->redirect(Yii::app()->params['baseUrlMobile']);
            /*
              $cookieName = "client.browsermode";
              if (isset(Yii::app()->request->cookies[$cookieName]) == false || Yii::app()->request->cookies[$cookieName] != "pc") {
              $this->redirect(Yii::app()->params['baseUrlMobile']);
              }
             * 
             */
        }
    }

    public function isBaseUrlMobile() {
        // get rule like 'http://m.example.com'.
        $baseUrl = Yii::app()->getBaseUrl(true);
        /*
          // remove 'http://' in url.
          $baseUrl = str_ireplace('http://', '', $baseUrl);
          // if starts with 'm.'.
          return (strpos($baseUrl, 'm.') === 0);
         */
        return $baseUrl == Yii::app()->params['baseUrlMobile'];
    }

    public function setBrowserInSession($browser) {
        Yii::app()->session['client.browser'] = $browser;
    }

    /**
     * Stores user's access info for every request.
     */
    public function storeUserAccessInfo() {
        $coreAccess = new CoreAccess();
        $coreAccess->user_host_ip = Yii::app()->request->getUserHostAddress();
        $coreAccess->url = Yii::app()->request->getUrl();
        $coreAccess->url_referrer = Yii::app()->request->getUrlReferrer();
        $coreAccess->user_agent = Yii::app()->request->getUserAgent();
        $coreAccess->user_host = Yii::app()->request->getUserHost();
        $coreAccess->save();
    }

}
