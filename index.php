<?php

/**
 * GDrive bootstrap file.
 *
 * @link http://gdrive.unima.com.ua/
 * @author Zablotskyi kostiantyn
 * @license GNU GPLv3 https://www.gnu.org/licenses/gpl-3.0.html
 * 
 * 
 * 
 */
namespace GDrive;

require __DIR__ . '/vendor/autoload.php';

date_default_timezone_set('Europe/Kiev');
//error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);

error_reporting(E_ALL);

$app = new Apps(Cfg::getInstance());

$app->start();

