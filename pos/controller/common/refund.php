<?php
class ControllerCommonRefund extends Controller {
	public $data = array();
	public $template = '';
	public function index() {
		if(!isset($this->request->post['order_id']))
		{
			$this->template = 'common/orders.tpl';
			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
			} else {
				$page = 1;
			}
			$data = array(
					'sort'  => 'o.date_added',
					'order' => 'DESC',
					'start' => ($page - 1) * 10,
					'limit' => 5,
					'payment_code'  => 'cash_'.$this->user->getId()
			);
			$this->load->model('sale/order');
			$results = $this->model_sale_order->getOrders($data);
			foreach ($results as $result) {
				$action = array();
			
				$action = array(
						'text' => $this->language->get('text_view'),
						'href' => $this->url->link('sale/order/info', 'token=' . $this->session->data['postoken'] . '&order_id=' . $result['order_id'], 'SSL')
				);
				$totals = $this->model_sale_order->getOrderTotals($result['order_id']);
				$paid = 0;
				$balance = 0;
				foreach($totals as $total)
				{
					if($total['code'] == 'total_paid')
					{
						$paid = $total['title'];
					}
					if($total['code'] == 'balance')
					{
						$balance = $total['title'];
					}
				}
				$this->data['orders'][] = array(
						'order_id'   => $result['order_id'],
						'customer'   => $result['customer'],
						'status'     => $result['status'],
						'payment_method' => $result['payment_method'],
						'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
						'total'      => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
						'total_paid' => $paid,
						'balance' => $balance,
						'action'     => $action
				);
			}
			
			
		
			$data = array(
					'sort'  => 'o.date_added',
					'order' => 'DESC',
					'payment_code'  => 'cash_'.$this->user->getId()
			);
			$results = $this->model_sale_order->getOrders($data);
			$order_total = count($results);
			
			$pagination = new Pagination();
			
			$pagination->total = $order_total;
			$pagination->page = $page;
			$pagination->limit = $this->config->get('config_admin_limit');
			$pagination->text = $this->language->get('text_pagination');
			$pagination->url = $this->url->link('common/refund', 'token=' . $this->session->data['postoken']  . '&page={page}', 'SSL');
			
			$this->data['pagination'] = $pagination->render();
			$this->response->setOutput($this->load->view($this->template, $this->data));
			
		}else{
			$this->load->model('sale/order');
			$order_id = (int)$this->request->post['order_id'];
			$this->data['order'] = $this->model_sale_order->getOrder($order_id);
			$this->data['order_products'] = array();
			$order_products = $this->model_sale_order->getOrderProducts($order_id);
			foreach ($order_products as $order_product) {
				$order_option = $this->model_sale_order->getOrderOptions($order_id, $order_product['order_product_id']);
				if (isset($order_product['order_download'])) {
					$order_download = $order_product['order_download'];
				} elseif (isset($this->request->get['order_id'])) {
					$order_download = $this->model_sale_order->getOrderDownloads($order_id, $order_product['order_product_id']);
				} else {
					$order_download = array();
				}
	
				$this->data['order_products'][] = array(
					'order_product_id' => $order_product['order_product_id'],
					'product_id'       => $order_product['product_id'],
					'name'             => $order_product['name'],
					'model'            => $order_product['model'],
					'option'           => $order_option,
					'download'         => $order_download,
					'quantity'         => $order_product['quantity'],
					'price'            => $order_product['price'],
					'total'            => $order_product['total'],
					'tax'              => $order_product['tax']
				);
			}	
			$this->data['totals'] = $this->model_sale_order->getOrderTotals($order_id);
			echo json_encode($this->data);
		}
		
	}
	
	function cancel()
	{
		$order_id = (int)$this->request->post['order_id'];
	}
}