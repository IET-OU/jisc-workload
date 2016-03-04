<?php
/**
* CInstitutionController class file
*
* @author Jitse van Ameijde <djitsz@yahoo.com>
*
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CInstitutionController implements the controller for actions involving faculties
*
*
*/
    class CInstitutionController extends CExtendController {


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
        public function actionView() {
            // If user is authenticated show the overview screen
            if($this->application->user->isAuthenticated() && $this->application->user->isSuperAdministrator()) {
                $institutions = CInstitutionModel::loadByAttributes(array('deleted'=>null),array('order by'=>'`name` asc'))->getObjectArray();
                $this->attachViewToRegion('main','institution','view',array('institutions'=>$institutions));
                $this->render();
            }
            // If not authenticated send to the login screen
            else {
                $this->application->responseHandler->redirect('/login/');
            }
        }

        /**
        * actionAdd - Adds a new institution
        *
        */
        public function actionAdd() {
            if(!$this->application->user->isAuthenticated()) {
                $this->application->responseHandler->redirect('/login/');
                return;
            }
            $institution = null;
            if($this->application->user->isSuperAdministrator()) {
                $fields = array(
                    'name'=>array('type'=>'textinput','label'=>'Institution name','value'=>'','required'=>true),
                    'submit' => array('type'=>'submit', 'label'=>'Add', 'class'=>'btn-primary')
                );
                $validators = array(array('name','required'));
            }
            else {
                return;
            }
            $form = new CForm('add-institution-form','/institution/add/',$fields,$validators,false);

            if($form->wasSubmitted()) {
                if($form->validate()) {
                    $institution = new CInstitutionModel();
                    $institution->name = $form->name;
                    $db = CDatabaseHandler::getInstance();
                    // Check if an institution with the specified name already exists
                    $rows = $db->select('institutions','UPPER(`name`) AS `name`',array('name'=>strtoupper($form->name)));
                    if(count($rows) > 0) {
                        $form->setMessage('An institution with that name already exists.');
                        $institution = null;
                    }
                    else {
                        $institution->setDefaultValues();
                        $institution->insert();
                    }
                }
                else {
                    $form->setMessage('Please correct the indicated errors in the form.');
                }
            }
            $this->attachViewToRegion('main','institution','add',array('form'=>$form,'institution'=>$institution));
            $this->render();
        }

        /**
        * actionEdit - Edits an institution
        *
        */
        public function actionEdit() {
            if(!$this->application->user->isAuthenticated()) {
                $this->application->responseHandler->redirect('/login/');
                return;
            }
            // Only Super Administrators can edit institutions
            if(!$this->application->user->isSuperAdministrator()) {
                return;
            }
            $institutionId = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'institutionId');
            if($institutionId) {
                $institution = CInstitutionModel::loadByPk($institutionId);
                $fields = array(
                    'institutionId'=>array('type'=>'hidden','value'=>$institutionId,'required'=>true),
                    'name'=>array('type'=>'textinput','label'=>'Faculty name','value'=>htmlentities($institution->name,ENT_COMPAT,'utf-8'),'required'=>true),
                    'submit' => array('type'=>'submit', 'label'=>'Save', 'class'=>'btn-primary')
                );
                $validators = array(array('institutionId,name','required'));
                $form = new CForm('edit-institution-form','/institution/edit/',$fields,$validators,false);

                if($form->wasSubmitted()) {
                    if($form->validate()) {
                        $db = CDatabaseHandler::getInstance();
                        // Check if an institution with the provided name already exists
                        $rows = $db->select('institutions','UPPER(`name`) AS `name`',array('name'=>strtoupper($form->name),'institutionId'=>array('!=',$institutionId)));
                        if(count($rows) > 0) {
                            $form->setMessage('An institution with that name already exists');
                            $institution = null;
                        }
                        else {
                            $institution->name = $form->name;
                            $institution->setDefaultValues();
                            $institution->update();
                        }
                    }
                    else {
                        $form->setMessage('Please correct the indicated errors in the form.');
                    }
                }
                else $institution = null;
                $this->attachViewToRegion('main','institution','edit',array('form'=>$form,'institution'=>$institution));
                $this->render();
            }
        }

        /**
        * actionDelete - Deletes an institution
        *
        */
        public function actionDelete() {
            if(!$this->application->user->isAuthenticated()) {
                $this->application->responseHandler->redirect('/login/');
                return;
            }
            // Only Super Administrators can delete institutions
            if(!$this->application->user->isSuperAdministrator()) {
                return;
            }
            $institutionId = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'institutionId');
            if($institutionId) {
                $institution = CInstitutionModel::loadByPk($institutionId);
                $institution->delete();
                $this->attachViewToRegion('main','institution','delete',array('institution'=>$institution));
                $this->render();
            }
        }

    }
