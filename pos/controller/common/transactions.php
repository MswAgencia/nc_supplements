<?php
class ControllerCommonTransactions extends Controller {
	public $data = array();
	public $template = '';
	public function index() {
		$this->load->model('sale/transaction');
		$this->template = 'common/transactions.tpl';
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		$store_id = $this->session->data['store_id'];
		
		
		$data = array(
				'start' => ($page - 1) * 10,
				'limit' => 10,
				'store_id' => $store_id 
		);
		$this->data['transactions'] = array();
		foreach($this->model_sale_transaction->getTransactions($data) as $tran)
		{
			$tran['money_in'] = $this->currency->format($tran['money_in']);
			$tran['money_out'] = $this->currency->format($tran['money_out']);
			$this->data['transactions'][] = $tran;
		}
		
		$transactions_total = $this->model_sale_transaction->getTotalTransactions($store_id);
		$this->data['total'] = $this->currency->format($this->model_sale_transaction->getTotal($store_id));
		$pagination = new Pagination();
		
		$pagination->total = $transactions_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('common/transactions', 'token=' . $this->session->data['postoken']  . '&page={page}', 'SSL');
		
		$this->data['pagination'] = $pagination->render();
		$this->data['empty'] = true;
		if($transactions_total > 0)
		{
			$this->data['empty'] = false;
		}
		$this->response->setOutput($this->load->view($this->template, $this->data));
		
	}
	
	public function add()
	{
		$money_in = isset($this->request->post['money_in'])?$this->request->post['money_in']:0;
		$money_out = isset($this->request->post['money_out'])?$this->request->post['money_out']:0;
		$type = isset($this->request->post['type'])?$this->request->post['type']:'custom';
		$comment = $this->request->post['comment'];
		$today = date("Y-m-d H:i:s");
		$rs = array('status' => 0);
		$store_id = $this->session->data['store_id'];
		
		if($money_in > 0 or $money_out > 0)
		{
			$this->load->model('sale/transaction');
			$data = array(
					'store_id' => $this->session->data['store_id'],
					'type' => $type,
					'money_in' => $money_in,
					'money_out' => $money_out,
					'comment' => $comment,
					'date_created' => $today
			);
			if($this->model_sale_transaction->addTransaction($data))
			{
				$output = '
				<tr class="new-trans">
					<td class="right">'.$this->user->getUserName().'</td>
	                <td class="right">'.$today.'</td>
	                <td class="right">'.$type.'</td>
	                <td class="left">'.$this->currency->format($money_in).'</td>
	                <td class="left">'.$this->currency->format($money_out).'</td>
	                <td class="left">'.$comment.'</td>
              	</tr>
						';
				$transactions_total = $this->currency->format($this->model_sale_transaction->getTotal($store_id));
				
				$rs = array('status' => '1','html' => $output,'total'=>$transactions_total);
			}
		}
		echo json_encode($rs);
		exit;
	}
}