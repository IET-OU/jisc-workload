<!DOCTYPE html>
<!--[if lt IE 9]>
<html lang="en" class="ie-lt-9">
<![endif]-->
<!--[if gt IE 8]><!-->
<html lang="en">
<!--<![endif]-->    <head>
    <title><?php echo $this->title;?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <?php
        if(isset($page) && $page->description != null && $page->description != '') {
            echo '<meta name="description" content="' . htmlentities($page->description,ENT_COMPAT,'utf-8') . '">';
        }
        if(isset($page) && $page->keywords != null && $page->keywords != '') {
            echo '<meta name="keywords" content="' . htmlentities($page->keywords,ENT_COMPAT,'utf-8') . '">';
        }
    ?>
    <!--[if lt IE 9]>
        <script src="<?= $webroot ?>/js/html5shiv.js"></script>
    <![endif]-->
    <!--<link href="/favicon.ico" rel="shortcut icon" type="image/x-icon">-->
    <link href="<?= $webroot ?>/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="<?= $webroot ?>/css/styles.css" rel="stylesheet" media="screen">
    <?php
        $this->application->renderHeaderScripts();
    ?>
    </head>

    <body>
        <?php
            if($this->countElements('adminBar') > 0) {
                $this->renderRegion('adminBar');
                echo '<div style="height:50px;"></div>';
            }
        ?>

        <div id="site-body">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 workload-header" role="banner">
                        <h1>Student Workload Tool</h1>
                        <div class="logos">
                            <a href="https://www.jisc.ac.uk/"><img src="<?= $webroot ?>/css/img/jisc-logo.png" alt="Jisc" title="Jisc"></a>
                            <a href="https://www.open.ac.uk/"><img src="<?= $webroot ?>/css/img/ou-logo.png" alt="The Open University" title="The Open University"></a>
                            <a href="<?= $webroot ?>/logout/"><img src="<?= $webroot ?>/css/img/logout.png" alt="Logout" title="Logout"></a>
                        </div>
                    </div>
                </div>
                <div class="row workload-content" role="main">
                    <?php
                        $this->renderRegion('main');
                    ?>
                </div>
            </div>
        </div>
        <footer id="site-footer" role="contentinfo">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">

                      <p>
                        <a href="https://www.open.ac.uk" title="Copyright &copy; The Open University (IET)">&copy; The Open University</a>
                        |
                        <a href="https://www.open.ac.uk/privacy">Privacy policy</a>
                      </p>

                        <?php
                            $this->renderRegion('footer');
                        ?>
                    </div>
                </div>
            </div>
        </footer>
        <?php
            if(!isset($_SESSION['acceptedCookies']) || $_SESSION['acceptedCookies'] == false):
        ?>
            <div class="cookies-notice" role="alert"><table><tbody><tr><td><img src="<?= $webroot ?>/css/img/cookies.png" alt="" /></td>
            <td>This site uses <a href="https://en.wikipedia.org/wiki/HTTP_cookie" target="_blank">cookies</a>.
            These cookies store small bits of anonymous data on how visitors use this website.
            By using this website you agree that we place these cookies on your device.
            <a href="<?= $webroot ?>/accept-cookies/">Hide this notice</a></td></tr></tbody></table></div>
        <?php
            endif;
        ?>
        <?php
            $this->renderRegion('googleAnalytics');
            $this->application->renderBodyScripts();
        ?>
    </body>
</html>
