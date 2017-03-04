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

use Google_Service_Drive;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Apps
{

    use GClient;

    // contains app configuration object
    private $appconfig;

    private $logger;

    public function __construct(Cfg $c)
    {

        // config object
        $this->appconfig = $c;

        // create a log channel for debug
        $this->logger = new Logger('GDrive');
        $this->logger->pushHandler(new StreamHandler(realpath(__DIR__ . '/..') . '/log/app.log'));


    }


    public function start()
    {

        session_start();


        // NOTE: simple routing
        // if no route  -- show main page
        // in other case we take into consideration only first part of URI


        $q = parse_url(strip_tags($_SERVER['REQUEST_URI']));

        $route = explode("/", $q['path'])[1];

        switch ($route) {

            //
            case '':

                if (isset($_SESSION['_token'])) {

                    $cl = $this->CreateClient();
                    $cl->setAccessToken($_SESSION['_token']);

                    if ($cl->isAccessTokenExpired()) {
                        $this->logger->info('Try refresh expired token');
                        $access_token = $cl->fetchAccessTokenWithRefreshToken();
                        $_SESSION['_token'] = $access_token;
                        $this->logger->info(print_r($access_token, true));
                    };
                    $this->renderView(realpath(__DIR__ . '/..') . '/view/dash.php');

                };

                $this->renderView(realpath(__DIR__ . '/..') . '/view/mainview.php');
                break;

            case 'auth' :

                $cl = $this->CreateClient();

                if (!isset($_GET['code'])) {
                    $auth_url = $cl->createAuthUrl();
                    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
                } else {

                    $access_token = $cl->fetchAccessTokenWithAuthCode($_GET['code']);
                    $_SESSION['_token'] = $access_token;

                    $this->logger->info(print_r($access_token, true));


                    header('Location: ' . '/');

                }
                break;


            case 'loadsource':

                $key = filter_var($_POST['key'], FILTER_SANITIZE_STRING);
                $isShowShared = filter_var($_POST['shared'], FILTER_SANITIZE_STRING);


                $cl = $this->CreateClient();
                $cl->setAccessToken($_SESSION['_token']);

                // Refresh the token if it's expired.
                if ($cl->isAccessTokenExpired()) {

                    $access_token = $cl->fetchAccessTokenWithRefreshToken();
                    $_SESSION['_token'] = $access_token;
                }

                $drive = new Google_Service_Drive($cl);

               $user =  $drive->about->get(['fields'=>'user'])->getUser();

              $qstring = 'trashed=false';

              if ($isShowShared=='false')  $qstring .= " and '$user->emailAddress' in owners";

                if ($key == 'root') {

                    $files = $drive->files->listFiles(['q' => $qstring, 'fields' => 'files(id, name,size,owners(displayName,emailAddress,me,photoLink),mimeType,trashed,shared,webContentLink,webViewLink)']);

                } else {

                    $qstring .=  " and '$key' in parents";

                    $files = $drive->files->listFiles(['q' =>$qstring , 'fields' => 'files(id, name,size,owners(displayName,emailAddress,me,photoLink),mimeType,trashed,shared,webContentLink,webViewLink)']);

                }

                // die( var_dump($files));

                $treedata = [];

                foreach ($files as $f) {


                    $treenode = [];

                    $treenode['key'] = $f->id;
                    $treenode['title'] = $f->name;
                    if ('application/vnd.google-apps.folder' == $f->mimeType) {
                        $treenode ['lazy'] = 'true';
                        $treenode ['folder'] = 'true';
                    }
                    $treenode['size'] = round($f->size / 1024, 0);
                    $treenode['owner'] = $f->owners[0]['displayName'];
                    $treenode['shared'] = $f->shared;
                    $treenode['dlink'] = $f->webContentLink;


                    array_push($treedata, $treenode);
                };

                $this->sendAjaxResponse($treedata);


                break;


            case 'delete':


                $k = $_POST['keys'];

                $cl = $this->CreateClient();
                $cl->setAccessToken($_SESSION['_token']);

                // Refresh the token if it's expired.
                if ($cl->isAccessTokenExpired()) {

                    $access_token = $cl->fetchAccessTokenWithRefreshToken();
                    $_SESSION['_token'] = $access_token;
                }

                $drive = new Google_Service_Drive($cl);


                array_walk($k,function($item,$key,$drv){

                    $drv->files->delete($item);


                },$drive);
                $this->sendAjaxResponse($k);


                break;

            case 'download':

                break;
            default:

                $this->renderView(realpath(__DIR__ . '/..') . '/view/404.php');

        };


        /*


        */
    }


    /**
     *  Simple template render
     *
     * @param $file -- template file
     */


    public function renderView($file, array $data = [])
    {


        $f = $this->get_include_contents($file, $data);

        die($f);


    }


    private function get_include_contents($filename, array $data = [])
    {


        if (is_file($filename)) {

            $data['viewfile'] = $filename;

            ob_start();
            ob_implicit_flush(false);

            extract($data, EXTR_SKIP);

            include $this->appconfig->get('layout');

            return ob_get_clean();
        }
        return false;
    }

    /**
     * Ajax response
     *
     *
     * @param unknown $file
     */


    public function sendAjaxResponse($response)
    {


        die(json_encode($response, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP));


    }


    public function IsAjax()
    {

        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

    }


}