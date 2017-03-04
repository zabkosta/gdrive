<?php
/**
 *
 *
 * @link http://gdrive.unima.com.ua/
 * @author Zablotskyi kostiantyn
 * @license GNU GPLv3 https://www.gnu.org/licenses/gpl-3.0.html
 *
 *
 *
 */

namespace GDrive;

use Google_Client;
use Google_Service_Drive;

trait GClient
{

public function CreateClient(){


    $client = new Google_Client();
    $client->setApplicationName('GDrive manager');
    $client->setAuthConfig(realpath(__DIR__ . '/..').'/Config/client_secret.json');
    $client->setAccessType("offline");        // offline access
    $client->setApprovalPrompt('force');
    $client->setIncludeGrantedScopes(true);   // incremental auth
    $client->addScope(Google_Service_Drive::DRIVE);
    $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/auth');



    return  $client;


}





}