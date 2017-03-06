<?php
/**
 * Application controller
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


class AppController
{
    use GClient;

    private $logger;

    private $cfg;

    // list google doc export formats which we  want use
    private $preferedformats = ['application/pdf', 'application/zip', 'image/jpeg'];



    public function __construct(Cfg $c)
    {

        $this->cfg = $c;

        // create a log channel for debug
        $this->logger = new Logger('GDriveController');
        $this->logger->pushHandler(new StreamHandler(realpath(__DIR__ . '/..') . '/log/app.log'));


    }


    /**
     *
     *   Main action
     *   Decide whether  render start screen or dashboard when user grant access to our app
     *
     * @return bool
     */
    public function indexAction()
    {


        if (isset($_SESSION['_token']) && $_SESSION['_token']) {

            $cl = $this->CreateClient();
            $cl->setAccessToken($_SESSION['_token']);
            $this->logger->info("Start with token: " . print_r($_SESSION['_token'], true));

            $this->renderView(realpath(__DIR__ . '/..') . '/view/dash.php');

        };

        $this->renderView(realpath(__DIR__ . '/..') . '/view/mainview.php');

        return true;
    }


    /**
     *   Auth action +
     *   Google OAuth2 callback
     *
     *
     *
     * @return bool
     */
    public function authAction()
    {

        if (isset($_GET['error'])) return false;

        $cl = $this->CreateClient();

        if (!isset($_GET['code'])) {

            $auth_url = $cl->createAuthUrl();
            header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
            exit;

        } else {

            $access_token = $cl->fetchAccessTokenWithAuthCode($_GET['code']);
            $this->logger->info("fetchAccessTokenWithAuthCode: " . print_r($access_token, true));

            if (isset($access_token['error'])) return false;

            $_SESSION['_token'] = $access_token;
            header('Location: ' . '/');
            exit;
        }

        return true;
    }


    /**
     *      Load tree
     *
     *    POST key  --  'root'  if we load nodes within root
     *                  parents folder ID for lazy load
     *
     *    POST shared   include in file.list  shared with user files or not
     *
     * @return bool
     */
    public function loadAction()
    {

        // if session expire
        if ( !isset($_SESSION['_token']) ) {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }

        $key = filter_var($_POST['key'], FILTER_SANITIZE_STRING);
        $isShowShared = filter_var($_POST['shared'], FILTER_SANITIZE_STRING);



        $cl = $this->CreateClient();
        $cl->setAccessToken($_SESSION['_token']);

        $drive = new Google_Service_Drive($cl);
        $user = $drive->about->get(['fields' => 'user'])->getUser();

        // query filter string
        $qstring = 'trashed=false';

        if ($isShowShared == 'false') $qstring .= " and '$user->emailAddress' in owners";

        if ($key == 'root') {

            $files = $drive->files->listFiles(['q' => $qstring, 'fields' => 'files(id, name,size,owners(displayName,emailAddress,me,photoLink),mimeType,trashed,shared,webContentLink,webViewLink)']);

        } else {

            $qstring .= " and '$key' in parents";
            $files = $drive->files->listFiles(['q' => $qstring, 'fields' => 'files(id, name,size,owners(displayName,emailAddress,me,photoLink),mimeType,trashed,shared,webContentLink,webViewLink)']);

        }

        //  prepare data for js tree
        $treedata = [];

        foreach ($files as $f) {


            $treenode = [];

            $treenode['key'] = $f->id;
            $treenode['title'] = $f->name;
            if ('application/vnd.google-apps.folder' == $f->mimeType) {
                $treenode ['lazy'] = 'true';
                $treenode ['folder'] = 'true';
            }
            $treenode['mimeType'] = $f->mimeType;
            $treenode['size'] = round($f->size / 1024, 0);
            $treenode['owner'] = $f->owners[0]['displayName'];
            $treenode['shared'] = $f->shared;
            $treenode['dlink'] = $f->webContentLink;
            $treenode['webViewLink'] = $f->webViewLink;


            array_push($treedata, $treenode);
        };

        $this->sendAjaxResponse($treedata);

        return true;

    }


    /**
     *    Permanently deletes a file owned by the user without moving it to the trash.
     *
     *    POST key     - file ID
     *
     *    @return bool  action state
     */
    public function deleteAction()
    {

        // if session expire
        if ( !isset($_SESSION['_token']) ) {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }

        // clean param
        $k = filter_var($_POST['key'], FILTER_SANITIZE_STRING);

        // create google client
        $cl = $this->CreateClient();
        $cl->setAccessToken($_SESSION['_token']);

        // perform action
        $drive = new Google_Service_Drive($cl);
        $drive->files->delete($k);

        $this->logger->info("Deletr file ID: " . $k);

        // send some response
        $this->sendAjaxResponse(['state' => '200', 'deleted' => [$k]]);
        return true;
    }


    /**
     *
     *   Download Google Documents
     *
     *   POST key     - file ID
     *   POST mime    - file mimeType
     *
     *
     */
    public function downloadAction()
    {

        // if session expire
        if ( !isset($_SESSION['_token']) ) {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }

        // its a Google Doc, we need use files.export

        // clean param
        $k = filter_var($_POST['key'], FILTER_SANITIZE_STRING);
        $m = filter_var($_POST['mime'], FILTER_SANITIZE_STRING);

        $this->logger->info("Download file ID: " . $k);

        // create google client
        $cl = $this->CreateClient();
        $cl->setAccessToken($_SESSION['_token']);

        // perform action
        $drive = new Google_Service_Drive($cl);

      // get file name by ID
        $filename = $drive->files->get($k, ['fields' => 'name'])->getName();

        // get all posible export format for this document
        $eformat = $drive->about->get(['fields' => 'exportFormats'])->getExportFormats();

        $eformat = $eformat[$m];

        // now need to decide which  export format use
        // 'application/zip', 'application/pdf',  'image/jpeg'
        $eformat = array_intersect($this->preferedformats, $eformat);

        // in case if there are several possible format we choose first, i.e. $eformat[0]

        $response = $drive->files->export($k, $eformat[0], array(
            'alt' => 'media'));

        //add filename extension
        $filename .= "." . explode('/', $eformat[0])[1];

        $content = $response->getBody()->getContents();

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($content));

        // lets download
        exit($content);


    }


    /**
     *  Logout with revoking access
     *
     * @param $file -- template file
     */
    public function revokeAction()
    {
        // if session expire
        if ( !isset($_SESSION['_token']) ) {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }

        // create google client
        $cl = $this->CreateClient();
        $cl->setAccessToken($_SESSION['_token']);

        $cl->revokeToken($_SESSION['_token']);

        unset ($_SESSION['_token']);
        header('Location: ' . '/');
        exit;
    }


    /**
     *  Simple template render
     *
     * @param $file -- template file
     */


    public function renderView($file, array $data = [])
    {

        $f = $this->get_include_contents($file, $data);

        if ($f !== false )  die($f);

        $file = realpath(__DIR__ . '/..') . '/view/404.php';
        $f = $this->get_include_contents($file);

        if ($f !== false )  die($f);

        die(422);

    }

    /**
     * Read template and implement variable
     *
     * @param $data  -- variable in template
     */


    private function get_include_contents($filename, array $data = [])
    {
         if (is_file($filename)) {

            $data['viewfile'] = $filename;

            ob_start();
            ob_implicit_flush(false);

            extract($data, EXTR_SKIP);

         // all template included via layout
            include $this->cfg->get('layout');

            return ob_get_clean();
        }

        return false;
    }

    /**
     * Send JSON response
     *
     * @param $response  any type except a resource.
     */


    public function sendAjaxResponse($response)
    {

        die(json_encode($response, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP));

    }



}