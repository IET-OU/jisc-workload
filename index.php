<?php
/**
* Front controller.
*
* Jisc / OU Student Workload Tool.
*
* @license   http://gnu.org/licenses/gpl.html GPL-3.0+
* @author    Jitse van Ameijde
* @copyright 2015 The Open University.
*
* Funded by Jisc as part of the Jisc Learning Analytics micro-project funding.
*/

    define("ALL_SYSTEMS_GO",true);

    if(strpos($_SERVER['SERVER_NAME'],'.') !== false) ini_set('display_errors', '0');

    set_exception_handler(function(Exception $e) {
        $handler = CErrorHandler::getInstance();
        $handler->logError("Exception",$e->getMessage(),$e->getFile(),$e->getLine(),$e->getTraceAsString());
    });

    set_error_handler(function($errno,$message,$file,$line) {
        $handler = CErrorHandler::getInstance();
        $handler->logError($errno,$message,$file,$line);
    });

    spl_autoload_register(function($class) {
        if(substr($class,strlen($class) - 5) == 'Model' && file_exists('framework' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . $class . '.php')) {
            include_once('framework' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . $class . '.php');
        }
        else if(substr($class,strlen($class) - 10) == 'Controller' && file_exists('framework' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $class . '.php')) {
            include_once('framework' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $class . '.php');
        }
        else if(substr($class,strlen($class) - 8) == 'Renderer' && file_exists('framework' . DIRECTORY_SEPARATOR . 'renderers' . DIRECTORY_SEPARATOR . $class . '.php')) {
            include_once('framework' . DIRECTORY_SEPARATOR . 'renderers' . DIRECTORY_SEPARATOR . $class . '.php');
        }
        else if(substr($class,strlen($class) - 7) == 'Wrapper' && file_exists('framework' . DIRECTORY_SEPARATOR . 'wrappers' . DIRECTORY_SEPARATOR . $class . '.php')) {
            include_once('framework' . DIRECTORY_SEPARATOR . 'wrappers' . DIRECTORY_SEPARATOR . $class . '.php');
        }
        else if(file_exists('framework' . DIRECTORY_SEPARATOR . 'base' . DIRECTORY_SEPARATOR . $class . '.php')) {
            include_once('framework' . DIRECTORY_SEPARATOR . 'base' . DIRECTORY_SEPARATOR . $class . '.php');
        }
    });

    $app = CWebApplication::getInstance();

    $app->run();


#End.
