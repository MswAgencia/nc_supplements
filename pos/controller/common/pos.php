<?php
class ControllerCommonPos extends Controller {
	public $data = array();
	public $error;
	public function __construct($registry) {
		parent::__construct($registry);
		//error_reporting(0);
		//ini_set('display_errors', '0');
	}
	public function index() {

		$this->language->load('module/openpos');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->addScript('view/javascript/pos/lang.js');
		$this->document->addScript('view/javascript/pos/jquery.indexeddb.js');
		$this->document->addScript('view/javascript/pos/mustache.js');
		$this->document->addScript('view/javascript/pos/pos.js');
		$this->document->addScript('view/javascript/pos/custom.js');
		$this->document->addScript('view/javascript/pos/jcarousel/jquery.jcarousel.min.js');
		$this->document->addScript('view/javascript/pos/jcarousel.responsive.js');
		$this->document->addScript('view/javascript/pos/jcarousel.ajax.js');
		$this->document->addScript('view/javascript/pos/jquery.mousewheel.js');
		$this->document->addScript('view/javascript/pos/jquery.jscrollpane.js');
		$this->document->addScript('view/javascript/pos/numeral.min.js');
		$this->document->addScript('view/javascript/pos/jquery.blackcalculator.beta.min.js');
		$this->document->addStyle('view/stylesheet/pos.css');
		$this->document->addStyle('view/stylesheet/jcarousel.responsive.css');
		$this->document->addStyle('view/stylesheet/jquery.jscrollpane.css');
		$this->document->addStyle('view/stylesheet/black_calculator.css');
		
		$this->data['token'] = $this->session->data['postoken'];
		$this->data['data_url'] = html_entity_decode($this->url->link('common/pos/data', 'token=' . $this->session->data['postoken'], 'SSL'));
		$this->data['status_url'] = html_entity_decode($this->url->link('common/pos/status', 'token=' . $this->session->data['postoken'], 'SSL'));
		$this->data['transaction_url'] = html_entity_decode($this->url->link('common/transactions', 'token=' . $this->session->data['postoken'], 'SSL'));
		$this->data['refund_url'] = html_entity_decode($this->url->link('common/refund', 'token=' . $this->session->data['postoken'], 'SSL'));
		$this->data['add_transaction_url'] = html_entity_decode($this->url->link('common/transactions/add', 'token=' . $this->session->data['postoken'], 'SSL'));
		
		$this->load->model('setting/setting');
		$this->load->model('sale/customer');
		$this->load->model('localisation/currency');
		
		$pos = $this->model_setting_setting->getSetting('openpos',$this->session->data['store_id']);
		
		$tmp_store = $this->model_setting_setting->getSetting('config',$this->session->data['store_id']);
		
		if(!isset($tmp_store['config_invoice_prefix']))
		{
			$tmp_store['config_invoice_prefix'] = '';
		}
		$store = array(
				'store_id' => $this->session->data['store_id'] ,
				'config_invoice_prefix'=> $tmp_store['config_invoice_prefix'],
				'config_complete_status_id' => $pos['openpos_config_pos_complete_status_id'],
				'config_invoice_prefix' => $tmp_store['config_invoice_prefix'],
				'config_tax' => $tmp_store['config_tax'],
				'config_currency' => $tmp_store['config_currency'],
				'config_name' => $tmp_store['config_name'],
				'config_owner' =>$tmp_store['config_owner'],
				//'config_address' => $tmp_store['config_address'],
				'config_email'  => $tmp_store['config_email'],
				'config_telephone'  => $tmp_store['config_telephone'],
				'config_fax'  => $tmp_store['config_fax'],
				'config_title'  => $tmp_store['config_meta_title'],
				'config_country_id' => $tmp_store['config_country_id'],
				'config_zone_id' => $tmp_store['config_zone_id'],
				//'config_payment' => $pos['default_payment']
		);
		
		$customer = $this->model_sale_customer->getCustomer($pos['openpos_default_customer']['id']);
		$currency = $this->model_localisation_currency->getCurrencyByCode($tmp_store['config_currency']);
		$address = $this->model_sale_customer->getAddresses($pos['openpos_default_customer']['id']);
		$customer['address'] = $address;
		$based = 'store';
		$tmp_customer = array(
			'customer_id'=> 0,
			'firstname'=> 'Guest',
			'lastname'=> '',
			'email'=> $pos['openpos_default_customer']['email'],
			'telephone'=>'',
		);
		if($pos['openpos_default_customer']['id'])
		{
			$tmp_customer = array(
				'customer_id'=>$customer['customer_id'],
				'firstname'=>$customer['firstname'],
				'lastname'=>$customer['lastname'],
				'email'=>$customer['email'],
				'telephone'=>$customer['telephone'],
			);
		}

		$default_format = '';
		for($i=0;$i<$currency['decimal_place'];$i++)
		{
			$default_format .='0';
		}
		$tmp_currency = array(
				'default_format' => $currency['symbol_left'].'0,0.'.$default_format.$currency['symbol_right'],
				'number_format'  => '0,0.'.$default_format
		);
		
		
		$json_pos = array(
				'cash_user_id' => $this->user->getId(),
				'cashier' => $this->user->getUserName(),
				'store' =>$store,
				'default_customer'=>$tmp_customer,
				'currency' =>$tmp_currency,
				'tax_base_on' =>$based,
				'tax_class_base' => $pos['openpos_config_pos_tax'], // there are 3 type : none, product tax class, choose fixed price ( vale: 0,-1,tax_class_id)
		);
		$this->data['cashier'] =  $this->user->getUserName();
		$this->data['default'] = json_encode($json_pos);
        $receipt_template = addslashes(str_replace("\n","",html_entity_decode(nl2br($pos['openpos_receipt_header']))));
		$this->data['receipt_header'] = $this->core->htmlStringToReceipt($receipt_template);

		$this->load->model('openpos/pos_payment');
		$this->data['payment'] = json_encode($this->model_openpos_pos_payment->getPayment($this->session->data['store_id']));
		
		$this->data['header'] = $this->load->controller('common/header');
		$this->data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('common/pos.tpl', $this->data));
		$this->template = 'common/pos.tpl';
		
	}
	public function active()
	{
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if(isset($this->request->post['key']) )
			{
				$key = $this->request->post['key'];
				if($this->core->valid($key))
				{
					$this->load->model('setting/setting');
					$this->model_setting_setting->editSetting('openposkey',array('openposkey'=>$key) );
					$this->redirect($this->url->link('common/login','', 'SSL'));
				}else{
					$this->error['warning'] = $this->core->getErrorMsg($key);
				}
			}

		}
		$this->language->load('common/login');
		$this->document->setTitle($this->language->get('heading_title'));
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

		$this->data['action'] = $this->url->link('common/pos/active',  'token=' . $this->session->data['postoken'], 'SSL');
		$this->data['header'] = $this->load->controller('common/header');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('common/active.tpl', $this->data));
	}
	public function status()
	{
		echo '1';
		exit;
	}
	
	public function data()
	{
		$request = '';
		if(isset($this->request->get['action']))
		{
			$request = $this->request->get['action'];
		}
		
		if(isset($this->request->post['action']))
		{
			$request = $this->request->post['action'];
		}
		
		if($request !='')
		{
			$action = '_'.strtolower($request);
			if(method_exists($this,$action))
			{
				$this->$action();
				exit;
			}else{
				echo 'action not found';
				exit;
			}
			
		}else{
			$this->getData();
		}
		
	}
	
	private function getData()
	{
		$json = array();
		$this->load->model('tool/image');
		$this->load->model('setting/setting');
		$this->load->model('setting/store');
		$this->load->model('catalog/product');
		$this->load->model('catalog/category');
		$this->load->model('sale/customer');
		$this->load->model('localisation/currency');
		$json['token'] =  $this->session->data['postoken'];
		$pos = $this->model_setting_setting->getSetting('openpos',$this->session->data['store_id']);
		
		$tmp_store = $this->model_setting_setting->getSetting('config',$this->session->data['store_id']);
		if(!isset($tmp_store['config_invoice_prefix']))
		{
			$tmp_store['config_invoice_prefix'] = '';
		}
		$store = array(
				'store_id' =>$this->session->data['store_id'] ,
				'config_invoice_prefix'=> $tmp_store['config_invoice_prefix'],
				'config_complete_status_id' => $pos['openpos_config_pos_complete_status_id'],
				'config_invoice_prefix' => $tmp_store['config_invoice_prefix'],
				'config_tax' => $tmp_store['config_tax'],
				'config_currency' => $tmp_store['config_currency'],
				'config_name' => $tmp_store['config_name'],
				'config_owner' =>$tmp_store['config_owner'],
				'config_address' => $tmp_store['config_address'],
				'config_email'  => $tmp_store['config_email'],
				'config_telephone'  => $tmp_store['config_telephone'],
				'config_fax'  => $tmp_store['config_fax'],
				'config_title'  => $tmp_store['config_meta_title'],
				'config_country_id' => $tmp_store['config_country_id'],
				'config_zone_id' => $tmp_store['config_zone_id'],
		);
		
		$customer = $this->model_sale_customer->getCustomer($pos['openpos_default_customer']['id']);
		$currency = $this->model_localisation_currency->getCurrencyByCode($tmp_store['config_currency']);
		$address = $this->model_sale_customer->getAddresses($pos['openpos_default_customer']['id']);
		$customer['address'] = $address;
	    $based = 'store';
		$json['pos'] = array(
				array('key' =>'store_id','value'=>$store),
				array('key' =>'default_customer_id','value'=>$customer),
				array('key' =>'currency','value'=>$currency),
				array('key' =>'tax_base_on','value'=>$based),
				array('key' =>'tax_class_base','value'=> $pos['openpos_config_pos_tax']), // there are 3 type : none, product tax class, choose fixed price ( vale: 0,-1,tax_class_id)
		);
		//category
		
		
		$categories = array();
		if(!isset($pos['openpos_categories']))
		{
			$pos['openpos_categories'] = array();
		}
		foreach($pos['openpos_categories'] as $cat)
		{
			$category_info = $this->model_catalog_category->getCategory($cat);
			$tmp['name'] = $category_info['name'];
			$tmp['category_id'] = $category_info['category_id'];
			$tmp['total'] = $this->model_catalog_product->getTotalProductsByCategoryId($category_info['category_id'],$this->session->data['store_id']);
			$id = $category_info['category_id'];
			$categories[$id] = $tmp;
		}
		$json['categories'] = $categories;
		//end category
		
		//product
		if(isset($this->request->get['page']))
		{
			$page = trim($this->request->get['page']);
		}else{
			$page = 0;
		}
		$page = (int)$page;
		if($page > 0)
		{
			$json = array();
		}
		$products = array();
		$total = $this->model_catalog_product->getTotalProductByStoreId($this->session->data['store_id']);
		$limit = 100;
		$totalPage = round($total / $limit) ;
		$offset = $page * $limit + 1;
		//$results = $this->model_catalog_product->getProductsByStoreId($this->session->data['store_id']);
		$results = $this->model_catalog_product->getProductsByStoreId($this->session->data['store_id'],$limit,$offset);
		if($page < $totalPage)
		{
			$page = $page + 1;
		}
		$json['page'] = $page;
		$this->load->model('openpos/pos_product');
		foreach($results as $re)
		{
			$img = $re['image'];

			$tax_rates = $this->tax->getRates($re['price'], $re['tax_class_id']);
				
			$tmp = array(
				'product_id' => $re['product_id'],
				'upc' => $re['upc'],
				'name' => $re['name'],
				'sku'  => $re['sku'],
				'model' => $re['model'],
				'quantity'=> $re['quantity'],
				'price' => $re['price'],
				'base_price' => $re['price'], 
				'points' => $re['points'],
				'tax_class_id' => $re['tax_class_id'],
				'image' =>  $img ? $this->model_tool_image->resize($img, 80, 80) : HTTP_SERVER.'view/image/no-image.jpg',
				'points' => $re['points'],
				'dummy'  => (int)$this->model_openpos_pos_product->isDummy($re['product_id']),
				'category_id' => $this->model_catalog_product->getProductCategories($re['product_id'])
				
			);
			
			$tmp['discounts'] = array();
			$discounts = $this->model_catalog_product->getProductDiscounts($re['product_id']);
				
			foreach ($discounts as $discount) {
				$tmp['discounts'][] = array(
						'quantity' => $discount['quantity'],
						'price'    => $this->currency->format($this->tax->calculate($discount['price'], $re['tax_class_id'], $this->config->get('config_tax')),'','',false),
						'base_price' => $discount['price'],
						'priority'  => $discount['price'],
						'customer_group_id' => $discount['customer_group_id'],
						'date_start' => $discount['date_start'],
						'date_end' => $discount['date_end']
				);
			}
			//options
			$this->load->model('catalog/option');
			$tmp['options'] = array();
			$lang = $this->config->get('config_language_id');
			foreach ($this->model_catalog_product->getProductOptions($re['product_id']) as $option) {
				
				if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
					$option_value_data = array();
					
					foreach ($option['product_option_value'] as $option_value) {
						if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
							if (($this->config->get('config_customer_price') || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
								$price = $option_value['price'];
							} else {
								$price = false;
							}
							
							$image = '';
							$opt_name = '';
							$product_option_value_id = 0;
							$option_value_id  = isset($option_value['option_value_id'])? $option_value['option_value_id']:0;
							
							if(isset($option_value['option_value_id']))
							{
								$product_option_value_id = $option_value['option_value_id'];
								
								$toption_value = $this->model_catalog_option->getOptionValue($product_option_value_id);

								if(isset($toption_value['image']))
								{

									$image = $this->model_tool_image->resize($toption_value['image'], 50, 50);
								}
								if(isset($toption_value['name']))
								{
									$opt_name = $toption_value['name'];
								}
							}
							
							$price_prefix = isset($option_value['price_prefix'])?$option_value['price_prefix']:'';
							
							$option_value_data[] = array(
									'product_option_value_id' => $product_option_value_id,
									'option_value_id'         => $option_value_id,
									'name'                    => $opt_name,
									'image'                   => $image,
									'price'                   => $price,
									'price_prefix'            => $price_prefix
							);
						}
					}
					
					$tmp['options'][] = array(
							'product_option_id' => $option['product_option_id'],
							'option_id'         => $option['option_id'],
							'name'              => $option['name'],
							'type'              => $option['type'],
							'option_value'      => $option_value_data,
							'required'          => $option['required']
					);
				} elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
					$option['option_value'] = isset( $option['option_value'])?  $option['option_value']:'';
					$tmp['options'][] = array(
							'product_option_id' => $option['product_option_id'],
							'option_id'         => $option['option_id'],
							'name'              => $option['name'],
							'type'              => $option['type'],
							'option_value'      => $option['option_value'],
							'required'          => $option['required']
					);
				}
			}
			
			
			$special = $this->model_catalog_product->getProductSpecials($re['product_id']);
			
			$tmp_special = array();
			foreach($special as $s)
			{
				$tmp1 = array(
							'price' => $s['price'],
							'base_price' => $s['price'],
						);
				
				$t = array_merge($s,$tmp1);
				$tmp_special[] = $t;
			}
			
			$tmp['special'] = $tmp_special; 
			
			$products[$re['product_id']] = $tmp;
		}
		$json['products'] = $products;
		//end product
		//start tax
		//$tax_query = $this->db->query("SELECT *,tr2.name as rate_name,tr2g.customer_group_id FROM " . DB_PREFIX . "tax_rule tr1 LEFT JOIN " . DB_PREFIX . "tax_rate tr2 ON (tr1.tax_rate_id = tr2.tax_rate_id) LEFT JOIN " . DB_PREFIX . "tax_rate_to_customer_group tr2g ON(tr2g.tax_rate_id = tr2.tax_rate_id)  WHERE tr1.based = '".$based."'   ORDER BY tr1.priority ASC");
		$tax_query = $this->db->query("SELECT tr1.tax_rule_id,tr1.tax_class_id,tr1.based,tr2cg.customer_group_id,z2gz.country_id,z2gz.country_id,z2gz.zone_id,tr2.tax_rate_id, tr2.name, tr2.rate, tr2.type, tr1.priority FROM " . DB_PREFIX . "tax_rule tr1 LEFT JOIN " . DB_PREFIX . "tax_rate tr2 ON (tr1.tax_rate_id = tr2.tax_rate_id) LEFT JOIN " . DB_PREFIX . "tax_rate_to_customer_group tr2cg ON (tr2.tax_rate_id = tr2cg.tax_rate_id) LEFT JOIN " . DB_PREFIX . "zone_to_geo_zone z2gz ON (tr2.geo_zone_id = z2gz.geo_zone_id) LEFT JOIN " . DB_PREFIX . "geo_zone gz ON (tr2.geo_zone_id = gz.geo_zone_id) WHERE  tr1.based = '".$based."'  AND z2gz.country_id = '" . (int)$tmp_store['config_country_id'] . "' AND (z2gz.zone_id = '0' OR z2gz.zone_id = '" . (int)$tmp_store['config_zone_id'] . "') ORDER BY tr1.priority ASC");
		
		$json['tax'] = $tax_query->rows ;
		//end tax
		//start zone
		//$zone_query = $this->db->query("SELECT z.*,zgz.zone_to_geo_zone_id,zgz.geo_zone_id,c.name as 'country_name' FROM " . DB_PREFIX . "zone z LEFT JOIN " . DB_PREFIX . "zone_to_geo_zone zgz ON (z.zone_id = zgz.zone_id) LEFT JOIN " . DB_PREFIX . "country c ON (c.country_id = z.country_id)  ORDER BY name ASC");
		//$json['zone'] = $zone_query->rows ;
		//end zone
		$this->response->setOutput(json_encode($json));
	}


	private function _customersearch()
	{
		$term = trim($this->request->get['term']);
		
		$this->load->model('sale/customer');
		$results = $this->model_sale_customer->search($term);
		$rs = array();
		foreach($results as $re)
		{
			$tmp = array();
			$tmp['id'] = $re['customer_id'];
			$tmp['label'] = $re['firstname'].' '.$re['lastname'];
			$tmp['value'] = $re['customer_id'];
			$tmp['email'] = $re['email'];
			$tmp['telephone'] = $re['telephone'];
			$tmp['firstname'] = $re['firstname'];
			$tmp['lastname'] = $re['lastname'];
			$tmp['customer_group_id'] = $re['customer_group_id'];
			$rs[] = $tmp;
		}
		
		echo json_encode($rs);
		exit;
	}
	
	private function _checkegift()
	{
		$this->load->model('sale/voucher');
		$code = trim($this->request->get['code']);
		$voucher_info = $this->model_sale_voucher->getPosVoucher($code);
		$rs = array();
		if (!$voucher_info) {
			$rs['status'] = 0;
			
		}else{
			$rs['status'] = 1;
			$rs['info'] = $voucher_info;
		}
		
		echo json_encode($rs);
		exit;
	}
	
	private function _checkcoupon()
	{
		$this->load->model('sale/coupon');
		$rs = array();
		$code = trim($this->request->get['code']);
		$coupon_info = $this->model_sale_coupon->getPosCoupon($code);
		
		if (!$coupon_info) {
			$rs['status'] = 0;
		}else{
			$rs['status'] = 1;
			$rs['info'] = $coupon_info;
		}
		echo json_encode($rs);
		exit;
	}
	
	private function _order()
	{
		error_reporting(0);
		$this->load->model('setting/setting');
		$this->load->model('sale/customer');
		$this->load->model('sale/order');
		$this->load->model('user/user');
		$rs = array();
		$data = html_entity_decode($this->request->post['data']);
		$data = json_decode($data);
		
		$pos = $this->model_setting_setting->getSetting('openpos',$data->store_id);
		$tmp_store = $this->model_setting_setting->getSetting('config',$data->store_id);
		$defaultCustomer = $this->model_sale_customer->getCustomer($pos['openpos_default_customer']['id']);
		$defaultAddress = $this->model_sale_customer->getAddresses($pos['openpos_default_customer']['id']);
		$tmp = array();
		foreach($defaultAddress as $ad)
		{
			$tmp = array_merge($ad,$tmp);
			
		}
		$defaultCustomer = array_merge($tmp,$defaultCustomer);
		$tmpcustomer = array();
		$comment = '';
		$order_voucher = array();
		$order_product = array();
		
		$sub_total = 0;
		$point = 0;
		foreach($data->data as $item)
		{
			$tmp = (array)$item;
			if(isset($tmp['type']) and $tmp['type'] =='edit-order')
			{
				$order_id = $tmp['order_id'];
				$oldorder = $this->model_sale_order->getOrder($order_id);
				$this->model_sale_order->deleteOrder($order_id);
				$this->load->model('sale/transaction');
				$comment = 'Remove order #'.$order_id;
				$today = date("Y-m-d H:i:s");
					
				$tdata = array(
						'store_id' => $this->session->data['store_id'],
						'type' => 'pos',
						'money_in' => 0,
						'money_out' => $oldorder['total'],
						'comment' => $comment,
						'date_created' => $today
				);
				$this->model_sale_transaction->addTransaction($tdata);
			}
		}
		foreach($data->data as $item)
		{
			$tmp = (array)$item;
			
			if(isset($tmp['type']) and $tmp['type'] =='customer')
			{
				$tmpcustomer = (array)$tmp['value'];
				$tmpAddress = $this->model_sale_customer->getAddresses($tmpcustomer['customer_id']);
				$tmp = array();
				foreach($tmpAddress as $ad)
				{
					$tmp = array_merge($ad,$tmp);
						
				}
				
				$tmpcustomer = array_merge($tmp,$tmpcustomer);
			}
			
			if(isset($tmp['type']) and $tmp['type'] =='comment')
			{
				$comment = $tmp['comment'];
			}
			
			if(isset($tmp['type']) and $tmp['type'] =='product')
			{
				$ptax = 0;
				if(isset($tmp['tax']))
				{
					$tx = (array)$tmp['tax'];
					$ptax = $tx['total'];
				}

				$ptax = 0;
				$sub_total += $tmp['subtotal'];
				$tmp_product = array(
						'product_id' => $tmp['product_id'],
						'name' => $tmp['name'],
						'model' => $tmp['model'],
						'quantity' => $tmp['qty'],
						'price' => $tmp['price'],
						'total' => $tmp['price']*$tmp['qty'],
						'tax' => $ptax,
						'reward' => $tmp['point']*$tmp['qty'],
				);
				if(!empty($tmp['options']))
				{
					$tmp_product['order_option'] = array();
					foreach($tmp['options'] as $op)
					{
						$op = (array)$op;
						$t =  array(
								'product_option_id' => $op['product_option_id'],
								'product_option_value_id' => $op['product_option_value_id'],
								'name' => $op['name'],
								'value' => $op['value'],
								'type' => $op['type'],
						);
						$tmp_product['order_option'][] = $t;
					}
				}
				$point += $tmp['point']*$tmp['qty'];
				$order_product[] = $tmp_product;
			}
		}
		
		$customer = array_merge($defaultCustomer,$tmpcustomer);
		
		if(isset($customer['createnew']) && $customer['email'] !='' && $customer['createnew'] == 1)
		{
			$this->load->model('sale/customer');
			$customer['newsletter'] = 1;
			$customer['password'] = 'password';
			if(count($this->model_sale_customer->search($customer['email'])) == 0)
			{
				$this->model_sale_customer->addCustomer($customer);
			}
		}
		
		$this->load->model('sale/voucher');
		$totaldiscount = 0;
		if(isset($data->discount))
		{
			$discount = (array)$data->discount;
			if(!empty($discount))
			{
				foreach($discount as $d)
				{
					$d = (array)$d;
					
					$t = (array)$d['value'];
					
					if($d['type'] == 'egift')
					{
						$voucher_info = $this->model_sale_voucher->getVoucher($t['voucher_id']);
						$voucher_info['description'] = 'Voucher checkout';
						$order_voucher[] = $voucher_info;
					}else{
						if($t['type'] == 'F')
						{
							$totaldiscount += $t['total'];
						}else{
							$totaldiscount += $sub_total*$t['price']/100;
						}
						
					}
				}
			}
		}
		
		
		
		$order_total = array(
				array(
						'code' => 'sub_total',
						'title' => 'Sub-Total',
						'text' => $this->currency->format($sub_total, $this->config->get('config_currency')),
						'value' => $sub_total,
						'sort_order' => 1,
				),
				
				array(
					'code' => 'total',
					'title' => 'Total',
					'text' => $this->currency->format($data->grand_total, $this->config->get('config_currency')),
					'value' => $data->grand_total,
					'sort_order' => 25,
				),
				array(
						'code' => 'total_paid',
						'title' => 'Total Paid',
						'text' => $this->currency->format($data->total_paid, $this->config->get('config_currency')),
						'value' => $data->total_paid,
						'sort_order' => 30,
				),
				array(
						'code' => 'balance',
						'title' => 'Balance',
						'text' => $this->currency->format($data->balance, $this->config->get('config_currency')),
						'value' => $data->balance,
						'sort_order' => 40,
				),
				array(
						'code' => 'refund',
						'title' => 'Refund',
						'text' => $this->currency->format(0, $this->config->get('config_currency')),
						'value' => 0,
						'sort_order' => 50,
				),
				
		);
		
		if(isset($data->tax))
		{
			$taxs = (array)$data->tax;
			if(!empty($taxs))
			{
				$count = 0;
				foreach($taxs as $key => $tax)
				{
					$tax = (array)$tax;
					if($tax['total'] > 0)
					{
						$order_total[] = array(
								'code' => 'tax',
								'title' => $tax['name'],
								'text' => $this->currency->format($tax['total'], $this->config->get('config_currency')),
								'value' => $tax['total'],
								'sort_order' => 10 + $count,
						);
						$count ++;
					}
					
				}
			}
		}
		
		if($totaldiscount > 0)
		{
			$order_total[] = array(
						'code' => 'discount',
						'title' => 'Discount',
						'text' => '-'.$this->currency->format($totaldiscount, $this->config->get('config_currency')),
						'value' => 0 - $totaldiscount,
						'sort_order' => 20,
				);
		}
		
		$user = $this->model_user_user->getUser((int)$data->cash_user_id);
		
		$payment_name = 'Unknown';
		$payment = false;
		
		if(isset($data->payment) and isset($data->payment->name))
		{
			$payment_id = $data->payment->name;
			$this->load->model('openpos/pos_payment');
			$payment = $this->model_openpos_pos_payment->getPaymentById($payment_id);
			
			$payment_name = isset($payment['payment_name'])?$payment['payment_name']:'Unknow';
			
		}
		
		$payment_name .= '('.$user['email'].')';
		if(!isset($customer['company']))
		{
			$customer['company'] = '';
		}
		if(!isset($customer['company_id']))
		{
			$customer['company_id'] = 0;
		}
		if(!isset($customer['tax_id']))
		{
			$customer['tax_id'] = 0;
		}
		
		if(!isset($customer['address_1']))
		{
			$customer['address_1'] = '';
		}
		
		if(!isset($customer['address_2']))
		{
			$customer['address_2'] = '';
		}
		
		if(!isset($customer['city']))
		{
			$customer['city'] = '';
		}
		
		if(!isset($customer['postcode']))
		{
			$customer['postcode'] = '';
		}
		
		if(!isset($customer['country_id']))
		{
			$customer['country_id'] = 0;
		}
		
		if(!isset($customer['zone_id']))
		{
			$customer['zone_id'] = 0;
		}
		$this->load->model('localisation/currency');
		$currency_info = $this->model_localisation_currency->getCurrencyByCode($this->config->get('config_currency'));
		
		if ($currency_info) {
			$currency_id = $currency_info['currency_id'];
			$currency_code = $currency_info['code'];
			$currency_value = $currency_info['value'];
		} else {
			$currency_id = 0;
			$currency_code = $this->config->get('config_currency');
			$currency_value = 1.00000;
		}

		$order_data = array(
				'store_id' => $data->store_id,
				'invoice_no' => $data->invoice_id,
				'order_product' => $order_product,
				'order_voucher' => $order_voucher,
				'order_total' => $order_total,
				'customer_id' => isset($customer['customer_id']) ? $customer['customer_id'] : 0,
				'customer_group_id' => isset($customer['customer_group_id']) ? $customer['customer_group_id'] : 0,
				'firstname' => isset($customer['firstname']) ? $customer['firstname'] : '',
				'lastname' => isset($customer['lastname']) ? $customer['lastname'] : '',
				'email' => isset($customer['email']) ? $customer['email'] : '',
				'telephone' => isset($customer['telephone']) ? $customer['telephone'] : '',
				'fax' => isset($customer['fax']) ? $customer['fax'] : '',
				'payment_firstname' => isset($customer['firstname']) ? $customer['firstname'] : '',
				'payment_lastname' => isset($customer['lastname']) ? $customer['lastname'] : '',
				'payment_company' => isset($customer['company']) ? $customer['company'] : '',
				'payment_company_id' => isset($customer['company_id']) ? $customer['company_id'] : '',
				'payment_tax_id' => isset($customer['tax_id']) ? $customer['tax_id'] : '',
				'payment_address_1' => isset($customer['address_1']) ? $customer['address_1'] : '',
				'payment_address_2' => isset($customer['address_2']) ? $customer['address_2'] : '',
				'payment_city' => isset($customer['city']) ? $customer['city'] : '',
				'payment_postcode' => isset($customer['postcode']) ? $customer['postcode'] : '',
				'payment_country_id' => isset($customer['country_id']) ? $customer['country_id'] : 0,
				'payment_zone_id' => isset($customer['zone_id']) ? $customer['zone_id'] : 0,
				'payment_zone' => '',
				'payment_method' => $payment_name,
				'payment_code' => 'cash_'. (int)$data->cash_user_id,
				'shipping_firstname' => isset($customer['firstname']) ? $customer['firstname'] : 0,
				'shipping_lastname' => isset($customer['lastname']) ? $customer['lastname'] : '',
				'shipping_company' => isset($customer['company']) ? $customer['company'] : '',
				'shipping_address_1' => isset($customer['address_1']) ? $customer['address_1'] : '',
				'shipping_address_2' => isset($customer['address_2']) ? $customer['address_2'] : '',
				'shipping_city' => isset($customer['lastname']) ? $customer['lastname'] : '',
				'shipping_postcode' => isset($customer['postcode']) ? $customer['postcode'] : '',
				'shipping_country_id' => isset($customer['country_id']) ? $customer['country_id'] : 0,
				'shipping_country' => '',
				'shipping_zone_id' => isset($customer['zone_id']) ? $customer['zone_id'] : 0,
				'shipping_zone' => '',
				'shipping_address_format' => '',
				'shipping_method' => 'Get From Store',
				'shipping_code' => 'free_pos',
				'commission' => 0,
				'marketing_id' => 0,
				'tracking' => '',
				'language_id' => $this->config->get('config_language_id'),
				'currency_id' => $currency_id,
				'currency_code' => $currency_code,
				'currency_value' => $currency_value,
				'ip' => '',
				'forwarded_ip' => '',
				'user_agent' => '',
				'accept_language' => '',
				'comment' => $comment,
				'order_status_id' => $pos['openpos_config_pos_complete_status_id'],
				'affiliate_id' => 0,
				'total' => $data->grand_total
		);
		$order_data['payment_custom_field'] = '';
		if(isset($data->payment))
		{
			if(isset($data->payment->ref) and $data->payment->ref !='')
			{
				$order_data['payment_custom_field']  = $data->payment->ref;
			}
		}
		$subtract = false;
		
		if($pos['openpos_config_pos_subtract_stock'] == 1)
		{
			$subtract = true;
		}
		
		if(!$this->model_sale_order->CheckOrderExits($data->invoice_id))
		{
			
			if($order_id = $this->model_sale_order->addOrder($order_data,$subtract))
			{
				if($customer['customer_id'] >0 and $customer['customer_id'] != $pos['openpos_default_customer']['id'])
				{
					if($pos['openpos_config_alert_mail'] == 1)
					{
						$this->model_sale_order->confirm($order_id,$pos['openpos_config_pos_complete_status_id']);
					}
					$this->load->model('sale/customer');
						
					$reward_total = $this->model_sale_customer->getTotalCustomerRewardsByOrderId($order_id);
					if (!$reward_total) {
						$this->model_sale_customer->addReward($customer['customer_id'],  'Order #' . $order_id, $point, $order_id);
			
					}
					
				}
				$rs = array('status'=>1);
				$this->load->model('sale/transaction');
				$comment = 'POS checkout order #'.$order_id;
				$today = date("Y-m-d H:i:s");
					
				$data = array(
						'store_id' => $this->session->data['store_id'],
						'type' => 'pos',
						'money_in' => $data->total_paid,
						'money_out' => $data->balance,
						'comment' => $comment,
						'date_created' => $today
				);
				if(isset($payment['is_cash']) and $payment['is_cash'] == 1)
				{
					$this->model_sale_transaction->addTransaction($data);
				}
				
			}else{
				$rs = array('status'=>0);
			}
		}else{
			$rs = array('status'=>1);
		}
		
		echo json_encode($rs);
		exit;
	}

	private function _cancel()
	{
		error_reporting(0);
		ini_set('display_errors', '0');
		$this->load->model('setting/setting');
		$this->load->model('sale/customer');
		$this->load->model('sale/order');
		$this->load->model('user/user');
		$rs = array();
		$data = html_entity_decode($this->request->post['data']);
		$data = json_decode($data);
		$pos = $this->model_setting_setting->getSetting('openpos',$data->store_id);
		$rs = array();
		$user = $this->model_user_user->getUser((int)$data->cash_user_id);
		if($order_id = $this->model_sale_order->CheckOrderExits($data->invoice_id))
		{
			// update order
			$order_info = $this->model_sale_order->getOrder($order_id);
			if($order_info['email'] != '')
			{
				$history_data = array(
					'order_status_id' => $pos['openpos_config_pos_cancel_status_id'],
					'notify' => 1,
					'comment' => 'Order update by '.$user['email']
				);
			}else{
				$history_data = array(
					'order_status_id' => $pos['openpos_config_pos_cancel_status_id'],
					'notify' => 0,
					'comment' => 'Order update by '.$user['email']
				);
			}

			$this->model_sale_order->addOrderHistory($order_id,$history_data);
			// add transaction record
			$amount = $data->total_paid;
			$this->load->model('sale/transaction');
			$comment = 'POS refund order #'.$order_id;
			$today = date("Y-m-d H:i:s");
			$tdata = array(
				'store_id' => $data->store_id,
				'type' => 'pos',
				'money_in' => 0,
				'money_out' => $amount,
				'comment' => $comment,
				'date_created' => $today
			);

			if(isset($data->cash_refund) and $data->cash_refund == 1)
			{
				$this->model_sale_transaction->addTransaction($tdata);
			}
		}
		echo json_encode($rs);
		exit;
	}
}