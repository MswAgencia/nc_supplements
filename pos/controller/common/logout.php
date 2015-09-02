<?php       
class ControllerCommonLogout extends Controller {   
	public function index() { 
		//$this->user->logout();
		$this->cache->delete('pos.'.$this->session->data['postoken']);
		unset($this->session->data['postoken']);
		unset($this->session->data['store_id']);
		$this->response->redirect($this->url->link('common/login', '', 'SSL'));
	}
}  
?>