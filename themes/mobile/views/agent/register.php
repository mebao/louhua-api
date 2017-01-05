<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->theme->baseUrl . '/vendors/switchery/dist/switchery.min.css');
Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl . '/vendors/switchery/dist/switchery.min.js', CClientScript::POS_END);
?>
<div class=""login>
    <div class="login_wrapper">
        <section class="login_content">
            <form class="form-horizontal">
                <div class="text-center">
                    <div class="uploadTab">头像</div>
                </div>
                <div class="form-group">
                    <label for="accountId" class="col-xs-4 control-label text-left">
                        Account ID:
                    </label>
                    <div class="col-xs-8 text-left pt8">
                        123456
                    </div>
                </div>
                <div class="form-group">
                    <label for="wechatId" class="col-xs-4 control-label text-left">
                        WeChat ID:
                    </label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="wechatId" required="" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="fullName" class="col-xs-4 control-label text-left">
                        Full Name:
                    </label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="fullName" required="">
                    </div>
                </div>
                <div class="form-group">
                    <label for="company" class="col-xs-4 control-label text-left">
                        Company:
                    </label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="company" required="">
                    </div>
                </div>
                <div class="form-group">
                    <label for="cellPhone" class="col-xs-4 control-label text-left">
                        Cell Phone:
                    </label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="cellPhone" required="">
                    </div>
                </div>
                <div class="form-group">
                    <label for="officePhone" class="col-xs-4 control-label text-left">
                        Office Phone:
                    </label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="officePhone" required="">
                    </div>
                </div>
                <div class="form-group">
                    <label for="recoNumber" class="col-xs-4 control-label text-left">
                        Reco Number:
                    </label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" id="recoNumber" required="">
                    </div>
                </div>
                <div class="form-group">
                    <label for="subscribe" class="col-xs-4 control-label text-left">
                        Subscribe:
                    </label>
                    <div class="col-xs-8">
                        <input type="checkbox" class="js-switch" checked />
                    </div>
                </div>

                <div>
                    <a class="btn btn-default submit" href="#">Log in</a>
                </div>
            </form>
        </section>
    </div>
</div>