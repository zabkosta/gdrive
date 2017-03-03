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

use GDrive\Cfg;
use GDrive\GClient;
use Google_Service_Drive;
use Google_Service_Drive_FileList;

class Apps {


    private $appconfig;


    public function __construct(Cfg $c) {

      $this->appconfig = $c;


    }


    public function start()
    {

        $v = session_start();

      //  var_dump($v);

         // simple routing

        // if no route  -- main page
        // in other case we take into consideration only first part


        $q = parse_url(strip_tags($_SERVER['REQUEST_URI']));

        $route =  explode("/",$q['path'])[1];

        switch ($route) {


                   case '':
                       $this->renderView(realpath(__DIR__ . '/..').'/view/mainview.php');
                       break;

            case 'auth' :

                            $cl = GClient::CreateClient();
                            $auth_url =$cl->createAuthUrl();

                            header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));


                             break;

            case 'oauthcb' :
                                $code = $_GET['code'];

                                $cl = GClient::CreateClient();


                            if (! isset($code)) {
                                     $auth_url =  $cl->createAuthUrl();
                                     header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
                             } else {

                                  $cl->authenticate($code);
                                  $access_token = $cl->getAccessToken();
                                  $_SESSION['_token'] = $access_token;

                                  header('Location: ' . '/manage');
                              }


                              break;



            case 'manage':

              //  print_r($_SESSION);

                         $access_token  =  $_SESSION['_token'];

                         $cl = GClient::CreateClient();
                         $cl->setAccessToken($access_token);

                $drive = new Google_Service_Drive($cl);
                $files = $drive->files->listFiles(array())->getFiles();

                foreach ($files as $f) {

                    echo  $f->name .'<br>';
                }



                                  break;
                   default:

                              $this->renderView(realpath(__DIR__ . '/..').'/view/404.php');

        };


/*


*/
    }

	



	/**
	 *  Simple template render
	 *
	 * @param $file  -- template file
	 */
	
	
	public function renderView($file){
	
	
		$f = $this->get_include_contents($file);
	
		die($f);
	

	}


    private function get_include_contents($filename) {

        if (is_file($filename)) {

            ob_start();
            ob_implicit_flush(false);

            require $filename;
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
	

	public function sendAjaxResponse ($response){
	
	
		die(json_encode($response,JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP ));
	
	
	
	}


    public function IsAjax(){

        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

    }







}