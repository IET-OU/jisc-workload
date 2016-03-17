<?php
/**
* CControler class file
*
* @author Jitse van Ameijde <djitsz@yahoo.com>
* @copyright Copyright &copy; 2013 Quantum Frog Ltd
*
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CControler constitutes the base controller class
*
*
*/
    class CController {

        protected $_controllerId;
        protected $_moduleId;
        protected $_viewRegions;
        protected $_outputWrapper;
        protected $_delegatedController;
        protected $_pageElementTypes;
        protected $_outputFormat;
        protected $_templateOverride;

        protected $application;
        protected $title = '';

        /**
        *  constructor - initialises variables
        *
        */
        public function __construct($moduleId,$controllerId) {
            $this->_moduleId = $moduleId;
            $this->_controllerId = $controllerId;
            $this->application = CWebApplication::getInstance();
            $this->_viewRegions = array();
            $this->_delegatedController = null;
            $this->_pageElementTypes = array(
                'html' => 'HTML markup',
                'content-node' => 'content node',
                'menu' => 'menu',
                'slideshow' => 'slideshow',
                'gallery' => 'image gallery',
                'content-overview' => 'content overview',
                'web-form' => 'web form',
                'google-map' => 'google map',
                'search-field' => 'search field',
                'search-results' => 'search results',
                'rss-feed' => 'RSS feed'
            );
            $this->_outputFormat = 'html';
            $this->_templateOverride = null;
        }


        /**
        * overrideTemplate - overrides the default template for content output
        *
        * @param string $template - the name of the template
        *
        */
        public function overrideTemplate($template) {
            $this->_templateOverride = $template;
        }

        /**
        *  renderPartial - renders the indicated view without embedding it in the layout
        *
        * @param string $view - the name of the view to render
        * @param array $viewVars - array of variables to make accessible to the view
        */
        public function renderPartial($view, $viewVars = null) {
            if($viewVars) {
                foreach($viewVars as $name => $value) {
                    $$name = $value;
                }
            }
            $controllerFolder = strtolower(preg_replace_callback('/[a-z]([A-Z])/',function($matches) {
                return substr($matches[0],0,1) . '-' . $matches[1];
            },$this->_controllerId));
            $viewFile = $this->application->viewRoot . DIRECTORY_SEPARATOR . strtolower($this->_moduleId) . DIRECTORY_SEPARATOR . $controllerFolder . DIRECTORY_SEPARATOR . $view . '.php';
            include($viewFile);
        }

        /**
        * render - renders the view
        *
        * @param array $viewVars - array of variables to make accessible to the view
        */
        public function render($viewVars = null) {
            if($viewVars) {
                foreach($viewVars as $name => $value) {
                    $$name = $value;
                }
            }
            // Ignore Code Climate warning - 'webroot' used in included template!
            $webroot = $this->application->webRoot();
            $template = 'default';
            $templateFile = $this->application->templateRoot . DIRECTORY_SEPARATOR . $template . '.php';
            include($templateFile);
        }

        /**
        * renderSnippet - renders a snipped with the provided view vars
        *
        * @param string $snippet - name of the snippet to render
        * @param array $viewVars - array of variables to make accessible to the snippet
        */
        public function renderSnippet($snippet, $viewVars = null) {
            if($viewVars) {
                foreach($viewVars as $name => $value) {
                    $$name = $value;
                }
            }
            $snippetFile = $this->application->frameworkRoot . DIRECTORY_SEPARATOR . 'snippets' . DIRECTORY_SEPARATOR . $snippet . '.php';
            include($snippetFile);
        }


        /**
        * attachViewToRegion - attaches the specified view to the specified hook
        *
        * @param string $region - the region to attach the specified view to
        * @param string $viewCollection - the collection in which to find the view
        * @param string $view - the view to attach to the hook
        * @param array $viewVars - an array of key=>value pairs which represent variables to be made available to the view
        * @param string $wrapperClass - the class name of an optional container to wrap the view in
        */
        public function attachViewToRegion($region,$viewCollection, $view,$viewVars = array(), $wrapperClass = null) {
            if(!isset($this->_viewRegions[$region])) $this->_viewRegions[$region] = array();
            $viewVars[ 'webroot' ] = $this->application->webRoot();
            $this->_viewRegions[$region][] = array('view'=>$view,'viewCollection'=>$viewCollection,'viewVars'=>$viewVars,'wrapperClass'=>$wrapperClass);
        }

        /**
        * renderRegion - renders all elements attached to the specified region. Normally called by the template to place the
        * various elements within the overall layout
        *
        * @param string $region - the name of the hook to render
        */
        public function renderRegion($region) {
            if(isset($this->_viewRegions[$region])) {
/*                $controllerFolder = strtolower(preg_replace_callback('/[a-z]([A-Z])/',function($matches) {
                    return substr($matches[0],0,1) . '-' . $matches[1];
                },$this->_controllerId));*/

                foreach($this->_viewRegions[$region] as $_view_) {
                    $viewFile = $this->application->viewRoot . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $_view_['viewCollection'] . DIRECTORY_SEPARATOR . $_view_['view'] . '.php';
                    if($_view_['viewVars'] != null) {
                        foreach($_view_['viewVars'] as $name => $value) {
                            $$name = $value;
                        }
                    }
                    if($_view_['wrapperClass'] != null) echo '<div class="' . $_view_['wrapperClass'] . '">';
                    include($viewFile);
                    if($_view_['wrapperClass'] != null) echo '</div>';
                    if($this->application->user->isEditor() && isset($pageElement) && $this->_outputFormat == 'html') {
                        echo '</div>';
                    }
                }
            }
        }

        /**
        * countElements - returns the number of elements attached to the specified region
        *
        * @param mixed $region
        */
        public function countElements($region) {
            return isset($this->_viewRegions[$region]) ? count($this->_viewRegions[$region]) : 0;
        }

        /**
        * onBeforeAction function called before the controller action is invoked
        *
        */
        public function onBeforeAction() {

        }

        /**
        * onAfterAction function called before the controller action is invoked
        *
        */
        public function onAfterAction() {

        }

        /**
        * setOutputWrapper - sets a wrapper for wrapping the output of the rendered views
        * For instance, wrapping the output in a dialog box markup
        *
        * @param CWrapper $wrapper
        */

        public function setOutputWrapper($wrapper) {
            $this->_outputWrapper = $wrapper;
        }
    }
