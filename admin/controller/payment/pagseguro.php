<?php 
class ControllerPaymentPagseguro extends Controller {
 	private $error = array(); 
	
	public function index() {
		$this->load->language('payment/pagseguro');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('pagseguro', $this->request->post);				
		  	
			$this->session->data['success'] = $this->language->get('text_success');
		  	
			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));			
		}
		
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_frete_loja'] = $this->language->get('text_frete_loja');
		$data['text_frete_pagseguro_pac'] = $this->language->get('text_frete_pagseguro_pac');
		$data['text_frete_pagseguro_sedex'] = $this->language->get('text_frete_pagseguro_sedex');
		$data['text_frete_pagseguro_nao_especificado'] = $this->language->get('text_frete_pagseguro_nao_especificado');
		
		$data['entry_token'] = $this->language->get('entry_token');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_posfixo'] = $this->language->get('entry_posfixo');
		$data['entry_order_status'] = $this->language->get('entry_order_status');		
		$data['entry_order_aguardando_retorno'] = $this->language->get('entry_order_aguardando_retorno');
		$data['entry_order_aguardando_pagamento'] = $this->language->get('entry_order_aguardando_pagamento');
		$data['entry_order_analise'] = $this->language->get('entry_order_analise');
		$data['entry_order_paga'] = $this->language->get('entry_order_paga');
		$data['entry_order_disponivel'] = $this->language->get('entry_order_disponivel');
		$data['entry_order_disputa'] = $this->language->get('entry_order_disputa');
		$data['entry_order_devolvida'] = $this->language->get('entry_order_devolvida');
		$data['entry_order_cancelada'] = $this->language->get('entry_order_cancelada');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');	
		$data['entry_update_status_alert'] = $this->language->get('entry_update_status_alert');
		$data['entry_tipo_frete'] = $this->language->get('entry_tipo_frete');
		$data['entry_total'] = $this->language->get('entry_total');
		
		$data['help_token'] = $this->language->get('help_token');
		$data['help_email'] = $this->language->get('help_email');
		$data['help_posfixo'] = $this->language->get('help_posfixo');
		$data['help_tipo_frete'] = $this->language->get('help_tipo_frete');
		$data['help_order_aguardando_pagamento'] = $this->language->get('help_order_aguardando_pagamento');
		$data['help_order_analise'] = $this->language->get('help_order_analise');
		$data['help_order_paga'] = $this->language->get('help_order_paga');
		$data['help_order_disputa'] = $this->language->get('help_order_disputa');
		$data['help_order_devolvida'] = $this->language->get('help_order_devolvida');
		$data['help_order_cancelada'] = $this->language->get('help_order_cancelada');
		$data['help_update_status_alert'] = $this->language->get('help_update_status_alert');
		$data['help_total'] = $this->language->get('help_total');		
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		
		if (isset($this->error['warning'])) {
		  $data['error_warning'] = $this->error['warning'];
		} else {
		  $data['error_warning'] = '';
		}
					
		if (isset($this->error['token'])) {
		  $data['error_token'] = $this->error['token'];
		} else {
		  $data['error_token'] = '';
		}
		
		if (isset($this->error['email'])) {
		  $data['error_email'] = $this->error['email'];
		} else {
		  $data['error_email'] = '';
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('payment/pagseguro', 'token=' . $this->session->data['token'], 'SSL')
		);		
		
		$data['action'] = $this->url->link('payment/pagseguro', 'token=' . $this->session->data['token'], 'SSL');
			
		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset($this->request->post['pagseguro_token'])) {
		  $data['pagseguro_token'] = $this->request->post['pagseguro_token'];
		} else {
		  $data['pagseguro_token'] = $this->config->get('pagseguro_token');
		}
			
		if (isset($this->request->post['pagseguro_email'])) {
		  $data['pagseguro_email'] = $this->request->post['pagseguro_email'];
		} else {
		  $data['pagseguro_email'] = $this->config->get('pagseguro_email');
		}

		if (isset($this->request->post['pagseguro_posfixo'])) {
			$data['pagseguro_posfixo'] = $this->request->post['pagseguro_posfixo'];
		} else {
			$data['pagseguro_posfixo'] = $this->config->get('pagseguro_posfixo');
		}
		
		if (isset($this->request->post['pagseguro_total'])) {
			$data['pagseguro_total'] = $this->request->post['pagseguro_total'];
		} else {
			$data['pagseguro_total'] = $this->config->get('pagseguro_total'); 
		}		
		
		if (isset($this->request->post['pagseguro_tipo_frete'])) {
			$data['pagseguro_tipo_frete'] = $this->request->post['pagseguro_tipo_frete'];
		} else {
			$data['pagseguro_tipo_frete'] = $this->config->get('pagseguro_tipo_frete');
		}		

		if (isset($this->request->post['pagseguro_update_status_alert'])) {
			$data['pagseguro_update_status_alert'] = $this->request->post['pagseguro_update_status_alert'];
		} else {
			$data['pagseguro_update_status_alert'] = $this->config->get('pagseguro_update_status_alert');
		}		
		
		if (isset($this->request->post['pagseguro_order_aguardando_retorno'])) {
		  $data['pagseguro_order_aguardando_retorno'] = $this->request->post['pagseguro_order_aguardando_retorno'];
		} else {
		  $data['pagseguro_order_aguardando_retorno'] = $this->config->get('pagseguro_order_aguardando_retorno'); 
		}    
		
		if (isset($this->request->post['pagseguro_order_aguardando_pagamento'])) {
		  $data['pagseguro_order_aguardando_pagamento'] = $this->request->post['pagseguro_order_aguardando_pagamento'];
		} else {
		  $data['pagseguro_order_aguardando_pagamento'] = $this->config->get('pagseguro_order_aguardando_pagamento'); 
		}
			
		if (isset($this->request->post['pagseguro_order_analise'])) {
		  $data['pagseguro_order_analise'] = $this->request->post['pagseguro_order_analise'];
		} else {
		  $data['pagseguro_order_analise'] = $this->config->get('pagseguro_order_analise'); 
		}
			
		if (isset($this->request->post['pagseguro_order_paga'])) {
		  $data['pagseguro_order_paga'] = $this->request->post['pagseguro_order_paga'];
		} else {
		  $data['pagseguro_order_paga'] = $this->config->get('pagseguro_order_paga'); 
		}
		
		if (isset($this->request->post['pagseguro_order_disponivel'])) {
		  $data['pagseguro_order_disponivel'] = $this->request->post['pagseguro_order_disponivel'];
		} else {
		  $data['pagseguro_order_disponivel'] = $this->config->get('pagseguro_order_disponivel'); 
		}    
		
		if (isset($this->request->post['pagseguro_order_disputa'])) {
		  $data['pagseguro_order_disputa'] = $this->request->post['pagseguro_order_disputa'];
		} else {
		  $data['pagseguro_order_disputa'] = $this->config->get('pagseguro_order_disputa'); 
		}
			
		if (isset($this->request->post['pagseguro_order_devolvida'])) {
		  $data['pagseguro_order_devolvida'] = $this->request->post['pagseguro_order_devolvida'];
		} else {
		  $data['pagseguro_order_devolvida'] = $this->config->get('pagseguro_order_devolvida'); 
		}
			
		if (isset($this->request->post['pagseguro_order_cancelada'])) {
		  $data['pagseguro_order_cancelada'] = $this->request->post['pagseguro_order_cancelada'];
		} else {
		  $data['pagseguro_order_cancelada'] = $this->config->get('pagseguro_order_cancelada'); 
		}		
		
		if (isset($this->request->post['pagseguro_order_nao_efetivado'])) {
			$data['pagseguro_order_nao_efetivado'] = $this->request->post['pagseguro_order_nao_efetivado'];
		} else {
			$data['pagseguro_order_nao_efetivado'] = $this->config->get('pagseguro_order_nao_efetivado');
		}    
		
		if (isset($this->request->post['pagseguro_order_status_id'])) {
		  $data['pagseguro_order_status_id'] = $this->request->post['pagseguro_order_status_id'];
		} else {
		  $data['pagseguro_order_status_id'] = $this->config->get('pagseguro_order_status_id'); 
		}
		
		$this->load->model('localisation/order_status');
		
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['pagseguro_geo_zone_id'])) {
		  $data['pagseguro_geo_zone_id'] = $this->request->post['pagseguro_geo_zone_id'];
		} else {
		  $data['pagseguro_geo_zone_id'] = $this->config->get('pagseguro_geo_zone_id'); 
		} 
		
		$this->load->model('localisation/geo_zone');
		
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['pagseguro_status'])) {
		  $data['pagseguro_status'] = $this->request->post['pagseguro_status'];
		} else {
		  $data['pagseguro_status'] = $this->config->get('pagseguro_status');
		}
		
		if (isset($this->request->post['pagseguro_sort_order'])) {
		  $data['pagseguro_sort_order'] = $this->request->post['pagseguro_sort_order'];
		} else {
		  $data['pagseguro_sort_order'] = $this->config->get('pagseguro_sort_order');
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/pagseguro.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/pagseguro')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['pagseguro_token']) {
		  $this->error['token'] = $this->language->get('error_token');
		}
		
		if (!$this->request->post['pagseguro_email']) {
		  $this->error['email'] = $this->language->get('error_email');
		}

		return !$this->error;
	}
}
?>