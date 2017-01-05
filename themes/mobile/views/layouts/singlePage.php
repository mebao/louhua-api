<!DOCTYPE html> 
<html lang="zh" xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title><?php echo $this->pageTitle; ?></title>
        <meta name="keywords" content="<?php echo $this->htmlMetaKeywords; ?>" />
        <meta name="description" content="<?php echo $this->htmlMetaDescription; ?>" />

        <!-- Bootstrap -->
        <link href="<?php echo Yii::app()->theme->baseUrl; ?>/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" />

        <!-- Custom Theme Style -->
        <link href="<?php echo Yii::app()->theme->baseUrl; ?>/css/custom.min.css" rel="stylesheet" />

        <!-- kaifa -->
        <link href="<?php echo Yii::app()->theme->baseUrl; ?>/css/common.css" rel="stylesheet" />
        <link href="<?php echo Yii::app()->theme->baseUrl; ?>/css/building.css" rel="stylesheet" />

        <!-- jQuery -->
        <script src="<?php echo Yii::app()->theme->baseUrl; ?>/vendors/jquery/dist/jquery.min.js"></script>
        <!-- Bootstrap -->
        <script src="<?php echo Yii::app()->theme->baseUrl; ?>/vendors/bootstrap/dist/js/bootstrap.min.js"></script>
        <!-- Custom Theme Scripts -->
        <script src="<?php echo Yii::app()->theme->baseUrl; ?>/js/custom.min.js"></script>
    </head>

    <body>
        <div id="section_container" <?php echo $this->createPageAttributes(); ?> >
            <section id="main_section" class="active" data-init="true">
                <?php
                if ($this->showHeader()) {
                    //$this->renderPartial('//layouts/header');
                }
                ?>
                <!-- /header -->

                <?php
                echo $content;
                ?>  
                <!-- /content -->

                <?php
                if ($this->showFooter()) {
                    // $this->renderPartial('//layouts/footer');
                }
                ?>
                <!-- /footer -->
            </section>
        </div>
    </body>
</html>