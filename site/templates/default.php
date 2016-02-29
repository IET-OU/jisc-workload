<!DOCTYPE html>
<!--[if lt IE 9]>
<html lang="en" class="ie-lt-9">
<![endif]-->
<!--[if gt IE 8]><!--> 
<html lang="en">
<!--<![endif]-->    <head>
    <title><?php echo $this->title;?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <?php 
        if(isset($page) && $page->description != null && $page->description != '') {
            echo '<meta name="description" content="' . htmlentities($page->description,ENT_COMPAT,'utf-8') . '">';
        }
        if(isset($page) && $page->keywords != null && $page->keywords != '') {
            echo '<meta name="keywords" content="' . htmlentities($page->keywords,ENT_COMPAT,'utf-8') . '">';
        }
    ?>
    <!--[if lt IE 9]>
        <script src="/js/html5shiv.js"></script>
    <![endif]--> 
    <!--<link href="/favicon.ico" rel="shortcut icon" type="image/x-icon">-->
    <link href="/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="/css/styles.css" rel="stylesheet" media="screen">
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
                    <div class="col-xs-12 workload-header">
                        <h1>Student Workload Tool</h1>
                        <div class="logos">
                            <a href="https://www.jisc.ac.uk/" title="Jisc"><img src="/css/img/jisc-logo.png" alt="Jisc Logo" title="Jisc Logo"></a>
                            <a href="http://www.open.ac.uk/" title="The Open University"><img src="/css/img/ou-logo.png" alt="OU Logo" title="OU Logo"></a>
                            <a href="/logout/" title="Logout"><img src="/css/img/logout.png" alt="Logout" title="Logout"></a>
                        </div>
                    </div>
                </div>
                <div class="row workload-content">
                    <?php
                        $this->renderRegion('main');
                    ?>
                </div>
            </div>
        </div>
        <footer id="site-footer">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <?php
                            $this->renderRegion('footer');
                        ?>
                    </div>
                </div>
            </div>
        </footer>
        <?php
            if(!isset($_SESSION['acceptedCookies']) || $_SESSION['acceptedCookies'] == false) {
                echo '<div class="cookies-notice"><table><tbody><tr><td><img src="/css/img/cookies.png" alt="Some cookies" /></td><td>This site uses <a href="http://en.wikipedia.org/wiki/HTTP_cookie" target="_blank">cookies</a>. These cookies store small bits of anonymous data on how visitors use this website. By using this website you agree that we place these cookies on your device. <a href="/accept-cookies/">Hide this notice</a></td></tr></tbody></table></div>';
            }
        ?>
        <?php
            $this->application->renderBodyScripts();
        ?>
    </body>
</html>