<?php
/**
 * Singleton class for get config
 *
 * @link http://gdrive.unima.com.ua/
 * @author Zablotskyi kostiantyn
 * @license GNU GPLv3 https://www.gnu.org/licenses/gpl-3.0.html
 *
 *
 */

namespace GDrive;

final class Cfg
{
   // class instance
	private static $instance;

   // config array
	private static $config;



	private function __construct() {
		
		self::$config = require(__DIR__ . '/../Config/config.php');
		
			
	}

    // just gag
	private function __clone() {}


    /**
     * Call this method to get config singleton
     *
     * @return app config
     */
	
	public static function getInstance() {

	    if (self::$instance === null) {
      	
    		self::$instance = new self;
    		
    		
    	}
    		return self::$instance;
  		}


  		//

    /**
     * resolve config route
     *
     *
     * @param $path
     * @return config params ( array or string )
     */
    public function get($path)
  		{
  			if (isset($path)) {
  				$path   = explode('.', $path);
  				$result = self::$config;
  		
  				foreach ($path as $key) {
  					if (isset($result[$key])) {
  						$result = $result[$key];
  					}
  				}
  		
  				return $result;
  			}
  		
  	   }
  		

	 	
  
}