<?php
require_once(DIR_SYSTEM . 'library/pos/core.php');
class ControllerModuleOpenPos extends Controller {
	private $error = array();
	public $data = array();
	public $core;
	public function __construct($registry)
	{
		parent::__construct($registry);
		$this->load->model('setting/setting');
		$openposkey = $this->model_setting_setting->getSetting('openposkey');
		$key = isset($openposkey['openposkey'])?$openposkey['openposkey']:'';
		$this->core = new PosCore($this->url->link('module/openpos/active', 'token=' . $this->session->data['token'], 'SSL'),$key);
	}
	public function install()
	{
		$this->updateUpc();
		$sql = "
				CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "pos_payment` (
				  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
				  `payment_name` text NOT NULL,
				  `store_id` int(11) NOT NULL,
				  `is_cash` int(11) NOT NULL,
				  PRIMARY KEY (`payment_id`)
				)
				";
		$this->db->query($sql);
		
		$sql = "
				CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "pos_transaction` (
				  `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
				  `type` varchar(255) NOT NULL,
				  `store_id` int(11) NOT NULL,
				  `user_id` int(11) NOT NULL,
				  `money_in` decimal(15,4) NOT NULL,
				  `money_out` decimal(15,4) NOT NULL,
				  `comment` text NOT NULL,
				  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  PRIMARY KEY (`transaction_id`)
				) 
				";
		$this->db->query($sql);
		
		$sql = "
				CREATE TABLE IF NOT EXISTS `".DB_PREFIX."pos_product_meta` (
				  `product_id` int(11) NOT NULL,
				  `meta_key` varchar(255) NOT NULL,
				  `meta_value` text NOT NULL,
				  PRIMARY KEY (`product_id`,`meta_key`)
				)
				";
		$this->db->query($sql);
	}

    public function uninstall()
    {
        $this->core->uninstall();
    }

	private function updateUpc()
	{
		$sql = "SELECT * FROM  `" . DB_PREFIX . "product`";
		$query = $this->db->query($sql);
		foreach($query->rows as $row)
		{
			if($row['upc'] == '')
			{
				$num = sprintf("%011s", $row['product_id']);
				$upc = $this->core->createUPC($num);
				$sql = "UPDATE  `" . DB_PREFIX . "product` SET upc = '".$upc."' WHERE product_id = '".$row['product_id']."'";
				$this->db->query($sql);
			}else{
				if(!$this->core->validateUPC($row['upc']))
				{
					$num = sprintf("%011s", $row['product_id']);
					$upc = $this->core->createUPC($num);
					$sql = "UPDATE  `" . DB_PREFIX . "product` SET upc = '".$upc."' WHERE product_id = '".$row['product_id']."'";
					$this->db->query($sql);
				}
			}

		}
	}
	public function index()
	{
		$this->updateUpc();
		$this->language->load('module/openpos');
		$this->load->model('setting/setting');
		$this->document->setTitle($this->language->get('heading_title'));
		if($this->core->checkTrial())
		{
			$days = $this->core->getRemainDays();
			$this->error['warning'] = 'Your OpenPos will expire in '.$days.' days. <a href="http://magetop.com">Buy OpenPos</a> | <a href="'.$this->url->link('module/openpos/active', '&token=' . $this->session->data['token'], 'SSL').'">Enter Key</a>';
		}
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$post = $this->request->post;
			
			$this->model_setting_setting->editSetting('openposbarcode',$post );

			$this->session->data['success'] = $this->language->get('text_success');
			$this->data['success'] = $this->language->get('text_success');
			$this->redirect($this->url->link('module/openpos', 'token=' . $this->session->data['token'], 'SSL'));
		}
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['text_default'] = $this->language->get('text_default');
		
		$this->data['tab_general'] = $this->language->get('tab_general');
		$this->data['tab_store'] = $this->language->get('tab_store');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		
		$this->data['column_name'] = $this->language->get('column_name');
		$this->data['column_url'] = $this->language->get('column_url');
		$this->data['column_action'] = $this->language->get('column_action');
		
		$this->load->model('setting/store');
		$this->data['stores'] = array();
		$action = array();
		
		$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('module/openpos/setting', 'store_id=0&token=' . $this->session->data['token'], 'SSL')
		);
		
		$this->data['stores'][] = array(
				'store_id' => 0,
				'name'     => $this->config->get('config_name') . $this->language->get('text_default'),
				'url'      => HTTP_CATALOG,
				'selected' => isset($this->request->post['selected']) && in_array(0, $this->request->post['selected']),
				'action'   => $action
		);
		$results = $this->model_setting_store->getStores();
		
		foreach ($results as $result) {
			$action = array();
		
			$action[] = array(
					'text' => $this->language->get('text_edit'),
					'href' => $this->url->link('module/openpos/setting', 'store_id='.$result['store_id'].'&token=' . $this->session->data['token'] . '&store_id=' . $result['store_id'], 'SSL')
			);
		
			$this->data['stores'][] = array(
					'store_id' => $result['store_id'],
					'name'     => $result['name'],
					'url'      => $result['url'],
					'selected' => isset($this->request->post['selected']) && in_array($result['store_id'], $this->request->post['selected']),
					'action'   => $action
			);
		}
		
		$this->load->model('catalog/category');
		$this->data['categories'] = $this->model_catalog_category->getCategories(array());
		
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		$this->data['breadcrumbs'] = array();
		
		$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => false
		);
		
		$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_module'),
				'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => ' :: '
		);
		
		$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('module/openpos', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => ' :: '
		);
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$this->data['openpos'] = $this->request->post;
		} elseif ($this->model_setting_setting->getSetting('openposbarcode')) {
			
			$this->data['openpos'] = $this->model_setting_setting->getSetting('openposbarcode');
				
		}
		$this->data['token'] =  $this->session->data['token'];
		$this->data['action'] = $this->url->link('module/openpos', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['barcode'] = $this->url->link('module/openpos/product', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['transaction'] = $this->url->link('module/openpos/transaction', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['order'] = $this->url->link('module/openpos/orders', 'token=' . $this->session->data['token'], 'SSL');


		$this->template = 'module/openpos/openpos.tpl';
		$this->children = array(
				'common/header',
				'common/footer'
		);
		$this->data['header'] = $this->load->controller('common/header');
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view($this->template, $this->data));
		//$this->response->setOutput($this->render());
	}
	
	public function customerautocomplete()
	{
		$json = array();
		
		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
			$this->load->model('sale/customer');
			$data = array(
					'filter_email'             => $filter_email,
			);
			
			$customer_total = $this->model_sale_customer->getTotalCustomers($data);
			
			$results = $this->model_sale_customer->getCustomers($data);
			
			foreach ($results as $result) {
				$json[] = array(
						'customer_id' => $result['customer_id'],
						'email'        => $result['email']
				);
			}
		}
		
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function setting()
	{
		$this->language->load('module/openpos');
		$this->load->model('catalog/category');
		$this->load->model('setting/store');
		$this->load->model('setting/setting');
		$this->load->model('user/user_group');
		$this->load->model('openpos/pos_payment');
		$this->load->model('localisation/order_status');
		$this->data['store_id'] = $this->request->get['store_id'];
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$post = $this->request->post;
			$payment_name = $post['payment_name'];
			$is_cash = isset($post['is_cash'])?$post['is_cash']:0;
			unset($post['payment_name']);
			unset($post['is_cash']);
			unset($post['store_id']);
			
			$this->model_setting_setting->editSetting('openpos', $post,$this->request->post['store_id']);
		
			//update pos payment
			$pdata = array();
			foreach($payment_name as $key => $payment)
			{
				$pdata[] = array(
						'payment_name' => $payment,
						'store_id' => $this->request->post['store_id'],
						'is_cash' => isset($is_cash[$key])?'1':'0'
				);
				
			}
			$this->model_openpos_pos_payment->addPayment($this->request->post['store_id'],$pdata);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('module/openpos/setting', 'store_id='.$this->request->post['store_id'].'&token=' . $this->session->data['token'], 'SSL'));
		}
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['text_default'] = $this->language->get('text_default');
		
		$this->data['tab_general'] = $this->language->get('tab_general');
		$this->data['tab_store'] = $this->language->get('tab_store');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$this->data['openpos'] = $this->request->post;
		} elseif ($this->model_setting_setting->getSetting('openpos',$this->data['store_id'])) {
			$this->data['openpos'] = $this->model_setting_setting->getSetting('openpos',$this->data['store_id']);
		}
		
		$this->data['categories'] = $this->model_catalog_category->getCategories(array());
		$this->data['action'] = $this->url->link('module/openpos/setting', 'store_id='.$this->request->get['store_id'].'&token=' . $this->session->data['token'], 'SSL');
		$this->data['user_groups'] = $this->model_user_user_group->getUserGroups();
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		$this->load->model('localisation/tax_class');
		
		$this->data['payment'] = $this->model_openpos_pos_payment->getPayment($this->request->get['store_id']);
		
		$this->data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
		
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		$this->data['token'] =  $this->session->data['token'];
		$this->data['breadcrumbs'] = array();
		
		$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => false
		);
		
		$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_module'),
				'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => ' :: '
		);
		
		$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('module/openpos', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => ' :: '
		);
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
		$this->data['cancel'] = $this->url->link('module/openpos', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['header'] = $this->load->controller('common/header');
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('module/openpos/openpos_setting.tpl', $this->data));
	}
	
	private function validate()
	{
		return true;
	}
	
	public function transaction()
	{
		$this->language->load('module/openpos');
		$this->load->model('openpos/transaction');
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		$this->data['user_id'] = isset($this->request->get['user_id']) ? $this->request->get['user_id']:'all';
		$this->data['store_id'] = isset($this->request->get['store_id']) ? $this->request->get['store_id']:'all';
		$data = array(
				'start' => ($page - 1) * 10,
				'limit' => 10,
				'store_id' => $this->data['store_id'],
				'user_id' => $this->data['user_id'],
		);

		$transactions = $this->model_openpos_transaction->getTransactions($data);
		$this->data['transactions'] = array();
		foreach($transactions as $trans)
		{
			$trans['money_in'] = $this->currency->format($trans['money_in']);
			$trans['money_out'] = $this->currency->format($trans['money_out']);
			$this->data['transactions'][] = $trans;
		}
		$transactions_total = $this->model_openpos_transaction->getTotalTransactions($this->data['store_id'],$this->data['user_id']);
		$this->data['total'] = $this->currency->format($this->model_openpos_transaction->getTotal($this->data['store_id'],$this->data['user_id']));
		$pagination = new Pagination();
		
		$pagination->total = $transactions_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('module/openpos/transaction', 'token=' . $this->session->data['token']  . '&page={page}', 'SSL');
		
		$this->data['pagination'] = $pagination->render();
		$this->data['empty'] = true;
		
		if($transactions_total > 0)
		{
			$this->data['empty'] = false;
		}
		
		$this->data['token'] =  $this->session->data['token'];
		$this->data['breadcrumbs'] = array();
		
		$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => false
		);
		
		$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_module'),
				'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => ' :: '
		);
		
		$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('module/openpos', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => ' :: '
		);
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
		$this->data['users'] = $this->model_openpos_transaction->getTransactionUser();


		$this->document->setTitle('All Transactions');
		$this->data['heading_title'] = 'All Transactions';
		$this->data['cancel'] = $this->url->link('module/openpos', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['header'] = $this->load->controller('common/header');
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('module/openpos/openpos_transaction.tpl', $this->data));
	
	}

	public function orders()
	{
		$this->load->model('openpos/order');
		$this->language->load('module/openpos');
		if (isset($this->request->get['filter_store_id'])) {
			$filter_store_id = $this->request->get['filter_store_id'];
		} else {
			$filter_store_id = null;
		}

		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = null;
		}

		if (isset($this->request->get['filter_customer'])) {
			$filter_customer = $this->request->get['filter_customer'];
		} else {
			$filter_customer = null;
		}

		if (isset($this->request->get['filter_order_status'])) {
			$filter_order_status = $this->request->get['filter_order_status'];
		} else {
			$filter_order_status = null;
		}

		if (isset($this->request->get['filter_total'])) {
			$filter_total = $this->request->get['filter_total'];
		} else {
			$filter_total = null;
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'o.order_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_store_id'])) {
			$url .= '&filter_store_id=' . $this->request->get['filter_store_id'];
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('module/openpos/orders', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['invoice'] = $this->url->link('sale/order/invoice', 'token=' . $this->session->data['token'], 'SSL');
		$data['shipping'] = $this->url->link('sale/order/shipping', 'token=' . $this->session->data['token'], 'SSL');
		$data['insert'] = $this->url->link('sale/order/add', 'token=' . $this->session->data['token'], 'SSL');

		$data['orders'] = array();

		$filter_data = array(
			'filter_order_id'      => $filter_order_id,
			'filter_store_id'      => $filter_store_id,
			'filter_customer'	   => $filter_customer,
			'filter_order_status'  => $filter_order_status,
			'filter_total'         => $filter_total,
			'filter_date_added'    => $filter_date_added,
			'filter_date_modified' => $filter_date_modified,
			'sort'                 => $sort,
			'order'                => $order,
			'start'                => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                => $this->config->get('config_limit_admin')
		);

		$order_total = $this->model_openpos_order->getTotalOrders($filter_data);

		$results = $this->model_openpos_order->getOrders($filter_data);

		foreach ($results as $result) {
			$data['orders'][] = array(
				'order_id'      => $result['order_id'],
				'customer'      => $result['customer'],
				'status'        => $result['status'],
				'total'         => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				'date_added'    => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
				'shipping_code' => $result['shipping_code'],
				'view'          => $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'] . $url, 'SSL'),
				'edit'          => $this->url->link('sale/order/edit', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'] . $url, 'SSL'),
				'delete'        => $this->url->link('sale/order/delete', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'] . $url, 'SSL')
			);
		}
		$this->document->setTitle('All POS Order ');
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_missing'] = $this->language->get('text_missing');

		$data['column_order_id'] = $this->language->get('column_order_id');
		$data['column_customer'] = $this->language->get('column_customer');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_total'] = $this->language->get('column_total');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_date_modified'] = $this->language->get('column_date_modified');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_return_id'] = $this->language->get('entry_return_id');
		$data['entry_order_id'] = $this->language->get('entry_order_id');
		$data['entry_customer'] = $this->language->get('entry_customer');
		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_total'] = $this->language->get('entry_total');
		$data['entry_date_added'] = $this->language->get('entry_date_added');
		$data['entry_date_modified'] = $this->language->get('entry_date_modified');

		$data['button_invoice_print'] = $this->language->get('button_invoice_print');
		$data['button_shipping_print'] = $this->language->get('button_shipping_print');
		$data['button_insert'] = $this->language->get('button_insert');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');
		$data['button_view'] = $this->language->get('button_view');

		$data['token'] = $this->session->data['token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_store_id'])) {
			$url .= '&filter_store_id=' . $this->request->get['filter_store_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_order'] = $this->url->link('module/openpos/orders', 'token=' . $this->session->data['token'] . '&sort=o.order_id' . $url, 'SSL');
		$data['sort_customer'] = $this->url->link('module/openpos/orders', 'token=' . $this->session->data['token'] . '&sort=customer' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('module/openpos/orders', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');
		$data['sort_total'] = $this->url->link('module/openpos/orders', 'token=' . $this->session->data['token'] . '&sort=o.total' . $url, 'SSL');
		$data['sort_date_added'] = $this->url->link('module/openpos/orders', 'token=' . $this->session->data['token'] . '&sort=o.date_added' . $url, 'SSL');
		$data['sort_date_modified'] = $this->url->link('module/openpos/orders', 'token=' . $this->session->data['token'] . '&sort=o.date_modified' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_store_id'])) {
			$url .= '&filter_store_id=' . $this->request->get['filter_store_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('module/openpos/orders', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['filter_order_id'] = $filter_order_id;
		$data['filter_store_id'] = $filter_store_id;
		$data['filter_customer'] = $filter_customer;
		$data['filter_order_status'] = $filter_order_status;
		$data['filter_total'] = $filter_total;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_date_modified'] = $filter_date_modified;

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['sort'] = $sort;
		$data['order'] = $order;

		$this->load->model('setting/store');
		$data['stores'] = array();
		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->config->get('config_name') . $this->language->get('text_default')
		);
		$results = $this->model_setting_store->getStores();
		foreach ($results as $result) {
			$data['stores'][] = array(
				'store_id' => $result['store_id'],
				'name'     => $result['name']
			);
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('module/openpos/openpos_orders.tpl', $data));
	}
	
	public function product()
	{
		$this->language->load('catalog/product');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/product');
		
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}
		
		if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
		} else {
			$filter_model = null;
		}
		
		if (isset($this->request->get['filter_price'])) {
			$filter_price = $this->request->get['filter_price'];
		} else {
			$filter_price = null;
		}
		
		if (isset($this->request->get['filter_quantity'])) {
			$filter_quantity = $this->request->get['filter_quantity'];
		} else {
			$filter_quantity = null;
		}
		
		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pd.name';
		}
		
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$url = '';
		
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}
		
		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}
		
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$this->data['breadcrumbs'] = array();
		
		$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => false
		);
		
		$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('module/openpos/product', 'token=' . $this->session->data['token'] . $url, 'SSL'),
				'separator' => ' :: '
		);
		
		$this->data['print'] = $this->url->link('module/openpos/print', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['back'] = $this->url->link('module/openpos', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['label_setting'] = $this->url->link('module/openpos/label', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$this->data['products'] = array();
		
		$data = array(
				'filter_name'	  => $filter_name,
				'filter_model'	  => $filter_model,
				'filter_price'	  => $filter_price,
				'filter_quantity' => $filter_quantity,
				'filter_status'   => $filter_status,
				'sort'            => $sort,
				'order'           => $order,
				'start'           => ($page - 1) * $this->config->get('config_admin_limit'),
				'limit'           => $this->config->get('config_admin_limit')
		);
		
		$this->load->model('tool/image');
		
		$product_total = $this->model_catalog_product->getTotalProducts($data);
		
		$results = $this->model_catalog_product->getProducts($data);
		
		foreach ($results as $result) {
			$action = array();
		
			$action[] = array(
					'text' => $this->language->get('text_edit'),
					'href' => $this->url->link('module/openpos/printbarcode', 'token=' . $this->session->data['token'] . '&product_id=' . $result['product_id'] . $url, 'SSL')
			);
		
			if ($result['image'] && file_exists(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.jpg', 40, 40);
			}
		
			$special = false;
		
			$product_specials = $this->model_catalog_product->getProductSpecials($result['product_id']);
		
			foreach ($product_specials  as $product_special) {
				if (($product_special['date_start'] == '0000-00-00' || $product_special['date_start'] < date('Y-m-d')) && ($product_special['date_end'] == '0000-00-00' || $product_special['date_end'] > date('Y-m-d'))) {
					$special = $product_special['price'];
		
					break;
				}
			}
			$this->load->model('openpos/pos_product');
			$this->data['products'][] = array(
					'product_id' => $result['product_id'],
					'name'       => $result['name'],
					'model'      => $result['model'],
					'price'      => $result['price'],
					'upc'      	 => $result['upc'],
					'special'    => $special,
					'image'      => $image,
					'quantity'   => $result['quantity'],
					'status'     => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
					'selected'   => isset($this->request->post['selected']) && in_array($result['product_id'], $this->request->post['selected']),
					'action'     => $action,
					'dummy'      => $this->model_openpos_pos_product->isDummy($result['product_id'])
			);
		}
		
		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		$this->data['text_image_manager'] = $this->language->get('text_image_manager');
		
		$this->data['column_image'] = $this->language->get('column_image');
		$this->data['column_name'] = $this->language->get('column_name');
		$this->data['column_model'] = $this->language->get('column_model');
		$this->data['column_price'] = $this->language->get('column_price');
		$this->data['column_quantity'] = $this->language->get('column_quantity');
		$this->data['column_status'] = $this->language->get('column_status');
		$this->data['column_action'] = $this->language->get('column_action');
		
		$this->data['button_copy'] = $this->language->get('button_copy');
		$this->data['button_insert'] = $this->language->get('button_insert');
		$this->data['button_delete'] = $this->language->get('button_delete');
		$this->data['button_filter'] = $this->language->get('button_filter');
		
		$this->data['token'] = $this->session->data['token'];
		
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
		
		$url = '';
		
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}
		
		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}
		
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$this->data['sort_name'] = $this->url->link('module/openpos/product', 'token=' . $this->session->data['token'] . '&sort=pd.name' . $url, 'SSL');
		$this->data['sort_model'] = $this->url->link('module/openpos/product', 'token=' . $this->session->data['token'] . '&sort=p.model' . $url, 'SSL');
		$this->data['sort_price'] = $this->url->link('module/openpos/product', 'token=' . $this->session->data['token'] . '&sort=p.price' . $url, 'SSL');
		$this->data['sort_quantity'] = $this->url->link('module/openpos/product', 'token=' . $this->session->data['token'] . '&sort=p.quantity' . $url, 'SSL');
		$this->data['sort_status'] = $this->url->link('module/openpos/product', 'token=' . $this->session->data['token'] . '&sort=p.status' . $url, 'SSL');
		$this->data['sort_order'] = $this->url->link('module/openpos/product', 'token=' . $this->session->data['token'] . '&sort=p.sort_order' . $url, 'SSL');
		
		$url = '';
		
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}
		
		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}
		
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('module/openpos/product', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
		$this->data['pagination'] = $pagination->render();
		$this->data['filter_name'] = $filter_name;
		$this->data['filter_model'] = $filter_model;
		$this->data['filter_price'] = $filter_price;
		$this->data['filter_quantity'] = $filter_quantity;
		$this->data['filter_status'] = $filter_status;
		
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		
		$this->data['header'] = $this->load->controller('common/header');
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('module/openpos/openpos_product.tpl', $this->data));
	}

	public function label()
	{
		$this->language->load('module/openpos');
		$this->load->model('setting/setting');
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$post = $this->request->post;
			$this->data['openposlabel'] = $this->request->post;
			$this->model_setting_setting->editSetting('openposlabel',$post );

			$this->session->data['success'] = $this->language->get('text_success');
			$this->data['success'] = $this->language->get('text_success');
		} elseif ($this->model_setting_setting->getSetting('openposlabel')) {
			$this->data['openposlabel'] = $this->model_setting_setting->getSetting('openposlabel');


		}else{
			$this->data['openposlabel'] = array (
				'openposlabel_label_hirizontal_space' => '0',
				'openposlabel_label_corner_radius' => '2.38',
				'openposlabel_label_margin_left' => '6.35',
				'openposlabel_label_vertical_space' => '0',
				'openposlabel_label_margin_bottom' => '12.7',
				'openposlabel_label_margin_right' => '6.35',
				'openposlabel_label_margin_top' => '12.7',
				'openposlabel_label_vertical_height' => '50.8',
				'openposlabel_label_width' => '25.4',
				'openposlabel_label_sheet_height' => '279.4',
				'openposlabel_label_sheet_width' => '215.9',
				'openposlabel_barcode_height' => 40,
				'openposlabel_label_template' => '<div>{{product_name}}</div><div>{{barcode}}</div><div>{{product_price}}</div>'
			);
		}
		$this->document->setTitle($this->language->get('heading_title'));
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['text_default'] = $this->language->get('text_default');

		$this->data['tab_general'] = $this->language->get('tab_general');
		$this->data['tab_store'] = $this->language->get('tab_store');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text'      =>  $this->language->get('text_label_products'),
			'href'      => $this->url->link('module/openpos/product', 'token=' . $this->session->data['token'] , 'SSL'),
			'separator' => ' :: '
		);
		$this->data['token'] = $this->session->data['token'];

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
		$this->data['cancel'] = $this->url->link('module/openpos/product', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['action'] = $this->url->link('module/openpos/label', 'token=' . $this->session->data['token'], 'SSL');


		$this->data['header'] = $this->load->controller('common/header');
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('module/openpos/openpos_label.tpl', $this->data));

	}

	public function printbarcode()
	{
		$this->load->model('catalog/product');
		$this->load->model('setting/setting');
		if (!$this->model_setting_setting->getSetting('openposlabel'))
		{
			$this->response->redirect($this->url->link('module/openpos/label', 'token=' . $this->session->data['token'], 'SSL'));
		}
		$openposlabel = $this->model_setting_setting->getSetting('openposlabel');
		$sheetWidth = $this->core->convertMnPt($openposlabel['openposlabel_label_sheet_width']);
		$sheetHeight = $this->core->convertMnPt($openposlabel['openposlabel_label_sheet_height']);
		$labelLength = $openposlabel['openposlabel_label_width'] - 0.2;
		$labelHeight = $openposlabel['openposlabel_label_vertical_height'] - 0.2;
		$labelCornerRadius = $openposlabel['openposlabel_label_corner_radius'];
		$labelHorizontalSpace = $openposlabel['openposlabel_label_hirizontal_space'];
		$labelVerticalSpace =  $openposlabel['openposlabel_label_vertical_space'];

		$spaceWidth = $openposlabel['openposlabel_label_sheet_width'] - $openposlabel['openposlabel_label_margin_right'] - $openposlabel['openposlabel_label_margin_left'];
		$perRow = ceil($spaceWidth / ($labelLength + $labelVerticalSpace + 0.2) );
		if($perRow* ($labelLength + $labelVerticalSpace + 0.2) > $openposlabel['openposlabel_label_sheet_width'])
		{
			$perRow = floor($spaceWidth / ($labelLength + $labelVerticalSpace + 0.2) );
		}
		$spaceLength = $openposlabel['openposlabel_label_sheet_height'] - $openposlabel['openposlabel_label_margin_top'] - $openposlabel['openposlabel_label_margin_bottom'];
		$perCol = ceil($spaceLength / ($labelHeight + $labelHorizontalSpace + 0.2) );
		if($perCol* ($labelHeight + $labelHorizontalSpace + 0.2) > $openposlabel['openposlabel_label_sheet_height'])
		{
			$perCol = floor($spaceLength / ($labelHeight + $labelHorizontalSpace + 0.2) );
		}
		$product_id = $this->request->get['product_id'];
		$product = $this->model_catalog_product->getProduct($product_id);
		$img = $this->core->barcodeImage($product_id,$openposlabel['openposlabel_barcode_height']);
		$html = '<style>@page { margin: '.$openposlabel['openposlabel_label_margin_top'].'mm '.$openposlabel['openposlabel_label_margin_right'].'mm 0mm '.$openposlabel['openposlabel_label_margin_left'].'mm; }</style>';
		$html .= '<table style="width:'.$spaceWidth.'mm;height:'.$spaceLength.'mm;overflow: hidden;border:none;border-collapse: collapse;border-spacing:0;padding: 0;margin: 0;" border="0">';
		for($i = 0;$i< $perCol;$i++)
		{
			$html .= '<tr style="width:'.$spaceWidth.'mm;overflow: hidden;">';
			for($j = 0; $j < $perRow;$j++)
			{
				if($i == ($perCol - 1))
				{
					$html .= '<td  style="margin:0; padding:0;width:'.$labelLength.'mm"><div style="width:'.$labelLength.'mm;height:'.$labelHeight.'mm;border: #ccc solid 0.1mm;float:left;margin-left:0px;margin-top:0px;margin-right:'.$labelVerticalSpace.'mm;border-radius: '.$labelCornerRadius.'mm;over-flow:hidden;">';
				}else{
					$html .= '<td style="margin:0; padding:0;width:'.$labelLength.'mm"><div style="margin:0; padding:0; width:'.$labelLength.'mm;height:'.$labelHeight.'mm;border: #ccc solid 0.1mm;float:left;margin-left:0px;margin-top:0px;margin-right:'.$labelVerticalSpace.'mm;margin-bottom:'.$labelHorizontalSpace.'mm;border-radius: '.$labelCornerRadius.'mm;over-flow:hidden;">';
				}
				$barcode = '<div style="padding: 2px;"><img src="'.$img.'"/></div>';
				$phrase = str_replace(array('{{barcode}}','{{product_name}}','{{product_price}}','{{product_upc}}'), array($barcode,$product['name'],$this->currency->format($product['price']),$product['upc']), $openposlabel['openposlabel_label_template']);
				$phrase = html_entity_decode($phrase);
				$html .= '<div>'.$phrase.'</div>';

				$html .='</div></td>';
			}
			$html .= '</tr>';
		}
		$html .= '</table>';
		$html =  '<html><body style="margin:0;">'.$html.'</body></html>';
		$this->core->getPdf($html,$sheetWidth,$sheetHeight);
		exit(0);
		
	}
	
	function createCheckDigit($code) {
		$evensum = 0;
		$oddsum = 0;
		if ($code) {
			for ($counter=0;$counter<=strlen($code)-1;$counter++) {
				$codearr[]=substr($code,$counter,1);
			}
				
			for ($counter=0;$counter<=count($codearr)-1;$counter++) {
				if ( $counter&1 ) {
					$evensum = $evensum + $codearr[$counter];
				} else {
					$oddsum = $oddsum + $codearr[$counter];
				}
			}
				
			$oddsum = $oddsum *3;
			$oddeven = $oddsum + $evensum;
				
			for ($number=0;$number<=9;$number++) {
				if (($oddeven+$number)%10==0) {
					$checksum = $number;
				}
			}
				
			return $checksum;
		} else {
			return false;
		}
	}
	
	function createUPC($code) {
		if ($code!="") {
			$checkdigit = $this->createCheckDigit($code);
			$upc = $code . $checkdigit;
				
			return $upc;
		} else {
			return false;
		}
	}
	
	function validateUPC($upc) {
		if ($upc!="") {
			$checkdigit = substr($upc, -1);
			$code = substr($upc, 0, -1);
				
			$checksum = $this->createCheckDigit($code);
			if ($checkdigit == $checksum) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	function update_product_meta()
	{
		$this->load->model('openpos/pos_product');
		$product_id = $this->request->post['product_id'];
		$meta_key = $this->request->post['meta_key'];
		$meta_value = $this->request->post['meta_value'];
		if($product_id > 0 and $meta_key !='')
		{
			$this->model_openpos_pos_product->addProductMeta($product_id,$meta_key,$meta_value);
		}
	}

	public function active()
	{
		$this->language->load('module/openpos');
		$this->load->model('setting/setting');
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$post = $this->request->post;
			$this->data['openposkey'] = $post ;
			$key = $post['key'];
			if($this->core->valid($key))
			{
				$this->model_setting_setting->editSetting('openposkey',array('openposkey'=>$key) );
				$this->session->data['success'] = $this->language->get('text_success');
				$this->data['success'] = $this->session->data['success'];
			}else{
				$this->error['warning'] = $this->core->getErrorMsg($key);
			}
		}else{
			$this->data['openposkey'] = $this->model_setting_setting->getSetting('openposkey');
		}

		$this->document->setTitle($this->language->get('heading_title'));
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['text_default'] = $this->language->get('text_default');

		$this->data['tab_general'] = $this->language->get('tab_general');
		$this->data['tab_store'] = $this->language->get('tab_store');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text'      =>  'Open Pos',
			'href'      => $this->url->link('module/openpos', 'token=' . $this->session->data['token'] , 'SSL'),
			'separator' => ' :: '
		);
		$this->data['token'] = $this->session->data['token'];

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
		$this->data['action'] = $this->url->link('module/openpos/active', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['cancel'] = $this->url->link('module/openpos', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['header'] = $this->load->controller('common/header');
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('module/openpos/active.tpl', $this->data));
	}
}



