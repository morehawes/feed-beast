<?php

class InMap_Inreach extends Joe_Class {

	private $request_endpoint = 'https://explore.garmin.com/feed/share/';
	
	private $request_data = [];
	private $cache_id = '';	

	private $request_string = '';
	private $response_string = '';
	
	private $KML = null;
	private $FeatureCollection = [];
	
	function __construct($params_in = null) {
		//Set parameters
		$this->parameters = [
			'mapshare_identifier' => null,
			'mapshare_password' => null,
			'mapshare_date_start' => null,
			'mapshare_date_end' => null,									
		];
					
		parent::__construct($params_in);

		$this->setup_request();
		$this->execute_request();		
		$this->process_kml();		
		$this->build_geojson();
	}
	
	function execute_request() {
		//Request is setup
		if($this->cache_id) {
			//Cached response	
			$this->response_string = Joe_Cache::get_item($this->cache_id);

			if($this->response_string === false) {
				//Setup call
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $this->request_string);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

				if($auth_password = $this->get_parameter('mapshare_password')) {
					curl_setopt($ch, CURLOPT_USERPWD, ":" . $auth_password);	//No username			
				}

				//Run it
				curl_exec($ch);

				//cURL success?
				if(! curl_errno($ch)) {
					$this->response_string = curl_multi_getcontent($ch);

					//MUST BE VALID KML to go into Cache
					if(is_string($this->response_string) && simplexml_load_string($this->response_string)) {
						//Insert into cache
						Joe_Cache::set_item($this->cache_id, $this->response_string, 15);	//Minutes
					}

					curl_close($ch);
				}
			}	
		}
	}

	function setup_request() {
		//Required
		$url_identifier = $this->get_parameter('mapshare_identifier');
				
		if(! $url_identifier) {
			return false;		
		}

		//Start building the request
		$this->request_string = $this->request_endpoint . $url_identifier;

		//Start date
		if($data_start = $this->get_parameter('mapshare_date_start')) {
			$this->request_data['d1'] = $this->get_parameter('mapshare_date_start');
		}

		//End date
		if($data_end = $this->get_parameter('mapshare_date_end')) {
			$this->request_data['d2'] = $this->get_parameter('mapshare_date_end');
		}
		
		//Append data
		if(sizeof($this->request_data)) {
			$this->request_string .= '?';
			$this->request_string .= http_build_query($this->request_data);
		}	

		//Determine cache ID
		$this->cache_id = Joe_Helper::slug_prefix(md5($this->request_string));
		
		return true;
	}	

	function get_geojson($response_type = 'string') {
		if($response_type == 'string') {
			return json_encode($this->FeatureCollection);
		}
		
		return $this->FeatureCollection;		
	}

	function process_kml() {
		//Do we have a response?
		if($this->response_string) {		
			$this->KML = simplexml_load_string($this->response_string);
		}		
	}
	
	function build_geojson() {
		$this->FeatureCollection = [
			'type' => 'FeatureCollection',
			'features' => []
		];
		
		//We have Placemarks
		if(isset($this->KML->Document->Folder->Placemark) && sizeof($this->KML->Document->Folder->Placemark)) {
			//Each Placemark
			for($i = 0; $i < sizeof($this->KML->Document->Folder->Placemark); $i++) {
				$Placemark = $this->KML->Document->Folder->Placemark[$i];
				
				//Create Feature
				$Feature = [
					'type' => 'Feature',
					'properties' => [],
					'geometry' => []
				];
				
				//Extended Data?
				if(isset($Placemark->ExtendedData)) {
					if(sizeof($Placemark->ExtendedData->Data)) {
						$extended_data = [];
						
						//Each
						for($j = 0; $j < sizeof($Placemark->ExtendedData->Data); $j++) {
							$key = (string)$Placemark->ExtendedData->Data[$j]->attributes()->name;
							
							//Must be a key we are interested in
							if(in_array($key, Joe_Config::get_item('kml_data_include'))) {
								$value = (string)$Placemark->ExtendedData->Data[$j]->value;

								//By Key
								switch($key) {
									case 'Id' :
										$Feature['properties']['id'] = $value;

										break;
								}
						
								//Store
								$extended_data[$key] = $value;																
							}								
						}
						
						//We have data														
						if(sizeof($extended_data)) {
							
							$Feature['properties']['description'] = Joe_Helper::assoc_array_table($extended_data);
						}
					}
				}
									
				// =========== Point ===========
				
				if($Placemark->Point->coordinates) {
					$coordinates = explode(',', (String)$Placemark->Point->coordinates);													
					
					//Invalid
					if(sizeof($coordinates) < 2 || sizeof($coordinates) > 3) {
						continue;						
					}
					
					$Feature['geometry']['type'] = 'Point';
					$Feature['geometry']['coordinates'] = $coordinates;

					//Style
					$Feature['properties']['icon'] = [
						'className' => 'inmap-point',
						'iconSize' => [ 7, 7 ],
						'html' => '<span></span>'
					];						
					
					//Valid GPS
					if(isset($extended_data['Valid GPS Fix']) && 'True' === $extended_data['Valid GPS Fix']) {
						$Feature['properties']['icon']['className'] .= ' inmap-icon-gps';
					}
					
					//By event
					if(isset($extended_data['Event'])) {
						//Remove periods!
						$extended_data['Event'] = trim($extended_data['Event'], '.');

						switch($extended_data['Event']) {
							case 'Tracking turned on from device' :
							case 'Tracking turned off from device' :
							case 'Tracking interval received' :
							case 'Tracking message received' :

								break;
							case 'Msg to shared map received' :
								$Feature['properties']['icon']['className'] .= ' inmap-icon-message inmap-icon-custom';
								$Feature['properties']['icon']['html'] = Joe_Config::get_setting('map', 'styles', 'message_icon');
			
								break;
							case 'Quick Text to MapShare received' :
								$Feature['properties']['icon']['className'] .= ' inmap-icon-message inmap-icon-quick';
								$Feature['properties']['icon']['html'] = 'Q';
								
								break;
// 							default :
//  								Joe_Helper::debug($extended_data);
// 							
// 								break;									
						}										
					}

// 					$Feature['properties']['icon']['html'] = Joe_Config::get_setting('map', 'styles', 'message_icon');
					
					//When
					if(isset($Placemark->TimeStamp->when)) {
						$title = (String)$Placemark->TimeStamp->when;
						$title = str_replace([
							'T',
							'Z'
						],
						[
							' ',
							' (UTC) [#' . $i . ']'
						], $title);
						
						$Feature['properties']['title'] = $title;
					}

				// =========== LineString ===========
				
				} elseif($Placemark->LineString->coordinates) {
					$coordinates = (string)$Placemark->LineString->coordinates;
					$coordinates = preg_split('/\r\n|\r|\n/', $coordinates);
					
					//Valid array
					if(sizeof($coordinates)) {

						$Feature['geometry']['type'] = 'LineString';

						//Each Coordinate
						foreach($coordinates as $point) {
							$coords = explode(',', $point);													
					
							//Invalid
							if(sizeof($coords) < 2 || sizeof($coords) > 3) {
								continue;						
							}	

							$Feature['geometry']['coordinates'][] = $coords;
						}
					}
				}
				
				//Style
				$Feature['properties']['style']['color'] = Joe_Config::get_setting('map', 'styles', 'tracking_colour');
				
				$this->FeatureCollection['features'][] = $Feature;
			}
		}
	}
}