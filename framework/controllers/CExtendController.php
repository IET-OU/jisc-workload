<?php
/**
* CExtendController class file
*
* Jisc / OU Student Workload Tool.
*
* @license   http://gnu.org/licenses/gpl.html GPL-3.0+
* @author    Nick Freear
* @copyright 2015 The Open University.
*/

defined('ALL_SYSTEMS_GO') or die;

/**
* CExtendController implements various functionality used by all controllers.
*
*
*/

class CExtendController extends CController
{
    protected $title = 'Student Workload Tool';

    public function __construct($p1 = null, $p2 = null) {
        parent::__construct('admin', 'Default');

        $this->attachViewToRegion('googleAnalytics', 'default', 'googleAnalytics',
            array('analyticsId' => $this->application->config[ 'googleAnalyticsId' ]));
    }

    /**
    * @return string  Return a HTTP GET parameter.
    */
    protected function getParam($key, $filter = FILTER_SANITIZE_STRING) {
        return filter_input(INPUT_POST, $key, $filter);
    }

    /**
    * @return string  Return a HTTP POST value.
    */
    protected function post($key, $filter = FILTER_SANITIZE_STRING) {
        return filter_input(INPUT_POST, $key, $filter);
    }
}
