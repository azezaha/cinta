<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\TempleteAsset;

TempleteAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="extra">
  <div class="main">
    <header>
      <div class="wrapper">
        <h1><a href="index" id="logo">Around the World</a></h1>
        <div class="right">
          <div class="wrapper">
            <form id="search" action="#" method="post">
              <div class="bg">
                <input type="submit" class="submit" value="">
                <input type="text" class="input">
              </div>
            </form>
          </div>
          <div class="wrapper">
            <nav>
              <!--<ul id="top_nav">
                <li><a href="#">Register</a></li>
                <li><a href="#">Log In</a></li>
                <li><a href="#">Help</a></li>
              </ul> -->
            </nav>
          </div>
        </div>
      </div>
      <nav>
        <ul id="menu">
          <li><a href="index" class="nav1">Home</a></li>
        <!--  <li><a href="about" class="nav2">halaman 2</a></li>
          <li><a href="tours" class="nav3">halaman 3</a></li>
          <li><a href="destinations" class="nav4">halaman 4</a></li>
          <li class="end"><a href="contacts" class="nav5">Contacts</a></li> -->
        </ul>
      </nav>
      <?=$content?>
    </header>
  </div>
  <!-- <div class="block"></div> -->
</div>
<div class="body1">
  <div class="main">
    <footer>
      <div class="footerlink">
        <p class="lf">Copyright &copy; 2010 <a href="#">SiteName</a> - All Rights Reserved</p>
        <p class="rf">Design by <a href="http://www.templatemonster.com/">TemplateMonster</a></p>
        <div style="clear:both;"></div>
      </div>
    </footer>
  </div>
</div>
<script type="text/javascript"> Cufon.now(); </script>
<!-- END PAGE SOURCE -->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
