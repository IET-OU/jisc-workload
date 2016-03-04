<?php
/**
* CUserController class file
*
* @author Jitse van Ameijde <djitsz@yahoo.com>
*
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CUserController implements the controller for actions involving users
*
*
*/
    class CUserController extends CExtendController {


        /**
        *  constructor - initialises variables
        *
        */
        public function __construct() {
            parent::__construct('admin','Default');
        }


        /**
        * actionView - Default view when no controller or action is selected
        *
        */
        function actionView() {
            // If user is authenticated show the overview screen
            if($this->application->user->isAuthenticated()) {
                if($this->application->user->isSuperAdministrator()) {
                    $users = CUserModel::loadByAttributes(array('deleted'=>null),array('order by'=>'`lastName` asc'))->getObjectArray();
                }
                else {
                    $users = CUserModel::loadByAttributes(array('deleted'=>null,'institutionId'=>$this->application->user->institutionId),array('order by'=>'`lastName` asc'))->getObjectArray();
                }
                $this->attachViewToRegion('main','user','view',array('users'=>$users));
                $this->render();
            }
            // If not authenticated send to the login screen
            else {
                $this->application->responseHandler->redirect('/login/');
            }
        }

        /**
        * actionAdd - Adds a new user
        *
        */
        function actionAdd() {
            if(!$this->application->user->isAuthenticated()) {
                $this->application->responseHandler->redirect('/login/');
                return;
            }
            $user = null;
            if($this->application->user->isSuperAdministrator()) {
                $institutions = CInstitutionModel::loadByAttributes(array(),array('order by'=>'`name` asc'))->getObjectArray();
                $institutionList = array();
                foreach($institutions as $institution) {
                    $institutionList[] = array('value'=>$institution->institutionId,'label'=>$institution->name);
                }
                $fields = array(
                    'institutionId'=>array('type'=>'select','label'=>'Institution','value'=>$this->application->user->institutionId,'options'=>$institutionList,'required'=>true),
                    'login'=>array('type'=>'textinput','label'=>'Login','value'=>'','required'=>true),
                    'firstName'=>array('type'=>'textinput','label'=>'First name','value'=>'','required'=>true),
                    'lastName'=>array('type'=>'textinput','label'=>'Last name','value'=>'','required'=>true),
                    'email'=>array('type'=>'textinput','label'=>'Email','value'=>'','required'=>true),
                    'password'=>array('type'=>'password','label'=>'Password','value'=>'','required'=>true),
                    'passwordRepeat'=>array('type'=>'password','label'=>'Repeat password','value'=>'','required'=>true),
                    'accessLevel'=>array('type'=>'select','label'=>'Access level','value'=>2,'options'=>array(array('value'=>0,'label'=>'Super administrator'),array('value'=>1,'label'=>'Administrator'),array('value'=>2,'label'=>'User')),'required'=>true),
                    'submit' => array('type'=>'submit', 'label'=>'Add', 'class'=>'btn-primary')
                );
                $validators = array(array('institutionId,login,firstName,lastName,email,password,passwordRepeat,accessLevel','required'));
            }
            else if($this->application->user->isAdministrator()){
                $fields = array(
                    'login'=>array('type'=>'textinput','label'=>'Login','value'=>'','required'=>true),
                    'firstName'=>array('type'=>'textinput','label'=>'First name','value'=>'','required'=>true),
                    'lastName'=>array('type'=>'textinput','label'=>'Last name','value'=>'','required'=>true),
                    'email'=>array('type'=>'textinput','label'=>'Email','value'=>'','required'=>true),
                    'password'=>array('type'=>'password','label'=>'Password','value'=>'','required'=>true),
                    'passwordRepeat'=>array('type'=>'password','label'=>'Repeat password','value'=>'','required'=>true),
                    'accessLevel'=>array('type'=>'select','label'=>'Access level','value'=>2,'options'=>array(array('value'=>1,'label'=>'Administrator'),array('value'=>2,'label'=>'User')),'required'=>true),
                    'submit' => array('type'=>'submit', 'label'=>'Add', 'class'=>'btn-primary')
                );
                $validators = array(array('login,firstName,lastName,email,password,passwordRepeat,accessLevel','required'));
            }
            else {
                $fields = array(
                    'login'=>array('type'=>'textinput','label'=>'Login','value'=>'','required'=>true),
                    'firstName'=>array('type'=>'textinput','label'=>'First name','value'=>'','required'=>true),
                    'lastName'=>array('type'=>'textinput','label'=>'Last name','value'=>'','required'=>true),
                    'email'=>array('type'=>'textinput','label'=>'Email','value'=>'','required'=>true),
                    'password'=>array('type'=>'password','label'=>'Password','value'=>'','required'=>true),
                    'passwordRepeat'=>array('type'=>'password','label'=>'Repeat password','value'=>'','required'=>true),
                    'submit' => array('type'=>'submit', 'label'=>'Add', 'class'=>'btn-primary')
                );
                $validators = array(array('login,firstName,lastName,email,password,passwordRepeat','required'));
            }
            $form = new CForm('add-user-form','/user/add/',$fields,$validators,false);

            $error = false;
            if($form->wasSubmitted()) {
                if($form->validate()) {
                    $user = new CUserModel();
                    $user->login = $form->login;
                    $user->firstName = $form->firstName;
                    $user->lastName = $form->lastName;
                    $user->email = $form->email;
                    if(!is_null($form->accessLevel)) $user->accessLevel = $form->accessLevel;

                    // Make sure that the passwords match
                    if($form->password == $form->passwordRepeat) {
                        $user->password = sha1($form->password);
                    }
                    else {
                        $form->setMessage('Password and repeat password don\'t match.');
                        $error = true;
                    }
                    // Make sure the newly created user isn't granted an access level higher than the user creating them
                    if($user->accessLevel < $this->application->user->accessLevel) $user->accessLevel = $this->application->user->accessLevel;
                    $db = CDatabaseHandler::getInstance();
                    $rows = $db->select('users','UPPER(`login`) as `login`',array('login'=>strtoupper($user->login)));
                    if(count($rows) > 0) {
                        $form->setMessage('A user with the specified login already exists.');
                        $error = true;
                    }
                    $rows = $db->select('users','UPPER(`email`) as `email`',array('email'=>strtoupper($user->email)));
                    if(count($rows) > 0) {
                        $form->setMessage('A user with the specified email already exists.');
                        $error = true;
                    }
                    if(!$this->application->user->isSuperAdministrator()) {
                        $user->institutionId = $this->application->user->institutionId;
                    }
                    else {
                        $user->institutionId = $form->institutionId;
                    }
                    if($error == false) {
                        $user->setDefaultValues();
                        $user->insert();
                    }
                    else {
                        $user = null;
                    }
                }
                else {
                    $form->setMessage('Please correct the indicated errors in the form.');
                }
            }
            $this->attachViewToRegion('main','user','add',array('form'=>$form,'user'=>$user));
            $this->render();
        }

        /**
        * actionEdit - Edits a user
        *
        */
        function actionEdit() {
            if(!$this->application->user->isAuthenticated()) {
                $this->application->responseHandler->redirect('/login/');
                return;
            }
            $userId = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'userId');
            if($userId) {
                $user = CUserModel::loadByPk($userId);
                //Make sure the authenticated user has permission to edit this user
                $canEdit = false;
                if($this->application->user->isSuperAdministrator()) $canEdit = true;
                else if($this->application->user->isAdministrator() && $this->application->user->institutionId == $user->institutionId) $canEdit = true;
                else if($this->application->user->userId == $user->userId) $canEdit = true;
                if($canEdit == false) return;

                if($this->application->user->isSuperAdministrator()) {
                    $institutions = CInstitutionModel::loadByAttributes(array(),array('order by'=>'`name` asc'))->getObjectArray();
                    $institutionList = array();
                    foreach($institutions as $institution) {
                        $institutionList[] = array('value'=>$institution->institutionId,'label'=>$institution->name);
                    }
                    $fields = array(
                        'userId'=>array('type'=>'hidden','value'=>$user->userId),
                        'institutionId'=>array('type'=>'select','label'=>'Institution','value'=>$user->institutionId,'options'=>$institutionList,'required'=>true),
                        'login'=>array('type'=>'textinput','label'=>'Login','value'=>$user->login,'required'=>true),
                        'firstName'=>array('type'=>'textinput','label'=>'First name','value'=>$user->firstName,'required'=>true),
                        'lastName'=>array('type'=>'textinput','label'=>'Last name','value'=>$user->lastName,'required'=>true),
                        'email'=>array('type'=>'textinput','label'=>'Email','value'=>$user->email,'required'=>true),
                        'password'=>array('type'=>'password','label'=>'Password','value'=>'','required'=>false),
                        'passwordRepeat'=>array('type'=>'password','label'=>'Repeat password','value'=>'','required'=>false),
                        'accessLevel'=>array('type'=>'select','label'=>'Access level','value'=>$user->accessLevel,'options'=>array(array('value'=>0,'label'=>'Super administrator'),array('value'=>1,'label'=>'Administrator'),array('value'=>2,'label'=>'User')),'required'=>true),
                        'submit' => array('type'=>'submit', 'label'=>'Save', 'class'=>'btn-primary')
                    );
                    $validators = array(array('userId,institutionId,login,firstName,lastName,email,accessLevel','required'),array('password,passwordRepeat','safe'));
                }
                else if($this->application->user->isAdministrator()){
                    $fields = array(
                        'userId'=>array('type'=>'hidden','value'=>$user->userId),
                        'login'=>array('type'=>'textinput','label'=>'Login','value'=>$user->login,'required'=>true),
                        'firstName'=>array('type'=>'textinput','label'=>'First name','value'=>$user->firstName,'required'=>true),
                        'lastName'=>array('type'=>'textinput','label'=>'Last name','value'=>$user->lastName,'required'=>true),
                        'email'=>array('type'=>'textinput','label'=>'Email','value'=>$user->email,'required'=>true),
                        'password'=>array('type'=>'password','label'=>'Password','value'=>'','required'=>false),
                        'passwordRepeat'=>array('type'=>'password','label'=>'Repeat password','value'=>'','required'=>false),
                        'accessLevel'=>array('type'=>'select','label'=>'Access level','value'=>2,'options'=>array(array('value'=>1,'label'=>'Administrator'),array('value'=>2,'label'=>'User')),'required'=>true),
                        'submit' => array('type'=>'submit', 'label'=>'Save', 'class'=>'btn-primary')
                    );
                    $validators = array(array('userId,login,firstName,lastName,email,accessLevel','required'),array('password,passwordRepeat','safe'));
                }
                else {
                    $fields = array(
                        'userId'=>array('type'=>'hidden','value'=>$user->userId),
                        'login'=>array('type'=>'textinput','label'=>'Login','value'=>$user->login,'required'=>true),
                        'firstName'=>array('type'=>'textinput','label'=>'First name','value'=>$user->firstName,'required'=>true),
                        'lastName'=>array('type'=>'textinput','label'=>'Last name','value'=>$user->lastName,'required'=>true),
                        'email'=>array('type'=>'textinput','label'=>'Email','value'=>$user->email,'required'=>true),
                        'password'=>array('type'=>'password','label'=>'Password','value'=>'','required'=>false),
                        'passwordRepeat'=>array('type'=>'password','label'=>'Repeat password','value'=>'','required'=>false),
                        'submit' => array('type'=>'submit', 'label'=>'Save', 'class'=>'btn-primary')
                    );
                    $validators = array(array('login,firstName,lastName,email','required'),array('password,passwordRepeat','safe'));
                }

                $form = new CForm('edit-user-form','/user/edit/',$fields,$validators,false);
                $error = false;
                if($form->wasSubmitted()) {
                    if($form->validate()) {
                        $user->login = $form->login;
                        $user->firstName = $form->firstName;
                        $user->lastName = $form->lastName;
                        $user->email = $form->email;
                        if(!is_null($form->accessLevel)) $user->accessLevel = $form->accessLevel;

                        // Make sure that the passwords match
                        if($form->password == $form->passwordRepeat) {
                            if($form->password != '') $user->password = sha1($form->password);
                        }
                        else {
                            $form->setMessage('Password and repeat password don\'t match.');
                            $error = true;
                        }
                        // Make sure the newly created user isn't granted an access level higher than the user creating them
                        if($user->accessLevel < $this->application->user->accessLevel) $user->accessLevel = $this->application->user->accessLevel;
                        $db = CDatabaseHandler::getInstance();
                        $rows = $db->select('users','UPPER(`login`) as `login`',array('login'=>strtoupper($user->login),'userId'=>array('!=',$user->userId)));
                        if(count($rows) > 0) {
                            $form->setMessage('A user with the specified login already exists.');
                            $error = true;
                        }
                        $rows = $db->select('users','UPPER(`email`) as `email`',array('email'=>strtoupper($user->email),'userId'=>array('!=',$user->userId)));
                        if(count($rows) > 0) {
                            $form->setMessage('A user with the specified email already exists.');
                            $error = true;
                        }
                        if(!$this->application->user->isSuperAdministrator()) {
                            $user->institutionId = $this->application->user->institutionId;
                        }
                        else {
                            $user->institutionId = $form->institutionId;
                        }
                        if($error == false) {
                            $user->setDefaultValues();
                            $user->update();
                        }
                        else {
                            $user = null;
                        }
                    }
                    else {
                        $form->setMessage('Please correct the indicated errors in the form.');
                    }
                }
                else {
                    $user = null;
                }
                $this->attachViewToRegion('main','user','edit',array('form'=>$form,'user'=>$user));
                $this->render();
            }
        }

        /**
        * actionDelete - Deletes a user
        *
        */
        function actionDelete() {
            if(!$this->application->user->isAuthenticated()) {
                $this->application->responseHandler->redirect('/login/');
                return;
            }
            $userId = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'userId');
            // Only Administrators can delete users
            if(!$this->application->user->isAdministrator()) {
                return;
            }
            if($userId) {
                $user = CUserModel::loadByPk($userId);
                //Check if the user has the necessary permissions to delete this user
                if(!$this->application->user->isSuperAdministrator() && !$this->application->user->institutionId == $user->institutionId ) {
                    return;
                }
                if($this->application->user->userId == $user->userId) {
                    return;
                }
                $user->delete();
                $this->attachViewToRegion('main','user','delete',array('user'=>$user));
                $this->render();
            }
        }

    }
