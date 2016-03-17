<?php
/**
* CWorkloadController class file
*
* @author Jitse van Ameijde <djitsz@yahoo.com>
*
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CWorkloadController implements the controller for actions involving users
*
*
*/
    class CWorkloadController extends CExtendController {


        /**
        *  constructor - initialises variables
        *
        */
        public function __construct() {
            parent::__construct('admin','Default');
        }

        public static function printWorkloadRow($item,$row,$course) {
            $html = '';
            if($item == null) {
                $id = $title = $wordcount = $av = $other = $FHI = $communication = $productive = $experiential = $interactive = $assessment = $tuition = '';
                $unit = '';
                $wpm = $course->defaultWpm;
                $html .= '<tr class="workload-row"><td class="drag-handle"><input type="hidden" class="item-id" name="temp-' . $row . '-item-id" value="' . $id . '" /><input type="hidden" class="unit" name="temp-' . $row . '-unit" value="' . $unit . '" />#</td>';
                $html .= '<td class="item"><input class="title" name="temp-' . $row . '-title" value="' . $title . '"/></td>';
                $html .= '<td class="assimilative"><input class="wordcount" name="temp-' . $row . '-wordcount" value="' . $wordcount . '"/><select class="wpm" name="temp-' . $row . '-wpm" value="' . $wpm . '"><option value="0"' . ($wpm == 0 ? ' selected="selected"': '') . '>Low</option><option value="1"' . ($wpm == 1 ? ' selected="selected"': '') . '>Med</option><option value="2"' . ($wpm == 2 ? ' selected="selected"': '') . '>Hi</option></select></td>';
                $html .= '<td class="assimilative"><input class="av minutes" name="temp-' . $row . '-av" value="' . $av . '"/></td>';
                $html .= '<td class="assimilative"><input class="other minutes" name="temp-' . $row . '-other" value="' . $other . '"/></td>';
                $html .= '<td class="FHI"><input class="FHI minutes" name="temp-' . $row . '-FHI" value="' . $FHI . '"/></td>';
                $html .= '<td class="communication"><input class="communication minutes" name="temp-' . $row . '-communication" value="' . $communication . '"/></td>';
                $html .= '<td class="productive"><input class="productive minutes" name="temp-' . $row . '-productive" value="' . $productive . '"/></td>';
                $html .= '<td class="experiential"><input class="experiential minutes" name="temp-' . $row . '-experiential" value="' . $experiential . '"/></td>';
                $html .= '<td class="interactive"><input class="interactive minutes" name="temp-' . $row . '-interactive" value="' . $interactive . '"/></td>';
                $html .= '<td class="assessment"><input class="assessment minutes" name="temp-' . $row . '-assessment" value="' . $assessment . '"/></td>';
                $html .= '<td class="tuition"><input class="tuition minutes" name="temp-' . $row . '-tuition" value="' . $tuition . '"/></td>';
                $html .= '<td class="total"></td>';
                $html .= '<td class="actions"><a class="remove-row" title="Remove row" href="#"><span class="glyphicon glyphicon-remove"></span></a><a class="insert-row" title="Insert row below" href="#"><span class="glyphicon glyphicon-plus"></span></a></td>';
            }
            else {
                $id = $item->itemId;
                $unit = $item->unit;
                $title = htmlentities($item->title,ENT_COMPAT,'utf-8');
                $wordcount = $item->wordcount;
                $wpm = $item->wpm;
                $av = $item->av;
                $other = $item->other;
                $FHI = $item->FHI;
                $communication = $item->communication;
                $productive = $item->productive;
                $experiential = $item->experiential;
                $interactive = $item->interactive;
                $assessment = $item->assessment;
                $tuition = $item->tuition;
                $html .= '<tr class="workload-row"><td class="drag-handle"><input class="item-id" type="hidden" name="row-' . $row . '-item-id" value="' . $id . '" /><input class="unit" type="hidden" name="row-' . $row . '-unit" value="' . $unit . '" />#</td>';
                $html .= '<td class="item"><input class="title" name="row-' . $row . '-title" value="' . $title . '"/></td>';
                $html .= '<td class="assimilative"><input class="wordcount" name="row-' . $row . '-wordcount" value="' . $wordcount . '"/><select class="wpm" name="row-' . $row . '-wpm" value="' . $wpm . '"><option value="0"' . ($wpm == 0 ? ' selected="selected"': '') . '>Low</option><option value="1"' . ($wpm == 1 ? ' selected="selected"': '') . '>Med</option><option value="2"' . ($wpm == 2 ? ' selected="selected"': '') . '>Hi</option></select></td>';
                $html .= '<td class="assimilative"><input class="av minutes" name="row-' . $row . '-av" value="' . $av . '"/></td>';
                $html .= '<td class="assimilative"><input class="other minutes" name="row-' . $row . '-other" value="' . $other . '"/></td>';
                $html .= '<td class="FHI"><input class="FHI minutes" name="row-' . $row . '-FHI" value="' . $FHI . '"/></td>';
                $html .= '<td class="communication"><input class="communication minutes" name="row-' . $row . '-communication" value="' . $communication . '"/></td>';
                $html .= '<td class="productive"><input class="productive minutes" name="row-' . $row . '-productive" value="' . $productive . '"/></td>';
                $html .= '<td class="experiential"><input class="experiential minutes" name="row-' . $row . '-experiential" value="' . $experiential . '"/></td>';
                $html .= '<td class="interactive"><input class="interactive minutes" name="row-' . $row . '-interactive" value="' . $interactive . '"/></td>';
                $html .= '<td class="assessment"><input class="assessment minutes" name="row-' . $row . '-assessment" value="' . $assessment . '"/></td>';
                $html .= '<td class="tuition"><input class="tuition minutes" name="row-' . $row . '-tuition" value="' . $tuition . '"/></td>';
                $html .= '<td class="total"></td>';
                $html .= '<td class="actions"><a class="remove-row" title="Remove row" href="#"><span class="glyphicon glyphicon-remove"></span></a><a class="insert-row" title="Insert row below" href="#"><span class="glyphicon glyphicon-plus"></span></a></td>';
            }
            return $html;
        }

        public static function printWorkloadSummaryRow($unit) {
            $html = '';
            $html .= '<tr class="summary"><th class="unit-title item" colspan="2">Unit ' . $unit . '</th>';
            $html .= '<th class="wordcount assimilative"></th>';
            $html .= '<th class="av assimilative"></th>';
            $html .= '<th class="other assimilative"></th>';
            $html .= '<th class="FHI"></th>';
            $html .= '<th class="communication"></th>';
            $html .= '<th class="productive"></th>';
            $html .= '<th class="experiential"></th>';
            $html .= '<th class="interactive"></th>';
            $html .= '<th class="assessment"></th>';
            $html .= '<th class="tuition"></th>';
            $html .= '<th class="total"></th>';
            $html .= '<th class="actions"></th>';
            return $html;
        }


        /**
        * actionView - Default view when no controller or action is selected
        *
        */
        function actionView() {
            // If user is authenticated show the workload screen
            if($this->application->user->isAuthenticated()) {
                $courseId = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'courseId');
                if($courseId) {
                    $course = CCourseModel::loadByPk($courseId);
                    $canEdit = false;
                    if($course->createdBy == $this->application->user->userId) $canEdit = true;
                    else if($this->application->user->isSuperAdministrator()) $canEdit = true;
                    else if($this->application->user->isAdministrator() && $course->faculty->institutionId == $this->application->user->institutionId) $canEdit = true;
                    else {
                        $collaborators = $this->application->db->select('collaborators','*',array('courseId'=>$courseId,'userId'=>$userId))->getObjectArray();
                        if(count($collaborators) > 0) $canEdit = true;
                    }
                    $items = CItemModel::loadByAttributes(array('courseId'=>$courseId),array('order by'=>'`order` asc'))->getObjectArray();
                    $this->attachViewToRegion('main','workload','view',array('course'=>$course,'items'=>$items));
                    $this->render();
                }
            }
            // If not authenticated send to the login screen
            else {
                $this->application->responseHandler->redirect('/login/');
            }
        }

        /**
        * actionSave - Save the workload table
        *
        */
        function actionSave() {
            if($this->application->user->isAuthenticated()) {
                //This is a workaround for the fact that PHP as standard accepts no more than 1000 items as part of a GET / POST request.
                //By encoding all items into a single data item and then decoding this back into the POST array we solve this problem
                if($this->post('data')) {
                    $items = explode('&', $this->post('data'));
                    unset($_POST['data']);
                    $i = 0;
                    foreach($items as $item) {
                        $pair = explode('=',$item);
                        $_POST[$pair[0]] = urldecode($pair[1]);
                        $i++;
                    }
                }
                $courseId = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'course-id');
                if($courseId) {
                    $course = CCourseModel::loadByPk($courseId);
                    $canEdit = false;
                    if($course->createdBy == $this->application->user->userId) $canEdit = true;
                    else if($this->application->user->isSuperAdministrator()) $canEdit = true;
                    else if($this->application->user->isAdministrator() && $course->faculty->institutionId == $this->application->user->institutionId) $canEdit = true;
                    else {
                        $collaborators = $this->application->db->select('collaborators','*',array('courseId'=>$courseId,'userId'=>$userId))->getObjectArray();
                        if(count($collaborators) > 0) $canEdit = true;
                    }
                    if($canEdit == true) {
                        $numRows = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'num-rows');
                        for($i = 1; $i <= $numRows; $i++) {
                            $itemId = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'row-' . $i . '-item-id');
                            $item = new CItemModel();
                            $item->courseId = $courseId;
                            $item->order = $i;
                            $item->unit = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'row-' . $i . '-unit');
                            $item->title = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_STRING,'row-' . $i . '-title');
                            $item->wordcount = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'row-' . $i . '-wordcount');
                            $item->wpm = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'row-' . $i . '-wpm');
                            $item->av = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'row-' . $i . '-av');
                            $item->other = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'row-' . $i . '-other');
                            $item->FHI = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'row-' . $i . '-FHI');
                            $item->communication = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'row-' . $i . '-communication');
                            $item->productive = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'row-' . $i . '-productive');
                            $item->experiential = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'row-' . $i . '-experiential');
                            $item->interactive = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'row-' . $i . '-interactive');
                            $item->assessment = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'row-' . $i . '-assessment');
                            $item->tuition = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'row-' . $i . '-tuition');
                            if($itemId) {
                                $item->itemId = $itemId;
                                $item->setDefaultValues();
                                $item->update();
                            }
                            else {
                                $item->setDefaultValues();
                                $item->insert();
                            }
                        }
                        $deletedItems = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_STRING,'deleted-items');
                        if($deletedItems != '') {
                            $items = explode(',',$deletedItems);
                            $this->application->db->delete('items',array('itemId'=>array('in',$items)));
                        }

                        $this->application->responseHandler->addJson(array('script'=>array('location.reload(true);'),'result'=>true));
                        $this->application->responseHandler->returnJsonResponse();
                        return;

                    }
                    else {
                        $this->application->responseHandler->addJson(array('script'=>array('alert("You do not have permissions to make changes to this module.");'),'result'=>false));
                        $this->application->responseHandler->returnJsonResponse();
                        return;
                    }
                }
            }
            // If not authenticated display an error message
            else {
                $this->application->responseHandler->addJson(array('script'=>array('alert("You do not have permissions to make changes to this module.");'),'result'=>false));
                $this->application->responseHandler->returnJsonResponse();
                return;
            }
        }

        /**
        * actionChart - Shows a workload chart for a particular course
        *
        */
        function actionChart() {
            if($this->application->user->isAuthenticated()) {
                $courseId = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'courseId');
                if($courseId) {
                    $course = CCourseModel::loadByPk($courseId);
                    $items = CItemModel::loadByAttributes(array('courseId'=>$courseId),array('order by'=>'`order` asc'))->getObjectArray();

                    $totals = array();
                    $speeds = array($course->wpmLow,$course->wpmMed,$course->wpmHi);
                    foreach($items as $item) {
                        if(!isset($totals[$item->unit])) $totals[$item->unit] = array('assimilative'=>0,'FHI'=>0,'communication'=>0,'productive'=>0,'experiential'=>0,'interactive'=>0,'assessment'=>0,'tuition'=>0,'total'=>0);
                        $mins = 0;
                        if($item->wordcount) $mins = $item->wordcount / $speeds[$item->wpm];
                        if($item->av) $mins += $item->av * 2;
                        if($item->other) $mins += $item->other;
                        $totals[$item->unit]['assimilative'] += $mins;
                        $totals[$item->unit]['total'] += $mins;
                        if($item->FHI) {
                            $totals[$item->unit]['FHI'] += $item->FHI;
                            $totals[$item->unit]['total'] += $item->FHI;
                        }
                        if($item->communication) {
                            $totals[$item->unit]['communication'] += $item->communication;
                            $totals[$item->unit]['total'] += $item->communication;
                        }
                        if($item->productive) {
                            $totals[$item->unit]['productive'] += $item->productive;
                            $totals[$item->unit]['total'] += $item->productive;
                        }
                        if($item->experiential) {
                            $totals[$item->unit]['experiential'] += $item->experiential;
                            $totals[$item->unit]['total'] += $item->experiential;
                        }
                        if($item->interactive) {
                            $totals[$item->unit]['interactive'] += $item->interactive;
                            $totals[$item->unit]['total'] += $item->interactive;
                        }
                        if($item->assessment) {
                            $totals[$item->unit]['assessment'] += $item->assessment;
                            $totals[$item->unit]['total'] += $item->assessment;
                        }
                        if($item->tuition) {
                            $totals[$item->unit]['tuition'] += $item->tuition;
                            $totals[$item->unit]['total'] += $item->tuition;
                        }
                    }
                    $max = 0;
                    foreach($totals as $total) {
                        if($total['total'] > $max) $max = $total['total'];
                    }
                    $max = ceil($max / 10) * 10;
                    $this->attachViewToRegion('main','workload','chart',array('course'=>$course,'totals'=>$totals,'max'=>$max));
                    $this->render();
                }
            }
            else {
                $this->application->responseHandler->redirect('/login/');
            }
        }

        /**
        * actionExport - Exports workload data to a CSV file
        *
        */
        function actionExport() {
            if($this->application->user->isAuthenticated()) {
                $courseId = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'courseId');
                if($courseId) {
                    $course = CCourseModel::loadByPk($courseId);
                    $items = CItemModel::loadByAttributes(array('courseId'=>$courseId),array('order by'=>'`order` asc'))->getObjectArray();

                    header('Content-type: text/csv');
                    header('Content-Disposition: attachment; filename="workload-items.csv"');

                    $headers = array(
                        'Order',
                        'Unit',
                        'Title',
                        'Assimilative',
                        'FHI',
                        'Communication',
                        'Productive',
                        'Experiential',
                        'Interactive',
                        'Assessment',
                        'Tuition',
                        'Total'
                    );
                    $this->application->responseHandler->serveCSVLine($headers);
                    $speeds = array($course->wpmLow,$course->wpmMed,$course->wpmHi);
                    foreach($items as $item) {
                        $array = array();
                        $array[] = $item->order;
                        $array[] = $item->unit;
                        $array[] = $item->title;
                        $assimilative = round($item->wordcount / $speeds[$item->wpm]) + $item->av + $item->other;
                        $array[] = $assimilative;
                        $array[] = $item->FHI;
                        $array[] = $item->communication;
                        $array[] = $item->productive;
                        $array[] = $item->experiential;
                        $array[] = $item->interactive;
                        $array[] = $item->assessment;
                        $array[] = $item->tuition;
                        $array[] = $assimilative + $item->FHI + $item->communication + $item->productive + $item->experiential + $item->interactive + $item->assessment + $item->tuition;
                        $this->application->responseHandler->serveCSVLine($array);
                    }
                }
            }
            else {
                $this->application->responseHandler->redirect('/login/');
            }
        }

        /**
        * actionExportSummary - Exports workload summary data to a CSV file
        *
        */
        function actionExportSummary() {
            if($this->application->user->isAuthenticated()) {
                $courseId = $this->application->requestHandler->requestVar(CRequestHandler::TYPE_INT,'courseId');
                if($courseId) {
                    $course = CCourseModel::loadByPk($courseId);
                    $items = CItemModel::loadByAttributes(array('courseId'=>$courseId),array('order by'=>'`order` asc'))->getObjectArray();

                    header('Content-type: text/csv');
                    header('Content-Disposition: attachment; filename="workload-summary.csv"');

                    $headers = array(
                        'Unit',
                        'Assimilative',
                        'FHI',
                        'Communication',
                        'Productive',
                        'Experiential',
                        'Interactive',
                        'Assessment',
                        'Tuition',
                        'Total'
                    );
                    $this->application->responseHandler->serveCSVLine($headers);
                    $totals = array();
                    $speeds = array($course->wpmLow,$course->wpmMed,$course->wpmHi);
                    foreach($items as $item) {
                        if(!isset($totals[$item->unit])) $totals[$item->unit] = array('assimilative'=>0,'FHI'=>0,'communication'=>0,'productive'=>0,'experiential'=>0,'interactive'=>0,'assessment'=>0,'tuition'=>0,'total'=>0);
                        $mins = 0;
                        if($item->wordcount) $mins = $item->wordcount / $speeds[$item->wpm];
                        if($item->av) $mins += $item->av * 2;
                        if($item->other) $mins += $item->other;
                        $totals[$item->unit]['assimilative'] += $mins;
                        $totals[$item->unit]['total'] += $mins;
                        if($item->FHI) {
                            $totals[$item->unit]['FHI'] += $item->FHI;
                            $totals[$item->unit]['total'] += $item->FHI;
                        }
                        if($item->communication) {
                            $totals[$item->unit]['communication'] += $item->communication;
                            $totals[$item->unit]['total'] += $item->communication;
                        }
                        if($item->productive) {
                            $totals[$item->unit]['productive'] += $item->productive;
                            $totals[$item->unit]['total'] += $item->productive;
                        }
                        if($item->experiential) {
                            $totals[$item->unit]['experiential'] += $item->experiential;
                            $totals[$item->unit]['total'] += $item->experiential;
                        }
                        if($item->interactive) {
                            $totals[$item->unit]['interactive'] += $item->interactive;
                            $totals[$item->unit]['total'] += $item->interactive;
                        }
                        if($item->assessment) {
                            $totals[$item->unit]['assessment'] += $item->assessment;
                            $totals[$item->unit]['total'] += $item->assessment;
                        }
                        if($item->tuition) {
                            $totals[$item->unit]['tuition'] += $item->tuition;
                            $totals[$item->unit]['total'] += $item->tuition;
                        }
                    }
                    foreach($totals as $unit => $total) {
                        $array = array();
                        $array[] = $unit;
                        $array[] = round($total['assimilative']);
                        $array[] = $total['FHI'];
                        $array[] = $total['communication'];
                        $array[] = $total['productive'];
                        $array[] = $total['experiential'];
                        $array[] = $total['interactive'];
                        $array[] = $total['assessment'];
                        $array[] = $total['tuition'];
                        $array[] = round($total['total']);
                        $this->application->responseHandler->serveCSVLine($array);
                    }
                }
            }
            else {
                $this->application->responseHandler->redirect('/login/');
            }
        }

    }
