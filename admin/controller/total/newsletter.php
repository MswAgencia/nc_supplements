<?php
class ControllerTotalNewsletter extends Controller {
	private $error = array(); 
	 
	public function index() { 
		$this->load->language('total/newsletter');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('newsletter', $this->request->post);
		
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->response->redirect($this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_edit'] = $this->language->get('text_edit');
		
		$data['entry_type'] = $this->language->get('entry_type');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_discount'] = $this->language->get('entry_discount');

		$data['text_percentual'] = $this->language->get('text_percentual');
		$data['text_static'] = $this->language->get('text_static');
					
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

   		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_total'),
			'href'      => $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('total/newsletter', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$data['action'] = $this->url->link('total/newsletter', 'token=' . $this->session->data['token'], 'SSL');
		
		$data['cancel'] = $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['newsletter_type'])) {
			$data['newsletter_type'] = $this->request->post['newsletter_type'];
		} else {
			$data['newsletter_type'] = $this->config->get('newsletter_type');
		}

		if (isset($this->request->post['newsletter_status'])) {
			$data['newsletter_status'] = $this->request->post['newsletter_status'];
		} else {
			$data['newsletter_status'] = $this->config->get('newsletter_status');
		}

		if (isset($this->request->post['newsletter_sort_order'])) {
			$data['newsletter_sort_order'] = $this->request->post['newsletter_sort_order'];
		} else {
			$data['newsletter_sort_order'] = $this->config->get('newsletter_sort_order');
		}

		if (isset($this->request->post['newsletter_discount'])) {
			$data['newsletter_discount'] = $this->request->post['newsletter_discount'];
		} else {
			$data['newsletter_discount'] = $this->config->get('newsletter_discount');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('total/newsletter.tpl', $data));

	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'total/newsletter')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		return !$this->error;
	}
}
?>