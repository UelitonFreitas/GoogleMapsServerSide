<?php
	class GoogleMap{
		
		public $mode;
		public $lang;
		public $key;
		public $origins;
		public $destinations;

		function __construct(){
			$this->mode = '&mode=driver';
			$this->lang = '&language=pt-BR';
			$this->key = '&key=AIzaSyDGfI3HttGm4URxFt4V1wbggshLgk_bet8';
			$this->origins;
			$this->destinations = '&destinations=';
		}

		public function setDestines($destines){

			$numberOfDestines = count($destines);

			for ($i=0; $i < $numberOfDestines -1 ; $i++) { 
				$this->destinations = $this->destinations.$destines[$i]."|";
			}
			$this->destinations = $this->destinations.$destines[$numberOfDestines-1];
		}

		public function setOrigins($lat, $lng){
			$this->origins = 'origins='.$lat.','.$lng;
		}

		public function pegaDistanciasDoGoogle(){

			$request = "https://maps.googleapis.com/maps/api/distancematrix/json?".$this->origins.$this->destinations.$this->mode.$this->lang.$this->key;
			
			$HttpSocket = new HttpSocket();
			$response = $HttpSocket->get($request);
			return json_decode($response->body,true)['rows'][0]['elements'];
		}

		public function geocoderReverse($lat, $lng){
			$request = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$lat.",".$lng.$this->key;
			
			$HttpSocket = new HttpSocket();
			$response = $HttpSocket->get($request);
			$geocoder = json_decode($response->body,true)['results'][0]['address_components'];

			//Se a lat e lgn pertence a um local especifico
			if(count($geocoder) > 7){
				return array(
						'lat' => $lat,
						'lng' => $lng,
						'cidade' => $geocoder[4]['long_name'], 
						'estado'=> $geocoder[5]['long_name'],
						'pais'	=> 	$geocoder[6]['long_name'],
						'cep' => $geocoder[7]['long_name']
				);
			}
			//Senao pertence ao um local generico. Uma cidade enteira, por exemplo.
			else {
				return array(
						'lat' => $lat,
						'lng' => $lng,
						'cidade' => $geocoder[3]['long_name'], 
						'estado'=> $geocoder[4]['long_name'],
						'pais'	=> 	$geocoder[5]['long_name'],
						'cep' => $geocoder[6]['long_name']
				);
			}
		}
	}
?>