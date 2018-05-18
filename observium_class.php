<?php

namespace WHMCS\Module\Addon\Bandwidth;

	class Observium_class {
		
		public $observium_url;
		public $observium_apiUsername;
		public $observium_apiPassword;
		
		public function __construct() {
			//echo 1235;
			
		}
		
		public function init($vars) {
			
			$this->set_observium_url($vars['observiumURL']);
			$this->set_observium_username($vars['apiUsername']); 	
			$this->set_observium_passord($vars['apiPassword']); 	
			//return 'Hello';
		}
		
		public function set_observium_url($observiumURL) {
			
			$this->observium_url = $observiumURL; 	
			
		}
	
		public function set_observium_username($apiUsername) {
			
			$this->observium_apiUsername = $apiUsername; 	
		}	

		public function set_observium_passord($apiPassword) {
			
			$this->observium_apiPassword = $apiPassword; 	
		}
		
		
		public function request($params = []) {
			
				$ch = curl_init();
				//echo $this->observium_url.'/api/v0/'.$params['cmd'];
				
				curl_setopt($ch, CURLOPT_URL, $this->observium_url.'/api/v0/'.$params['cmd']);
				curl_setopt($ch, CURLOPT_USERPWD, $this->observium_apiUsername.":".$this->observium_apiPassword);

				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				
				$data = curl_exec($ch);
				$info = curl_getinfo($ch);
				curl_close($ch);
			
						
				return json_decode($data);

		}
		
				
	}
