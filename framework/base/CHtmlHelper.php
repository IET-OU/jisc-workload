<?php
/**
* CHtmlHelper class file
* 
* @author Jitse van Ameijde <djitsz@yahoo.com>
* @copyright Copyright &copy; 2013 Quantum Frog Ltd
* 
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CHtmlHelper provides functionality for managing html elements
* 
* 
*/
    class CHtmlHelper {
        
        /**
        * alertBox - returns the HTML for an alert box
        * 
        * @param string $class - the class of message (success, info, warning, danger)
        * @param string $message - the message
        * @return string
        */
        public static function alertBox($class, $message) {
            return '<div class="alert alert-' . $class . '"><button type="button" class="close" data-dismiss="alert">x</button>' . $message . '</div>';
        }
        
        /**
        * breadcrumbs - returns the HTML for a breadcrumbs trail
        * 
        * @param string $links - the links of the breadcrumb trail
        * @return string
        */
        public static function breadcrumbs($links,$ajaxLinks = false) {
            $output = '<ol class="breadcrumb">';
            $keys = array_keys($links);
            for($i = 0; $i < count($keys); $i++) {
                $label = $keys[$i];
                if($i == count($keys) - 1) {
                    $output .= '<li>' . htmlentities($label) . '</li>';
                }
                else {
                    $output .= '<li><a' . ($ajaxLinks == true ? ' class="ajax-link"' : ''). ' href="' . $links[$label][0];
                    $first = true;
                    $indexes = array_keys($links[$label]);
                    unset($indexes[0]);
                    foreach($indexes as $index) {
                        $value = $links[$label][$index];
                        if($first == false) $output .= '&';
                        else $output .= '?';
                        $output .= $index . '=' . $value;
                        $first = false;
                    }
                    $output .= '">' . htmlentities($label) . '</a></li>';
                }
            }
            $output .= '</ol>';
            return $output;
        }
        
        /**
        * pagination - shows a pagination control with clickable browse icons
        * 
        * @param CPaginator $paginator
        */
        public static function pagination($paginator,$link,$linkClass=null) {
            $output = '';
            if($linkClass != null) $class = ' class="' . $linkClass . '"';
            else $class = '';
            if(strpos($link,'?') === false) $separator = '?';
            else $separator = '&';
            $first = 1;
            $last = $paginator->numPages;
            if($paginator->numPages > 15) {
                $first = $paginator->pageIndex - 7;
                $last = $paginator->pageIndex + 8;
                if($first < 0) {
                    $first = 0;
                    $last = 15;
                }
                else if($last > $paginator->numPages) {
                    $first = $paginator->numPages - 15;
                    $last = $paginator->numPages;
                }
            }
            if($paginator->numPages > 1) {
                $output .= '<ul class="pagination">' ;
                if($paginator->pageIndex == 1) {
                    $output .= '<li class="disabled"><a href="#">&laquo;</a></li>';
                }
                else {
                    $output .= '<li><a' . $class . ' href="' . $link . $separator . '_page=' . ($paginator->pageIndex - 1) . '">&laquo;</a></li>';
                }
                for($i = $first; $i <= $last; $i++) {
                    if($i == $paginator->pageIndex) $output .= '<li class="active"><a href="#">' . $i . '</a></li>';
                    else $output .= '<li><a' . $class . ' href="' . $link . $separator . '_page=' . $i . '">' . $i . '</a></li>';
                }
                if($paginator->pageIndex == $paginator->numPages) {
                    $output .= '<li class="disabled"><a href="#">&raquo;</a></li>';
                }
                else {
                    $output .= '<li><a' . $class . ' href="' . $link . $separator . '_page=' . ($paginator->pageIndex + 1) . '">&raquo;</a></li>';
                }
                $output .= '</ul>';
            }
            return $output;
        }
        
        /**
        * sanitiseHtml - sanitises HTML code
        * 
        * @param string $html - the HTML content to be sanitised
        * @return string - sanitised HTML content
        */
        public static function sanitiseHtml($html) {
            require_once(CWebApplication::getInstance()->frameworkRoot . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'HTMLPurifier' . DIRECTORY_SEPARATOR . 'HTMLPurifier.auto.php');
            $config = HTMLPurifier_Config::createDefault();
            $purifier = new HTMLPurifier($config);
            $result = $purifier->purify($html);
            return $result;
        }
        
        
        /**
        * textPreview - generates a text preview from HTML code
        * 
        * @param string $html - the HTML content to create preview of
        * @param int $length - the length in characters of the preview
        * @return string - string preview
        */
        public static function textPreview($html,$length) {
            $content = str_replace('</p>',"\r\n",$html);
            $content = strip_tags($content);
            if(strlen($content) > $length) $content = substr($content,0,$length) . '...';
            $content = nl2br($content);
            return $content;            
        }
        
    }  
?>