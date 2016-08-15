<?php
		
	abstract class Controller{

		function __construct(){
		}
		
		function __before($params,$action){}
		function __after($params,$action){}

		static function respond($array){
			if (isset($array['success']) && $array['success'] === false && !isset($array['status_code'])) {
				http_response_code(500);
			} 
			else if (isset($array['status_code'])) {
				header('Content-Type: application/json');
				http_response_code($array['status_code']);
				echo json_encode($array);
			} 
			else {
				header('Content-Type: application/json');
				http_response_code(200);
				echo json_encode($array);
			}
		}

		static function redirect($route){
			header("Location: ".$route);
		}

		function partial($name){
			include __DIR__."/../views/partials/".$name.".html";
		}

		function render_layout($view = ""){
			$this->view = $view;
			include __DIR__."/../views/layouts/".$this->layout.".html";
		}

		function render_view(){
			if(strlen($this->view) > 0) {
				$ctrl = explode("#",$this->view)[0];
				$view = explode("#",$this->view)[1];
				include __DIR__."/../views/$ctrl/$view.html";
			}
		}

	}

?>
