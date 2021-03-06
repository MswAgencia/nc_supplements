<?php  
class ControllerCommonLogin extends Controller { 
	private $error = array();
	public $data = array();

	public function index() { 
		$this->language->load('common/login');
        $this->language->load('module/openpos');
		$this->document->setTitle($this->language->get('heading_title'));
		
		if ($this->user->isLogged() && isset($this->request->get['token'])&& isset($this->session->data['postoken'])  && ($this->request->get['token'] == $this->session->data['postoken'])) {
			$this->response->redirect($this->url->link('common/pos', 'token=' . $this->session->data['postoken'], 'SSL'));
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) { 
			
			$this->session->data['postoken'] = md5(mt_rand());
			$this->session->data['store_id'] = $this->request->post['store_id'];
			$tmp = array(
					'user_id' => $this->user->getId(),
					'store_id' => $this->session->data['store_id']
			);
			$this->cache->set('pos.'.$this->session->data['postoken'],$tmp);
			if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], HTTP_SERVER) === 0 || strpos($this->request->post['redirect'], HTTPS_SERVER) === 0 )) {
				$this->response->redirect($this->request->post['redirect'] . '&token=' . $this->session->data['postoken']);
			} else {
				
				$this->response->redirect($this->url->link('common/pos', 'token=' . $this->session->data['postoken'], 'SSL'));
			}
		}
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_login'] = $this->language->get('text_login');
		$this->data['text_forgotten'] = $this->language->get('text_forgotten');

		$this->data['entry_username'] = $this->language->get('entry_username');
		$this->data['entry_password'] = $this->language->get('entry_password');
		$this->data['entry_storelogin'] = $this->language->get('entry_storelogin');

		$this->data['button_login'] = $this->language->get('button_login');

		if ((isset($this->session->data['postoken']) && !isset($this->request->get['token'])) || ((isset($this->request->get['token']) && (isset($this->session->data['postoken']) && ($this->request->get['token'] != $this->session->data['postoken']))))) {
			$this->error['warning'] = $this->language->get('error_token');
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->data['action'] = $this->url->link('common/login', '', 'SSL');

		if (isset($this->request->post['username'])) {
			$this->data['username'] = $this->request->post['username'];
		} else {
			$this->data['username'] = '';
		}

		if (isset($this->request->post['password'])) {
			$this->data['password'] = $this->request->post['password'];
		} else {
			$this->data['password'] = '';
		}

		if (isset($this->request->get['route'])) {
			$route = $this->request->get['route'];

			unset($this->request->get['route']);

			if (isset($this->request->get['token'])) {
				unset($this->request->get['token']);
			}

			$url = '';

			if ($this->request->get) {
				$url .= http_build_query($this->request->get);
			}

			$this->data['redirect'] = $this->url->link($route, $url, 'SSL');
		} else {
			$this->data['redirect'] = '';	
		}

		if ($this->config->get('config_password')) {
			$this->data['forgotten'] = $this->url->link('common/forgotten', '', 'SSL');
		} else {
			$this->data['forgotten'] = '';
		}
		
		$this->load->model('setting/store');
		$this->data['stores'] = array();

		$this->data['stores'][] = array(
				'store_id' => 0,
				'name'     => $this->config->get('config_name') . $this->language->get('text_default')
		);
		$results = $this->model_setting_store->getStores();
		
		foreach ($results as $result) {
		
			$this->data['stores'][] = array(
					'store_id' => $result['store_id'],
					'name'     => $result['name']
			);
		}

		$this->data['header'] = $this->load->controller('common/header');
		$this->data['footer'] = $this->load->controller('common/footer');
		

		$this->response->setOutput($this->load->view('common/login.tpl', $this->data));
	}

	protected function validate() {
		$this->load->model('setting/setting');
		$config = $this->model_setting_setting->getSetting('openpos',(int)$this->request->post['store_id']);
		if(!isset($config['openpos_config_pos_complete_status_id']))
		{
			$this->error['warning'] = 'Your OpenPos not install yet or incorrect setting. Please goto admin install and setting it.';
		}

		if(empty($this->error))
		{

			if (!isset($this->request->post['username']) || !isset($this->request->post['password']) || !$this->user->login($this->request->post['username'], $this->request->post['password'])) {
				$this->error['warning'] = $this->language->get('error_login');
			}
		}
		return !$this->error;
	}
	
	public function check() {
		$route = '';
	
		if (isset($this->request->get['route'])) {
			$part = explode('/', $this->request->get['route']);
	
			if (isset($part[0])) {
				$route .= $part[0];
			}
	
			if (isset($part[1])) {
				$route .= '/' . $part[1];
			}
		}
	
		$ignore = array(
				'common/login',
				'common/forgotten',
				'common/reset'
		);
	
		if (!$this->user->isLogged() && !in_array($route, $ignore)) {
			return new Action('common/login');
		}
		
		if (isset($this->request->get['route'])) {
			$ignore = array(
					'common/login',
					'common/logout',
					'common/forgotten',
					'common/reset',
					'error/not_found',
					'error/permission'
			);
	
			$config_ignore = array();
	
			if ($this->config->get('config_token_ignore')) {
				$config_ignore = unserialize($this->config->get('config_token_ignore'));
			}
	
			$ignore = array_merge($ignore, $config_ignore);
	
			if (!in_array($route, $ignore) && (!isset($this->request->get['token']) || !isset($this->session->data['postoken']) || ($this->request->get['token'] != $this->session->data['postoken']))) {
				return new Action('common/login');
			}
		} else {
			if (!isset($this->request->get['token']) || !isset($this->session->data['postoken']) || ($this->request->get['token'] != $this->session->data['postoken'])) {
				return new Action('common/login');
			}
		}
		
	}
}  
?>