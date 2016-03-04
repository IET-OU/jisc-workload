<?php
/**
* CCourseController class file
*
* Jisc / OU Student Workload Tool.
*
* @license   http://gnu.org/licenses/gpl.html GPL-3.0+
* @author    Jitse van Ameijde <djitsz@yahoo.com>
* @copyright 2015 The Open University.
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CCourseController implements the controller for actions involving users
*
*
*/
    class CCourseController extends CExtendController {


        /**
        *  constructor - initialises variables
        *
        */
        public function __construct() {
            parent::__construct('admin','Default');

            $this->attachViewToRegion('googleAnalytics', 'default', 'googleAnalytics',
                array('analyticsId' => $this->application->config[ 'googleAnalyticsId' ]));
        }


        /**
        * actionView - Default view when no controller or action is selected
        *
        */
        function actionView() {
            // If user is authenticated show the overview screen
            if($this->application->user->isAuthenticated()) {
                if($this->application->user->isSuperAdministrator()) {
                    $courses = CCourseModel::loadByAttributes(array('deleted'=>null),array('order by'=>'`created` desc'))->getObjectArray();
                }
                else {
                    $courses = CCourseModel::loadByAttributes(array('deleted'=>null,'institutionId'=>$this->application->user->institutionId),array('order by'=>'`created` desc'))->getObjectArray();
                }
                $this->attachViewToRegion('main','course','view',array('courses'=>$courses));
                $this->render();
            }
            // If not authenticated send to the login screen
            else {
                $this->application->responseHandler->redirect('/login/');
            }
        }

        /**
        * actionAdd - Adds a new course
        *
        */
        function actionAdd() {
            if(!$this->application->user->isAuthenticated()) {
                $this->application->responseHandler->redirect('/login/');
                return;
            }
            $course = null;
            if($this->application->user->isSuperAdministrator()) {
                $faculties = CFacultyModel::loadByAttributes(array(),array('order by'=>'`institutionId` asc, `name` asc'))->getObjectArray();
                $facultyList = array();
                foreach($faculties as $faculty) {
                    $facultyList[] = array('value'=>$faculty->facultyId,'label'=>$faculty->institution->name . ' - ' . $faculty->name);
                }
            }
            else {
                $faculties = CFacultyModel::loadByAttributes(array(),array('order by'=>'`name` asc'))->getObjectArray();
                $facultyList = array();
                foreach($faculties as $faculty) {
                    $facultyList[] = array('value'=>$faculty->facultyId,'label'=>$faculty->name);
                }
            }
            $fields = array(
                'facultyId'=>array('type'=>'select','label'=>'Faculty','value'=>'','options'=>$facultyList,'required'=>true),
                'code'=>array('type'=>'textinput','label'=>'Course code','value'=>'','required'=>true),
                'title'=>array('type'=>'textinput','label'=>'Course title','value'=>'','required'=>true),
                'presentation'=>array('type'=>'textinput','label'=>'Course presentation','value'=>'','required'=>true),
                'status'=>array('type'=>'select','label'=>'Status','value'=>1,'required'=>true,'options'=>array(array('label'=>'Draft','value'=>0),array('label'=>'In presentation','value'=>1),array('label'=>'Retired','value'=>2))),
                'defaultWpm'=>array('type'=>'select','label'=>'Default study speed','value'=>1,'required'=>true,'options'=>array(array('label'=>'Low','value'=>0),array('label'=>'Medium','value'=>1),array('label'=>'High','value'=>2))),
                'wpmLow'=>array('type'=>'textinput','label'=>'Words per minute (Low)','value'=>'35','required'=>true),
                'wpmMed'=>array('type'=>'textinput','label'=>'Words per minute (Medium)','value'=>'70','required'=>true),
                'wpmHi'=>array('type'=>'textinput','label'=>'Words per minute (High)','value'=>'120','required'=>true),
                'collaborators'=>array('type'=>'hidden','value'=>'','id'=>'collaborators'),
                'html'=>array('type'=>'html','html'=>'<div class="collaborators clearfix clearboth"><span class="bold">Collaborators:</span> <input id="collaborator-input" name="tmp" class="autocomplete" data-source="users" data-target="#collaborators" value=""><br /></div>'),
                'submit' => array('type'=>'submit', 'label'=>'Add', 'class'=>'btn-primary')
            );
            $validators = array(array('facultyId,code,title,presentation,status,defaultWpm,wpmLow,wpmMed,wpmHi','required'),array('collaborators','safe'),array('wpmLow,wpmMed,wpmHi','type','int'));
            $form = new CForm('add-course-form','/course/add/',$fields,$validators,false);

            $team = CUserModel::loadByAttributes(array('institutionId'=>$this->application->user->institutionId,'deleted'=>null))->getObjectArray();
            $users = array('users'=>array());
            foreach($team as $member) {
                $users['users'][] = array('text'=>$member->firstName . ' ' . $member->lastName,'value'=>$member->userId);
            }
            $this->application->includeJavaScriptSnippet('$(document).ready(function(){$.extend(globalVars,' . json_encode($users) . ');});');

            $error = false;
            if($form->wasSubmitted()) {
                if($form->validate()) {
                    $course = new CCourseModel();
                    $course->facultyId = $form->facultyId;
                    $course->code = $form->code;
                    $course->title = $form->title;
                    $course->presentation = $form->presentation;
                    $course->status = $form->status;
                    $course->defaultWpm = $form->defaultWpm;
                    $course->wpmLow = $form->wpmLow;
                    $course->wpmMed = $form->wpmMed;
                    $course->wpmHi = $form->wpmHi;
                    $course->setDefaultValues();
                    $course->insert();
                    if($form->collaborators != '') {
                        $ids = explode(',',$form->collaborators);
                        foreach($ids as $id) {
                            $this->application->db->insert('collaborators',array('userId'=>$id,'courseId'=>$course->courseId));
                        }
                    }
                    $this->application->responseHandler->redirect('/');
                    return;
                }
                else {
                    $form->setMessage('Please correct the indicated errors in the form.');
                }
            }
            $this->attachViewToRegion('main','course','add',array('form'=>$form,'course'=>$course));
            $this->render();
        }

        /**
        * actionEdit - Edits a course
        *
        */
        function actionEdit() {
            if(!$this->application->user->isAuthenticated()) {
                $this->application->responseHandler->redirect('/login/');
                return;
            }
            $courseId = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'courseId');
            if($courseId) {
                $course = CCourseModel::loadByPk($courseId);
                //Make sure the authenticated user has permission to edit this course
                if(!$this->application->user->isSuperAdministrator() && $this->application->user->institutionId != $course->institutionId) {
                    return;
                }
                if($this->application->user->isSuperAdministrator()) {
                    $faculties = CFacultyModel::loadByAttributes(array(),array('order by'=>'`institutionId` asc, `name` asc'))->getObjectArray();
                    $facultyList = array();
                    foreach($faculties as $faculty) {
                        $facultyList[] = array('value'=>$faculty->facultyId,'label'=>$faculty->institution->name . ' - ' . $faculty->name);
                    }
                }
                else {
                    $faculties = CFacultyModel::loadByAttributes(array(),array('order by'=>'`name` asc'))->getObjectArray();
                    $facultyList = array();
                    foreach($faculties as $faculty) {
                        $facultyList[] = array('value'=>$faculty->facultyId,'label'=>$faculty->name);
                    }
                }
                $collaborators = $this->application->db->select('collaborators','*',array('courseId'=>$courseId));
                $collaboratorsHtml = '';
                $collaboratorArray = array();
                foreach($collaborators as $collaborator) {
                    $user = CUserModel::loadByPk($collaborator['userId']);
                    $collaboratorsHtml .= '<div class="collaborator" id="collaborator-' . $user->userId . '"><span class="name">' . htmlentities($user->firstName . ' ' . $user->lastName,ENT_COMPAT,'utf-8') . '</span> <a href="#" class="remove-collaborator" data-value="' . $user->userId . '">x</a></div>';
                    $collaboratorArray[] = $user->userId;
                }
                $fields = array(
                    'courseId'=>array('type'=>'hidden','value'=>$course->courseId),
                    'facultyId'=>array('type'=>'select','label'=>'Faculty','value'=>$course->facultyId,'options'=>$facultyList,'required'=>true),
                    'code'=>array('type'=>'textinput','label'=>'Course code','value'=>$course->code,'required'=>true),
                    'title'=>array('type'=>'textinput','label'=>'Course title','value'=>$course->title,'required'=>true),
                    'presentation'=>array('type'=>'textinput','label'=>'Course presentation','value'=>$course->presentation,'required'=>true),
                    'status'=>array('type'=>'select','label'=>'Status','value'=>$course->status,'required'=>true,'options'=>array(array('label'=>'Draft','value'=>0),array('label'=>'In presentation','value'=>1),array('label'=>'Retired','value'=>2))),
                    'defaultWpm'=>array('type'=>'select','label'=>'Default study speed','value'=>$course->defaultWpm,'required'=>true,'options'=>array(array('label'=>'Low','value'=>0),array('label'=>'Medium','value'=>1),array('label'=>'High','value'=>2))),
                    'wpmLow'=>array('type'=>'textinput','label'=>'Words per minute (Low)','value'=>$course->wpmLow,'required'=>true),
                    'wpmMed'=>array('type'=>'textinput','label'=>'Words per minute (Medium)','value'=>$course->wpmMed,'required'=>true),
                    'wpmHi'=>array('type'=>'textinput','label'=>'Words per minute (High)','value'=>$course->wpmHi,'required'=>true),
                    'collaborators'=>array('type'=>'hidden','value'=>implode(',',$collaboratorArray),'id'=>'collaborators'),
                    'html'=>array('type'=>'html','html'=>'<div class="collaborators clearfix clearboth"><span class="bold">Collaborators:</span> <input id="collaborator-input" name="tmp" class="autocomplete" data-source="users" data-target="#collaborators" value=""><br />' . $collaboratorsHtml . '</div>'),
                    'submit' => array('type'=>'submit', 'label'=>'Save', 'class'=>'btn-primary')
                );
                $validators = array(array('facultyId,code,title,presentation,status,defaultWpm,wpmLow,wpmMed,wpmHi','required'),array('collaborators','safe'),array('wpmLow,wpmMed,wpmHi','type','int'));
                $form = new CForm('edit-course-form','/course/edit/',$fields,$validators,false);

                $team = CUserModel::loadByAttributes(array('institutionId'=>$this->application->user->institutionId,'deleted'=>null))->getObjectArray();
                $users = array('users'=>array());
                foreach($team as $member) {
                    $users['users'][] = array('text'=>$member->firstName . ' ' . $member->lastName,'value'=>$member->userId);
                }
                $this->application->includeJavaScriptSnippet('$(document).ready(function(){$.extend(globalVars,' . json_encode($users) . ');});');

                $error = false;
                if($form->wasSubmitted()) {
                    if($form->validate()) {
                        $course->facultyId = $form->facultyId;
                        $course->code = $form->code;
                        $course->title = $form->title;
                        $course->presentation = $form->presentation;
                        $course->status = $form->status;
                        $course->defaultWpm = $form->defaultWpm;
                        $course->wpmLow = $form->wpmLow;
                        $course->wpmMed = $form->wpmMed;
                        $course->wpmHi = $form->wpmHi;
                        $course->setDefaultValues();
                        $course->update();
                        $this->application->db->delete('collaborators',array('courseId'=>$course->courseId));
                        if($form->collaborators != '') {
                            $ids = explode(',',$form->collaborators);
                            foreach($ids as $id) {
                                $this->application->db->insert('collaborators',array('userId'=>$id,'courseId'=>$course->courseId));
                            }
                        }
                        $this->application->responseHandler->redirect('/');
                        return;
                    }
                    else {
                        $form->setMessage('Please correct the indicated errors in the form.');
                    }
                }
                else {
                    $course = null;
                }
                $this->attachViewToRegion('main','course','edit',array('form'=>$form,'course'=>$course));
                $this->render();
            }
        }

        /**
        * actionDelete - Deletes a course
        *
        */
        function actionDelete() {
            if(!$this->application->user->isAuthenticated()) {
                $this->application->responseHandler->redirect('/login/');
                return;
            }
            $courseId = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'courseId');
            if($courseId) {
                $course = CCourseModel::loadByPk($courseId);
                // Only Administrators or creators can delete courses
                if(!$this->application->user->isAdministrator() && $course->owner->userId != $this->application->user->userId) {
                    return;
                }
                //Check if the user has the necessary permissions to delete this course
                if(!$this->application->user->isSuperAdministrator() && !$this->application->user->institutionId == $course->institutionId ) {
                    return;
                }
                $course->delete();
                $this->attachViewToRegion('main','course','delete',array('course'=>$course));
                $this->render();
            }
        }

    }
