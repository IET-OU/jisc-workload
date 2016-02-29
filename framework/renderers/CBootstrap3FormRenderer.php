<?php
/**
* CBootstrap3FormRenderer class file
* 
* @author Jitse van Ameijde <djitsz@yahoo.com>
* @copyright Copyright &copy; 2013 Quantum Frog Ltd
* 
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CBootstrap3FormRenderer provides functionality for rendering web forms using Twitter Bootstrap 3 styling
* 
* 
*/
    class CBootstrap3FormRenderer {
        
        /**
        * @var CForm _form The form to render
        */
        protected $_form;
        
        /**
        * @var array $_options - the options for rendering this form
        * 
        */
        protected $_options;

        
        /**
        * Constructor - initialises variables
        * 
        * @param CForm $form The form to render
        */
        public function __construct($form, $options = array()) {
            $this->_form = $form;
            $this->_options = $options;
        }
        
        /**
        * asHtml - returns the HTML representation of the form
        * 
        * @param mixed $options
        */
        public function asHtml() {
            $output = $this->renderHeader();
            $output .= $this->renderFields();
            $output .= $this->renderFooter();
            return $output;
        }
        
        /**
        * renderHeader - renders the header of the form
        * 
        */
        public function renderHeader() {
            $formName = $this->_form->getName();
            if(isset($this->_options['style']) && $this->_options['style'] == 'horizontal') $horizontal = true;
            else $horizontal = false;
            $class = '';
            if($this->_form->isAjax()) $class = 'ajax-form';
            if($horizontal) $class .= ' form-horizontal';
            $output = '<form name="' . $formName . '" action="' . $this->_form->getAction() . '" method="post" enctype="multipart/form-data" accept-charset="UTF-8"' . ($class != '' ? ' class="' . $class . '"' : '') . '>';
            $output .= '<input type="hidden" name="_formId" value="' . $this->_form->getFormId() . '" />';
            if($this->_form->getMessage() != null) {
                $output .= '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">x</button><h4 class="alert-haeding">notice</h4><p>' . htmlentities($this->_form->getMessage()) . '</p></div>';
            }
            return $output;
        }
        
        /**
        * renderFields - renders the form fields
        * 
        */
        public function renderFields() {
            $output = '';
            foreach($this->_form as $name => $specs) {
                $output .= $this->renderControl($name,$specs);
            }
            return $output;
        }
        
        /**
        * renderFooter - renders the footer of the form
        * 
        */
        public function renderFooter($options = null) {
            $output = '</form>';
            return $output;
        }
        
        /**
        * renderControl - renders the control with the provided name
        * 
        * @param string $name - the name of the control to render
        * @param array $options
        */
        public function renderControl($name) {
            $control = $this->_form[$name];
            $output = '';
            if($control['type'] == 'controlGroup') {
                $output .= '<fieldset>';
                $output .= '<div class="fieldset"' . (isset($control['hidden']) && $control['hidden'] == true ? ' style="display: none;"' : '') . '>';
                foreach($control['controls'] as $subcontrolName => $subcontrol) {
                    $output .= $this->controlToHtml($subcontrolName,$subcontrol);
                }
                $output .= '</div></fieldset>';
            }
            else {
                $output = $this->controlToHtml($name,$control);
            }
            return $output;
        }
        
        /**
        * controlToHtml - returns the HTML representation of the provided control
        * 
        * @param string $name - the name of the control to render
        * @param control - the control to render
        * @param array $options
        */
        private function controlToHtml($name,$control) {
            $output = '';
            $options = $this->_options;
            $formName = $this->_form->getName();
            $horizontal = isset($options['style']) && $options['style'] == 'horizontal';
            $specs = $this->_form[$name];
            $required = '';
            if(isset($specs['required']) && $specs['required'] == true) $required = '<span style="color:red;font-weight:bold;">*</span>';
            if(isset($specs['id'])) $id = $specs['id'];
            else $id = $formName . '-' . $name;
            if($specs['type'] == 'hidden') {
                $output .= '<input type="hidden" id="' . $id . '" name="' . $name . '" value="' . $specs['value'] . '" ' . (isset($specs['class']) ? 'class="' . $specs['class'] . '"' : '') . '/>';
                if(isset($specs['label'])) {
                    if($horizontal) $class = ' class="col-md-3"';
                    else $class = '';
                    $output .= '<div' . $class . '><label>' . htmlentities($specs['label'],ENT_COMPAT,'utf-8') . $required . '</label></div>';
                }
                if(isset($specs['error'])) {
                    $output .= '<div><label class="error">' . htmlentities($specs['error'],ENT_COMPAT,'utf-8') . '</label></div>';
                }
                return $output;
            }
            if($specs['type'] == 'html') {
                return $specs['html'];
            }
            $data = '';
            if(isset($specs['data'])) {
                foreach($specs['data'] as $attr => $value) {
                    $data .= ' data-' . $attr . '="' . $value . '"';
                }
            }
            if(isset($specs['hidden']) && $specs['hidden']==true) $hidden = ' style="display:none"';
            else $hidden = '';
            if($specs['type'] != 'submit' && $specs['type'] != 'button' && $specs['type'] != 'dropdownButton' && $specs['type'] != 'fileupload') {
                $output .= '<div id="formGroup-' . $name . '"' . $hidden . ' class="form-group clearfix' . (isset($specs['error']) ? ' error' : '' ). '">';
            }
            if(isset($specs['label']) && $specs['type'] != 'button' && $specs['type'] != 'submit' && $specs['type'] != 'dropdownButton' && $specs['type'] != 'fileupload') {
                $labelClasses = array();
                if($horizontal) {
                    $labelClasses[] = 'col-md-3';
                    $controlClass = ' class="col-md-8"';
                }
                else {
                    $controlClass = '';
                }
                if(isset($specs['tooltip'])) {
                    $labelClasses[] = 'has-tooltip';
                    $tooltip = ' <span class="glyphicon glyphicon-info-sign has-tooltip"' . ' title="' . $specs['tooltip'] . '"' . '</span>';
                }
                else $tooltip = '';
                if(count($labelClasses) > 0) $labelClass = ' class="' . implode(' ',$labelClasses) . '"';
                else $labelClass = '';
                $output .= '<div class="control-label"><label' . $labelClass . ' for="' . $formName . '-' . $name . '">' . htmlentities($specs['label']) . $required . $tooltip . '</label></div>';
                $output .= '<div' . $controlClass . '>';
            }
            else if($specs['type'] != 'button' && $specs['type'] != 'submit' && $specs['type'] != 'dropdownButton' && $specs['type'] != 'fileupload'){
                if($horizontal) $output .= '<div class="col-md-10">';
                else $output .= '<div>';
            }
            if(isset($specs['class'])) $class = ' ' . $specs['class'];
            else $class = '';
            if(isset($specs['placeholder'])) $placeholder = ' placeholder="' . htmlentities($specs['placeholder'],ENT_COMPAT,'UTF-8') . '"';
            else $placeholder = '';
            if(isset($specs['disabled']) && $specs['disabled']==true) {
                $disabled = ' disabled="disabled"';
            }
            else {
                $disabled = '';
            }
            switch($specs['type']) {
                case 'textinput':
                    if(isset($specs['checkboxAddon'])) {
                        if(isset($specs['checkboxAddon']['class'])) $class2 = ' class="' . $specs['checkboxAddon']['class'] . '"';
                        else $class2 = '';
                        if(isset($specs['checkboxAddon']['checked']) && $specs['checkboxAddon']['checked'] == true) $checked = ' checked="checked"';
                        else $checked = '';
                        $output .= '<div class="input-group"><span class="input-group-addon"><label class="control-label" style="margin-right:5px;">' . 
                            htmlentities($specs['checkboxAddon']['label']). '</label>' . 
                            '<input type="checkbox" name="' . $specs['checkboxAddon']['name'] . '"' . $class2 . $checked . '></span>';
                    }
                    else if(isset($specs['buttonAddon'])) {
                        $output .= '<div class="input-group"><span class="input-group-btn">';
                        if(isset($specs['buttonAddon']['class'])) {
                            $class2 = ' ' . $specs['buttonAddon']['class'];
                        }
                        else $class2 = '';
                        if(isset($specs['buttonAddon']['url'])) {
                            $output .= '<a href="' . $specs['buttonAddon']['url'] . '" class="btn' . $class2 . '">' . htmlentities($specs['buttonAddon']['label'],ENT_COMPAT,'UTF-8') . '</a>';
                        }
                        else {
                            $output .= '<button class="btn' . $class . '">' . htmlentities($specs['buttonAddon']['label'],ENT_COMPAT,'UTF-8') . '</button>';
                        }
                        $output .= '</span>';
                    }
                    $output .= '<input type="text" id="' . $id . '" name="' . $name . '"' . $placeholder . $disabled . $data . ' class="form-control' . $class .'"' . (isset($specs['value']) ? ' value="' . htmlentities($specs['value'],ENT_COMPAT,'utf-8') . '"' : '') . ' />';
                    if(isset($specs['checkboxAddon']) || isset($specs['buttonAddon'])) {
                        $output .= '</div>';
                    }
                    break;
                case 'password':
                    $output .= '<input type="password" id="' . $id . '" name="' . $name . '"' . $placeholder . $disabled . $data . ' class="form-control' . $class .'" />';
                    break;
                case 'submit':
                    $output .= '<button type="submit" id="' . $id . '" name="' . $name . '" class="btn' . $class . '"' . $disabled . $data . '>' . htmlentities($specs['label'],ENT_COMPAT,'UTF-8') . '</button>';
                    break;
                case 'button':
                    if(isset($specs['clearboth'])) {
                        $output .= '<div class="clearfix">';
                    }
                    if(isset($specs['url'])) {
                        $output .= '<a href="' . $specs['url'] . '" id="' . $id . '" class="btn' . $class . '"' . $disabled . $data .'>' . htmlentities($specs['label'],ENT_COMPAT,'UTF-8') . '</a>';
                    }
                    else {
                        $output .= '<button id="' . $id . '" name="' . $name . '" class="btn' . $class . '"' . $disabled . $data . '>' . htmlentities($specs['label'],ENT_COMPAT,'UTF-8') . '</button>';
                    }
                    if(isset($specs['clearboth'])) {
                        $output .= '</div>';
                    }
                    break;
                case 'select':
                    if(isset($specs['multiple']) && $specs['multiple'] == true) {
                        $values = array();
                        if(isset($specs['value'])) {
                            if(is_array($specs['value'])) {
                                foreach($specs['value'] as $value) {
                                    $values[$value] = true;
                                }
                            }
                            else {
                                $values[$specs['value']] = true;
                            }
                        }
                    }
                    else if(isset($specs['value'])){
                        $values[$specs['value']] = true;
                    }
                    $output .= '<select id="' . $id . '" name="' . $name . '" class="form-control' . (isset($specs['class']) ? ' ' . $specs['class'] : '') . '"' . (isset($specs['multiple']) && $specs['multiple'] == true ? ' multiple' : '') . $data . '>';
                    foreach($specs['options'] as $option) {
                        if(isset($option['options'])) {
                            $output .= '<optgroup label="' . htmlentities($option['label'],ENT_COMPAT,'utf-8') . '">';
                            foreach($option['options'] as $suboption) {
                                $output .= '<option value="' . htmlentities($suboption['value'],ENT_COMPAT,'utf-8') . '"' . (isset($values[$suboption['value']]) ? ' selected' : '') . '>' . htmlentities($suboption['label'],ENT_COMPAT,'UTF-8') . '</option>';
                            }
                            $output .= '</optgroup>';
                        }
                        else {
                            $output .= '<option value="' . htmlentities($option['value'],ENT_COMPAT,'utf-8') . '"' . (isset($values[$option['value']]) ? ' selected' : '') . '>' . htmlentities($option['label'],ENT_COMPAT,'UTF-8') . '</option>';
                        }
                    }
                    $output .= '</select>';
                    break;
                case 'dropdownButton':
                    if(isset($specs['clearboth'])) {
                        $output .= '<div class="clearfix">';
                    }
                    $output .= '<div class="btn-group">';
                    $output .= '<button type="button" id="' . $id . '" class="btn dropdown-toggle' . (isset($specs['class']) ? ' ' . $specs['class'] : '') . '" data-toggle="dropdown"' . $data . '>';
                    $output .= htmlentities($specs['label'],ENT_COMPAT,'utf-8') . ' <span class="caret"></span>';
                    $output .= '</button><ul class="dropdown-menu" role="menu">';
                    foreach($specs['options'] as $option) {
                        if(isset($option['data'])) {
                            $data = '';
                            foreach($option['data'] as $id => $value) {
                                $data .= ' data-' . $id . '="' . $value . '"';
                            }
                        }
                        $output .= '<li><a href="' . $option['url'] . '"' . (isset($option['class']) ? ' class="' . $option['class'] . '"' : '') . $data . '>' . htmlentities($option['label'],ENT_COMPAT,'UTF-8') . '</a></li>';
                    }
                    $output .= '</ul></div>';
                    if(isset($specs['clearboth'])) {
                        $output .= '</div>';
                    }
                    break;
                case 'buttonGroup':
                    if(!isset($specs['separate']) || $specs['separate'] == true) {
                        $output .= '<div class="btn-group">';
                    }
                    foreach($specs['buttons'] as $button) {
                        if(isset($button['url'])) {
                            $output .= '<a href="' . $button['url'] . '" class="btn' . $class . '">' . htmlentities($button['label'],ENT_COMPAT,'UTF-8') . '</a>';
                        }
                        else {
                            $output .= '<button class="btn' . $class . '">' . htmlentities($button['label'],ENT_COMPAT,'UTF-8') . '</button>';
                        }
                    }
                    if(!isset($specs['separate']) || $specs['separate'] == true) {
                        $output .= '</div>';
                    }
                    break;                
                case 'radioGroup':
                    if(isset($specs['inline']) && $specs['inline']==true) {
                        $class = ' class="radio-inline"';
                        $inline = true;
                    }
                    else {
                        $class = '';
                        $inline = false;
                    }
                    $n = 1;
                    foreach($specs['options'] as $option) {
                        if($inline==false) $output .= '<div class="radio">';
                        if(isset($option['id'])) $id = $option['id'];
                        else $id = $formName . '-' . $name . '-' . $n;
                        $output .= '<label' . $class .' for="' . $id . '">';
                        $output .= '<input type="radio" name="' . $name . '" id="' . $id . '"' . $disabled . ' value="' . htmlentities($option['value'],ENT_COMPAT,'UTF-8') . '"' . 
                            (isset($specs['value']) && $specs['value'] == $option['value'] ? ' checked="checked"' : '') . ' />';
                        $output .= htmlentities($option['label'],ENT_COMPAT,'UTF-8') . '</label>';
                        if($inline==false) $output .= '</div>';
                        $n++;
                    }
                    break;
                case 'checkboxGroup':
                    if(isset($specs['inline']) && $specs['inline']==true) {
                        $class = ' class="checkbox-inline"';
                        $inline = true;
                    }
                    else {
                        $class = '';
                        $inline = false;
                    }
                    foreach($specs['options'] as $boxName => $option) {
                        if($inline==false) $output .= '<div class="checkbox">';
                        $output .= '<label' . $class .' for="' . $formName . '-' . $boxName . '">';
                        if(isset($option['disabled']) && $option['disabled']==true) {
                            $disabled = ' disabled="disabled"';
                        }
                        else {
                            $disabled = '';
                        }
                        if(isset($option['id'])) $id = $option['id'];
                        else $id = $formName . '-' . $boxName;
                        $output .= '<input type="checkbox" name="' . $boxName . '" id="' . $id . '" value="' . htmlentities($option['value'],ENT_COMPAT,'utf-8') . '"' .  $disabled .
                            (isset($option['checked']) && $option['checked'] == true ? ' checked="checked"' : '') . ' />';
                        $output .= htmlentities($option['label'],ENT_COMPAT,'UTF-8') . '</label>';
                        if($inline==false) $output .= '</div>';
                    }
                    break;
                case 'textarea':
                    if(isset($specs['value'])) $value = $specs['value'];
                    else $value = '';
                    if(isset($specs['numRows'])) $numRows = ' rows="' . $specs['numRows'] . '"';
                    else $numRows = ' rows="4"';
                    if(isset($specs['richtext']) && $specs['richtext'] == true) {
                        $output .= '<textarea id="' . $id . '" name="' . $name . '"' . $disabled . $data . $numRows . ' class="form-control richtext' . $class .'">' . $value . '</textarea>';
                        break; 
                    }
                    else {
                        $value = htmlentities($value);
                        $output .= '<textarea id="' . $id . '" name="' . $name . '"' . $disabled . $data . $numRows . ' class="form-control' . $class .'">' . $value . '</textarea>';
                    }
                    break;
                case 'fileupload':
                    if(isset($specs['clearboth'])) {
                        $output .= '<div class="clearfix">';
                    }
                    $output .= '<input type="file" name="' . $name . '" data-input="false" class="filestyle" data-buttonText="' . $specs['label'] . '" data-classButton="btn' . (isset($specs['class']) ? ' ' . $specs['class'] : '') . '" />';
                    if(isset($specs['clearboth'])) {
                        $output .= '</div>';
                    }
                    break;
                case 'captcha':
                    $output .= htmlentities(CWebApplication::getInstance()->config['captcha'][$_SESSION['captchas'][$this->_form->getName()]['index']]['prompt']) . '<br />';
                    $output .= '<input type="text" id="' . $id . '" name="' . $name . '"' . $placeholder . ' class="form-control' . $class .'" />';
                    break;
            }
            if(isset($specs['error'])) {
                $output .= '<label for="' . $id . '" class="error">' . htmlentities($specs['error']) . '</label>';
            }
            if(isset($specs['help'])) {
                $output .= '<p class="help-block">' . htmlentities($specs['help'],ENT_COMPAT,'utf-8') . '</p>';
            }
            if($specs['type'] != 'submit' && $specs['type'] != 'button' && $specs['type'] != 'dropdownButton' && $specs['type'] != 'fileupload') {
                $output .= '</div></div>';
            }
            return $output;
        }
    }
?>