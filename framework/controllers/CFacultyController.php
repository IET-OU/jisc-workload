<?php
/**
* CFacultyController class file
* 
* @author Jitse van Ameijde <djitsz@yahoo.com>
* 
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CFacultyController implements the controller for actions involving faculties
* 
* 
*/
    class CFacultyController extends CController {
        
        
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
                    $faculties = CFacultyModel::loadByAttributes(array('deleted'=>null),array('order by'=>'`name` asc'))->getObjectArray();
                }
                else {
                    $faculties = CFacultyModel::loadByAttributes(array('deleted'=>null,'institutionId'=>$this->application->user->institutionId),array('order by'=>'`name` asc'))->getObjectArray();
                }
                $this->attachViewToRegion('main','faculty','view',array('faculties'=>$faculties));
                $this->render();
            }
            // If not authenticated send to the login screen
            else {
                $this->application->responseHandler->redirect('/login/');
            }
        }
        
        /**
        * actionAdd - Adds a new faculty
        * 
        */
        function actionAdd() {
            if(!$this->application->user->isAuthenticated()) { 
                $this->application->responseHandler->redirect('/login/');
                return;
            }            
            $faculty = null;
            if($this->application->user->isSuperAdministrator()) {
                $institutions = CInstitutionModel::loadByAttributes(array(),array('order by'=>'`name` asc'))->getObjectArray();
                $institutionList = array();
                foreach($institutions as $institution) {
                    $institutionList[] = array('value'=>$institution->institutionId,'label'=>$institution->name);
                }
                $fields = array(
                    'institutionId'=>array('type'=>'select','label'=>'Institution','value'=>$this->application->user->institutionId,'options'=>$institutionList,'required'=>true),
                    'name'=>array('type'=>'textinput','label'=>'Faculty name','value'=>'','required'=>true),
                    'submit' => array('type'=>'submit', 'label'=>'Add', 'class'=>'btn-primary')
                );
                $validators = array(array('institutionId,name','required'));                
            }
            else {
                $fields = array(
                    'name'=>array('type'=>'textinput','label'=>'Faculty name','value'=>'','required'=>true),
                    'submit' => array('type'=>'submit', 'label'=>'Add', 'class'=>'btn-primary')
                );
                $validators =  array(array('name','required'));
            }
            $form = new CForm('add-faculty-form','/faculty/add/',$fields,$validators,false);
            
            if($form->wasSubmitted()) {
                if($form->validate()) {
                    $faculty = new CFacultyModel();
                    $faculty->name = $form->name;
                    if($this->application->user->isSuperAdministrator()) {
                        $faculty->institutionId = $form->institutionId;
                    }
                    else {
                        $faculty->institutionId = $this->application->user->institutionId;
                    }
                    $faculty->setDefaultValues();
                    $faculty->insert();
                    
                }
                else {
                    $form->setMessage('Please correct the indicated errors in the form.');
                }
            }
            $this->attachViewToRegion('main','faculty','add',array('form'=>$form,'faculty'=>$faculty));
            $this->render();
        }
        
        /**
        * actionEdit - Edits a faculty
        * 
        */
        function actionEdit() {
            if(!$this->application->user->isAuthenticated()) { 
                $this->application->responseHandler->redirect('/login/');
                return;
            }            
            $facultyId = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'facultyId');
            if($facultyId) {
                $faculty = CFacultyModel::loadByPk($facultyId);
                //Make sure the authenticated user has permission to edit this faculty
                if(!$this->application->user->isSuperAdministrator() && $this->application->user->institutionId != $faculty->institutionId) {
                    return;
                }
                if($this->application->user->isSuperAdministrator()) {
                    $institutions = CInstitutionModel::loadByAttributes(array(),array('order by'=>'`name` asc'))->getObjectArray();
                    $institutionList = array();
                    foreach($institutions as $institution) {
                        $institutionList[] = array('value'=>$institution->institutionId,'label'=>$institution->name);
                    }
                    $fields = array(
                        'facultyId'=>array('type'=>'hidden','value'=>$facultyId),
                        'institutionId'=>array('type'=>'select','label'=>'Institution','value'=>$this->application->user->institutionId,'options'=>$institutionList,'required'=>true),
                        'name'=>array('type'=>'textinput','label'=>'Faculty name','value'=>htmlentities($faculty->name,ENT_COMPAT,'utf-8'),'required'=>true),
                        'submit' => array('type'=>'submit', 'label'=>'Save', 'class'=>'btn-primary')
                    );
                    $validators = array(array('facultyId,institutionId,name','required'));                
                }
                else {
                    $fields = array(
                        'facultyId'=>array('type'=>'hidden','value'=>$facultyId),
                        'name'=>array('type'=>'textinput','label'=>'Faculty name','value'=>htmlentities($faculty->name,ENT_COMPAT,'utf-8'),'required'=>true),
                        'submit' => array('type'=>'submit', 'label'=>'Save', 'class'=>'btn-primary')
                    );
                    $validators =  array(array('facultyId,name','required'));
                }
                $form = new CForm('edit-faculty-form','/faculty/edit/',$fields,$validators,false);
                
                if($form->wasSubmitted()) {
                    if($form->validate()) {
                        $faculty->name = $form->name;
                        if($this->application->user->isSuperAdministrator()) {
                            $faculty->institutionId = $form->institutionId;
                        }
                        else {
                            $faculty->institutionId = $this->application->user->institutionId;
                        }
                        $faculty->setDefaultValues();
                        $faculty->update();                        
                    }
                    else {
                        $form->setMessage('Please correct the indicated errors in the form.');
                    }
                }
                else $faculty = null;
                $this->attachViewToRegion('main','faculty','edit',array('form'=>$form,'faculty'=>$faculty));
                $this->render();
            }
        }
        
        /**
        * actionDelete - Deletes a faculty
        * 
        */
        function actionDelete() {
            if(!$this->application->user->isAuthenticated()) { 
                $this->application->responseHandler->redirect('/login/');
                return;
            }            
            $facultyId = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'facultyId');
            // Only Administrators can delete faculties
            if(!$this->application->user->isAdministrator()) {
                return;
            }
            if($facultyId) {
                $faculty = CFacultyModel::loadByPk($facultyId);
                //Check if the user has the necessary permissions to delete this faculty
                if(!$this->application->user->isSuperAdministrator() && !$this->application->user->institutionId == $faculty->institutionId ) {
                    return;
                }
                $faculty->delete();
                $this->attachViewToRegion('main','faculty','delete',array('faculty'=>$faculty));
                $this->render();
            }
        }
        
    }  
?>
