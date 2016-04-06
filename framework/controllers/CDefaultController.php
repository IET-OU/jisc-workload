<?php
/**
* CDefaultController class file
*
* Jisc / OU Student Workload Tool.
*
* @license   http://gnu.org/licenses/gpl.html GPL-3.0+
* @author    Jitse van Ameijde <djitsz@yahoo.com>
* @copyright 2015 The Open University.
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CDefaultController implements the controller for default actions such as login
*
*
*/
    class CDefaultController extends CExtendController { //CController {



        /**
        *  constructor - initialises variables
        *
        */
        public function __construct() {
            parent::__construct('admin', 'Default');
        }


        /**
        * actionView - Default view when no controller or action is selected
        *
        */
        public function actionView() {
            // If user is authenticated show the overview screen
            if($this->application->user->isAuthenticated()) {
                if($this->application->user->isSuperAdministrator()) {
                    $courses = CCourseModel::loadByAttributes(array('deleted'=>null),array('order by'=>'`title` asc'))->getObjectArray();
                }
                else {
                    $courses = CCourseModel::loadByAttributes(array('deleted'=>null,'institutionId'=>$this->application->user->institutionId),array('order by'=>'`title` asc'))->getObjectArray();
                }
                $this->attachViewToRegion('main','default','overview',array('courses'=>$courses));
            }
            // If not authenticated show the introduction screen with a login link
            else {
                $this->attachViewToRegion('main','default','intro');
            }
            $this->render();
        }

        /**
        * actionLogin - logs in a user
        *
        */
        public function actionLogin() {
            if(( $this->getParam('resetToken') && $this->getParam('login') ) || $this->post('resetToken')) {
                $fields = array(
                    'login' => array('type'=>'hidden', 'value'=>$this->application->requestHandler->requestVar(CRequestHandler::TYPE_STRING,'login')),
                    'resetToken' => array('type'=>'hidden', 'value'=>$this->application->requestHandler->requestVar(CRequestHandler::TYPE_STRING,'resetToken')),
                    'password' => array('type'=>'password', 'placeholder'=>'New password...'),
                    'repeatPassword' => array('type'=>'password', 'placeholder'=>'Confirm password...'),
                    'submit' => array('type'=>'submit', 'label'=>'Login', 'class'=>'btn-primary'),
                );
                $validators = array(array('login,resetToken,password,repeatPassword','required'));
            }
            else {
                $fields = array(
                    'login' => array('type'=>'textinput', 'placeholder'=>'username or email...'),
                    'password' => array('type'=>'password', 'placeholder'=>'password...'),
                    'submit' => array('type'=>'submit', 'label'=>'login', 'class'=>'btn-primary'),
                    'reset' => array('type'=>'html', 'html'=>'<div style="margin-top:10px;"><a href="/login/?reset=1">I forgot my password</a></div>')
                );
                if($this->getParam('reset')) {
                    unset($fields['password']);
                    $fields['submit']['label'] = 'Reset my password';
                    $fields['cancel'] = array('type'=>'button','class'=>'btn-warning','label'=>'Cancel','url'=>'/login/');
                    $fields['reset'] = array('type'=>'hidden','value'=>1);
                    $validators = array(array('login,reset','required'));
                }
                else $validators =  array(array('login,password','required'));
            }
            if($this->post('reset')) $validators = array(array('login','required'),array('reset','safe'));

            $loginForm = new CForm('login-form','/login/',$fields,$validators,false);

            if($loginForm->wasSubmitted()) {
                if($loginForm->validate()) {
                    if($loginForm->resetToken) {
                        if($loginForm->password != $loginForm->repeatPassword) {
                            $loginForm->setMessage('Password and confirm password fields are not the same');
                        }
                        else if($this->application->resetUserPassword($loginForm->login,$loginForm->resetToken,$loginForm->password)) {
                            $this->application->authenticateUser($loginForm->login,$loginForm->password);
                            $this->application->responseHandler->redirect('/');
                        }
                        else {
                            $loginForm->setMessage('Error resetting password.');
                        }
                    }
                    else if($loginForm->reset == 1) {
                        if($this->application->resetUserPassword($loginForm->login)) {
                            $loginForm->setMessage('An email with a password reset link has been sent.');
                        }
                        else {
                            $loginForm->setMessage('Invalid login or email supplied.');
                        }
                    }
                    else if($this->application->authenticateUser($loginForm->login,$loginForm->password)) {
                        $this->application->responseHandler->redirect('/');
                    }
                    else {
                        $loginForm->setMessage('Unable to login with the provided username and password.');
                    }
                }
                else {
                    $loginForm->setMessage('Please correct the indicated errors in the form.');
                }
            }
            $this->attachViewToRegion('main','default','login',array('loginForm'=>$loginForm));
            $this->render();
//            $this->renderPartial('login', array('loginForm'=>$loginForm));
        }

    }
