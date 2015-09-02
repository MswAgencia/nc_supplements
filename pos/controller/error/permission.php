<?php    
class ControllerErrorPermission extends Controller {   
	public $data = array(); 
	public function index() { 
		$this->language->load('error/permission');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_permission'] = $this->language->get('text_permission');

		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['postoken'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('error/permission', 'token=' . $this->session->data['postoken'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data['header'] = $this->load->controller('common/header');
		$this->data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('error/permission.tpl', $this->data));
		
	}
	
	public function check() {
		if (isset($this->request->get['route'])) {
			$route = '';
	
			$part = explode('/', $this->request->get['route']);
	
			if (isset($part[0])) {
				$route .= $part[0];
			}
	
			if (isset($part[1])) {
				$route .= '/' . $part[1];
			}
	
			$ignore = array(
					'common/dashboard',
					'common/pos',
					'common/transactions',
					'common/refund',
					'common/login',
					'common/logout',
					'common/forgotten',
					'common/reset',
					'error/not_found',
					'error/permission'
			);
	
			if (!in_array($route, $ignore) && !$this->user->hasPermission('access', $route)) {
				return new Action('error/permission');
			}
		}
	}
}
?>