<?php
/**
* CJsonController class file
* 
* @author Jitse van Ameijde <djitsz@yahoo.com>
* @copyright Copyright &copy; 2013 Quantum Frog Ltd
* 
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CJsonController constitutes the base controller class
* 
* 
*/
    class CJsonController extends CController {
        
        /**
        *  constructor - initialises variables
        * 
        */
        public function __construct($moduleId,$controllerId) {
            parent::__construct($moduleId,$controllerId);
        }
        
        /**
        * onBeforeAction function called before the controller action is invoked
        * 
        */
        public function onBeforeAction() {
            ob_start();
        }
        
        /**
        * onAfterAction function called before the controller action is invoked
        * 
        */
        public function onAfterAction() {
            if($this->_delegatedController != null) {
                $this->_delegatedController->onAfterAction();
            }
            else {
                $output = ob_get_contents();
                if($this->_outputWrapper != null) {
                    $output = $this->_outputWrapper->wrap($output);
                }
                $this->application->responseHandler->addJson(array('xhtml'=>$output));
                ob_end_clean();
                $this->application->responseHandler->returnJsonResponse();
            }
        }
    }  
?>
