<?php
class ControllerModuleBrands extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('module/brands');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module');;

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_extension_module->addModule('brands', $this->request->post);
			} else {
				$this->model_extension_module->editModule($this->request->get['module_id'], $this->request->post);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');
		
		$aText = array('edit', 'enabled', 'disabled', 'div', 'link', 'basic', 'self', 'modal', 'placement', 
					'trigger', 'top', 'bottom', 'left', 'right', 'auto', 'click', 'hover', 'focus', 'show', 'hide',
					'breadcrumbs', 'list', 'grid'
				);
		foreach($aText as $a){
			$data['text_'.$a] = $this->language->get('text_'.$a);
		}
		
		$aEntry = array('name', 'status', 'type', 'view', 'title', 'class', 'popup', 'click', 'hover', 'focus', 'visible');
		foreach($aEntry as $a){
			$data['entry_'.$a] = $this->language->get('entry_'.$a);
		}
		
		$aHelp = array('results', 'popup', 'play', 'position', 'visible', 'iview', 'opacity');
		foreach($aHelp as $a){
			$data['help_'.$a] = $this->language->get('help_'.$a);
		}
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('module/brands', 'token=' . $this->session->data['token'], 'SSL')
		);

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('module/brands', 'token=' . $this->session->data['token'], 'SSL');
		} else {
			$data['action'] = $this->url->link('module/brands', 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], 'SSL');
		}

		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_extension_module->getModule($this->request->get['module_id']);
		}
		
		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}
		
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = 0;
		}
		
		if (isset($this->request->post['type'])) {
			$data['type'] = $this->request->post['type'];
		} elseif (!empty($module_info)) {
			$data['type'] = $module_info['type'];
		} else {
			$data['type'] = '';
		}
		
		if (isset($this->request->post['view'])) {
			$data['view'] = $this->request->post['view'];
		} elseif (!empty($module_info)) {
			$data['view'] = $module_info['view'];
		} else {
			$data['view'] = '';
		}
		
		if (isset($this->request->post['title'])) {
			$data['title'] = $this->request->post['title'];
		} elseif (!empty($module_info)) {
			$data['title'] = $module_info['title'];
		} else {
			$data['title'] = '';
		}
		
		if (isset($this->request->post['class'])) {
			$data['class'] = $this->request->post['class'];
		} elseif (!empty($module_info)) {
			$data['class'] = $module_info['class'];
		} else {
			$data['class'] = '';
		}
		
		if (isset($this->request->post['breadcrumbs'])) {
			$data['bcrumbs'] = $this->request->post['breadcrumbs'];
		} elseif (!empty($module_info)) {
			$data['bcrumbs'] = $module_info['breadcrumbs'];
		} else {
			$data['bcrumbs'] = 0;
		}
		
		if (isset($this->request->post['iview'])) {
			$data['iview'] = $this->request->post['iview'];
		} elseif (!empty($module_info)) {
			$data['iview'] = $module_info['iview'];
		} else {
			$data['iview'] = '';
		}
		
		if (isset($this->request->post['opacity'])) {
			$data['opacity'] = $this->request->post['opacity'];
		} elseif (!empty($module_info)) {
			$data['opacity'] = $module_info['opacity'];
		} else {
			$data['opacity'] = '';
		}
		
		if (isset($this->request->post['popup'])) {
			$data['popup'] = $this->request->post['popup'];
		} elseif (!empty($module_info)) {
			$data['popup'] = $module_info['popup'];
		} else {
			$data['popup'] = array("status"=>0, "placement"=>"", "trigger" => "", "class" => "");
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('module/brands.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/brands')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}
		
		return !$this->error;
	}
}
