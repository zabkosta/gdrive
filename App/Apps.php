<?php

/**
 * Main application
 *
 * @link http://gdrive.unima.com.ua/
 * @author Zablotskyi kostiantyn
 * @license GNU GPLv3 https://www.gnu.org/licenses/gpl-3.0.html
 *
 *
 *
 */

namespace GDrive;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Apps
{
    // contains app configuration object
    public $appconfig;

    private $logger;
    private $appcontroller;

    public function __construct(Cfg $c)
    {
        $this->appconfig = $c;

        //create application controller
        $this->appcontroller = new AppController($c);

        // create a log channel for debug
        $this->logger = new Logger('GDriveApps');
        $this->logger->pushHandler(new StreamHandler(realpath(__DIR__ . '/..') . '/log/app.log'));

    }


    public function start()
    {

        session_start();

        // NOTE: simple routing
        // if no route  -- show main page

        $q = parse_url(strip_tags($_SERVER['REQUEST_URI']));
        $route = explode("/", $q['path'])[1];

        switch ($route) {

            //
            case '':
                $state = $this->appcontroller->indexAction();
                if ($state === false)
                    $this->appcontroller->renderView(realpath(__DIR__ . '/..') . '/view/error.php');
                break;

            case 'auth' :

                $state = $this->appcontroller->authAction();
                if ($state === false)
                    $this->appcontroller->renderView(realpath(__DIR__ . '/..') . '/view/error.php');
                break;

            case 'loadsource':
                $this->appcontroller->loadAction();
                break;


            case 'delete':
                $this->appcontroller->deleteAction();
                break;

            case 'download':
                $this->appcontroller->downloadAction();
                break;

            case 'revoke' :
                $this->appcontroller->revokeAction();
                break;

            default:
                $this->appcontroller->renderView(realpath(__DIR__ . '/..') . '/view/404.php');

        };


    }


}