<?php 
class ControllerCommonHeader extends Controller {
	public $data = array();
	public function index() {
		$this->data['title'] = $this->document->getTitle(); 
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$this->data['base'] = HTTPS_SERVER;
		} else {
			$this->data['base'] = HTTP_SERVER;
		}

		$this->data['description'] = $this->document->getDescription();
		$this->data['keywords'] = $this->document->getKeywords();
		$this->data['links'] = $this->document->getLinks();	
		$this->data['styles'] = $this->document->getStyles();
		$this->data['scripts'] = $this->document->getScripts();
		$this->data['lang'] = $this->language->get('code');
		$this->data['direction'] = $this->language->get('direction');

		$this->language->load('common/header');

		$this->data['heading_title'] = $this->language->get('heading_title');

		
		$this->data['text_dashboard'] = $this->language->get('text_dashboard');
		$this->data['text_help'] = $this->language->get('text_help');
		$this->data['text_information'] = $this->language->get('text_information');
		$this->data['text_language'] = $this->language->get('text_language');
		$this->data['text_layout'] = $this->language->get('text_layout');
		$this->data['text_localisation'] = $this->language->get('text_localisation');
		$this->data['text_logout'] = $this->language->get('text_logout');
		$this->data['text_contact'] = $this->language->get('text_contact');
		$this->data['text_manager'] = $this->language->get('text_manager');
		$this->data['text_support'] = $this->language->get('text_support');
		$this->data['text_pos'] = $this->language->get('text_pos');
		
		if (!$this->user->isLogged() || !isset($this->request->get['token']) || !isset($this->session->data['postoken']) || ($this->request->get['token'] != $this->session->data['postoken'])) {
			
			
			
			$this->data['logged'] = '';

			$this->data['home'] = $this->url->link('common/login', '', 'SSL');
		} else {
			$this->data['logged'] = sprintf($this->language->get('text_logged'), $this->user->getUserName());
			$this->data['pp_express_status'] = $this->config->get('pp_express_status');

			$this->data['home'] = $this->url->link('common/home', 'token=' . $this->session->data['postoken'], 'SSL');
			$this->data['pos'] = $this->url->link('common/pos', 'token=' . $this->session->data['postoken'], 'SSL');
			$this->data['transactions'] = $this->url->link('common/transactions', 'token=' . $this->session->data['postoken'], 'SSL');
			$this->data['refund'] = $this->url->link('common/refund', 'token=' . $this->session->data['postoken'], 'SSL');
				
			$this->data['logout'] = $this->url->link('common/logout', 'token=' . $this->session->data['postoken'], 'SSL');
				
			
			$this->data['stores'] = array();

			$this->load->model('setting/store');

			$results = $this->model_setting_store->getStores();

			foreach ($results as $result) {
				$this->data['stores'][] = array(
					'name' => $result['name'],
					'href' => $result['url']
				);
			}			
		}

		return $this->load->view('common/header.tpl', $this->data);
	}
}
?>