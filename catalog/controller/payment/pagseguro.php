<?php
class ControllerPaymentPagseguro extends Controller {
 	public function index() {
	
		$this->language->load('payment/pagseguro');
		
	    $data['button_confirm'] = $this->language->get('button_confirm_pagseguro');
	
		require_once(DIR_SYSTEM . 'library/PagSeguroLibrary/PagSeguroLibrary.php');
		
		// Altera a codificação padrão da API do PagSeguro (ISO-8859-1)
		PagSeguroConfig::setApplicationCharset('UTF-8');
		
		$mb_substr = (function_exists("mb_substr")) ? true : false;
		
    	$this->load->model('checkout/order');
	    $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);		
		
		$paymentRequest = new PagSeguroPaymentRequest();
		
		/* 
		 * Dados do cliente
		 */
		 
		// Ajuste no nome do comprador para o máximo de 50 caracteres exigido pela API
		$customer_name = trim($order_info['payment_firstname']) . ' ' . trim($order_info['payment_lastname']);
		
		if($mb_substr){
			$customer_name = mb_substr($customer_name, 0, 50, 'UTF-8');
		}
		else{
			$customer_name = utf8_encode(substr(utf8_decode($customer_name), 0, 50));
		}
		
		if($order_info['currency_code'] != "BRL"){
			$this->log->write("PagSeguro :: Pedido " . $this->session->data['order_id'] . ". O PagSeguro só aceita moeda BRL (Real) e a loja está configurada para a moeda " . $order_info['currency_code']);
		}
		
		$paymentRequest->setCurrency($order_info['currency_code']);
		$paymentRequest->setSenderName(trim($customer_name));
		$paymentRequest->setSenderEmail(trim($order_info['email'])); // há limitação de 60 caracteres de acordo com a API
		
		// OpenCart não separa o DDD do número do telefone. Assim, tentamos separá-los.
		$telefone = preg_replace ("/[^0-9]/", '', $order_info['telephone']);
		$telefone = ltrim($telefone, '0');
		
		if(strlen($telefone) >= 9) {
			$paymentRequest->setSenderPhone(substr($telefone, 0, 2),  substr($telefone, 2, strlen($telefone) - 1)); 
		}
  
	    /* 
	     * Frete
	     */
	    
		$tipo_frete = $this->config->get('pagseguro_tipo_frete');
		
		if($tipo_frete){
			$paymentRequest->setShippingType($tipo_frete);	    	
	    }
	    else{
	    	$paymentRequest->setShippingType(3); // 3: Não especificado
	    }		
 		
		$this->load->model('localisation/zone');
	    
	   	if ($this->cart->hasShipping()) {

			$zone = $this->model_localisation_zone->getZone($order_info['shipping_zone_id']);
			
			// Endereço para entrega		
			$paymentRequest->setShippingAddress(  
				Array(  
					'postalCode'=> preg_replace ("/[^0-9]/", '', $order_info['shipping_postcode']),
					'street' 	=> $order_info['shipping_address_1'],     
					'number' 	=> '', // Não há este campo no OpenCart
					'complement'=> '', // Não há este campo no OpenCart
					'district' 	=> $order_info['shipping_address_2'],         
					'city' 		=> $order_info['shipping_city'],        
					'state' 	=> (isset($zone['code'])) ? $zone['code'] : '',       
					'country' 	=> $order_info['shipping_iso_code_3']
				)  
			);
		}
		else{
			$zone = $this->model_localisation_zone->getZone($order_info['payment_zone_id']);
			
			// Endereço para entrega		
			$paymentRequest->setShippingAddress(  
				Array(  
					'postalCode'=> preg_replace ("/[^0-9]/", '', $order_info['payment_postcode']),
					'street' 	=> $order_info['payment_address_1'],     
					'number' 	=> '', // Não há este campo no OpenCart
					'complement'=> '', // Não há este campo no OpenCart
					'district' 	=> $order_info['payment_address_2'],         
					'city' 		=> $order_info['payment_city'],        
					'state' 	=> (isset($zone['code'])) ? $zone['code'] : '',       
					'country' 	=> $order_info['payment_iso_code_3']
				)  
			);			
		}	   	

		/*
		 * Produtos
		 */
		$this->load->model('tool/upload');
		
 		foreach ($this->cart->getProducts() as $product) {
			$options_names = array();
			$model = ($product['model'] != '') ? ' | Modelo: ' . $product['model'] : '';
	    	
			foreach ($product['option'] as $option) {
				if ($option['type'] != 'file') {
					$value = $option['value'];
				} else {
					$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

					if ($upload_info) {
						$value = $upload_info['name'];
					} else {
						$value = '';
					}
				}
				$options_names[] = $option['name'] . ": " . (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value);
	    	}
				
			if(!empty($options_names)){
				$options = " | Opções:: " . implode(', ', $options_names);
			}
			else{
				$options = '';
			}
			
			// limite de 100 caracteres para a descrição do produto
			if($mb_substr){
				$description = mb_substr($product['name'].$model.$options, 0, 100, 'UTF-8');
			}
			else{
				$description = utf8_encode(substr(utf8_decode($product['name'].$model.$options), 0, 100));
			}
			
	    	$item = Array(
				'id' => $product['product_id'],
				'description' => trim($description),
				'quantity' => $product['quantity'],
				'amount' => $this->currency->format($product['price'], $order_info['currency_code'], false, false)
			);
			
			// O frete será calculado pelo PagSeguro.
			if($tipo_frete){
				$peso = $this->getPesoEmGramas($product['weight_class_id'], $product['weight'])/$product['quantity'];
				$item['weight'] = round($peso);
			}

			$paymentRequest->addItem($item);
	    }
	    
	    // Referência do pedido no PagSeguro
 		if($this->config->get('pagseguro_posfixo') != ""){
	    	$paymentRequest->setReference($this->session->data['order_id']."_".$this->config->get('pagseguro_posfixo'));
	    }
	    else{
			$paymentRequest->setReference($this->session->data['order_id']);
	    }	    

		// url para redirecionar o comprador ao finalizar o pagamento
		$paymentRequest->setRedirectUrl($this->url->link('checkout/success'));
		
		// url para receber notificações sobre o status das transações
		$paymentRequest->setNotificationURL($this->url->link('payment/pagseguro/callback')); 
	    
	    // obtendo frete, descontos e taxas 
		$total = $this->currency->format($order_info['total'] - $this->cart->getSubTotal(), $order_info['currency_code'], false, false);

		if ($total > 0) {
	    	$item = Array(
				'id' 			=> '-',
				'description' 	=> $this->language->get('text_extra_amount'),
				'quantity' 		=> 1,
				'amount' 		=> $total
			);
			$paymentRequest->addItem($item);			
		} 
		else if($total < 0) {
			$paymentRequest->setExtraAmount($total);
		}  
	    
	   	/* 
	   	 * Fazendo a chamada para a API de Pagamentos do PagSeguro. 
	   	 * Se tiver sucesso, retorna o código (url) de requisição para este pagamento.
	   	 */
		$data['url'] = '';
		try {
			$credentials = new PagSeguroAccountCredentials($this->config->get('pagseguro_email'), $this->config->get('pagseguro_token'));
			$url = $paymentRequest->register($credentials);
			$data['url'] = $url;

		} catch (PagSeguroServiceException $e) {
			$this->log->write('PagSeguro :: ' . $e->getOneLineMessage());
		}		
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/pagseguro.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/pagseguro.tpl', $data);
		} else {
			return $this->load->view('default/template/payment/pagseguro.tpl', $data);
		}
	}
		
	public function confirm() {
		if ($this->session->data['payment_method']['code'] == 'pagseguro') {
			$this->load->model('checkout/order');

			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('config_order_status_id'));
		}
	}
			
	public function callback() {
		
		require_once(DIR_SYSTEM . 'library/PagSeguroLibrary/PagSeguroLibrary.php');
		
		$code = (isset($_POST['notificationCode']) && trim($_POST['notificationCode']) != "") ? trim($_POST['notificationCode']) : null;
    	$type = (isset($_POST['notificationType']) && trim($_POST['notificationType']) != "") ? trim($_POST['notificationType']) : null;
    	
    	if($code && $type) {

    		$notificationType = new PagSeguroNotificationType($type);
    		$strType = $notificationType->getTypeFromValue();
    		
    		switch($strType) {
				
				case 'TRANSACTION':
					
    				$credentials = new PagSeguroAccountCredentials($this->config->get('pagseguro_email'), $this->config->get('pagseguro_token'));
										
    		    	try {
			    		$transaction = PagSeguroNotificationService::checkTransaction($credentials, $code);
			    		
			    		$transactionStatus	= $transaction->getStatus();
			    		$id_pedido 			= explode('_', $transaction->getReference());
			    		$paymentMethod 		= $transaction->getPaymentMethod();
			    		$parcelas 			= $transaction->getInstallmentCount();
						$pagSeguroShipping 	= $transaction->getShipping();
						$codigo_transacao	= $transaction->getCode(); 
						  
						$this->load->model('checkout/order');
						$order = $this->model_checkout_order->getOrder($id_pedido[0]);
						
						// Obtendo o tipo de pagamento escolhido
						$payment_code = $paymentMethod->getCode();
    		    		$comment = "Código da transação: " . $codigo_transacao . "\nTipo de pagamento: " . $payment_code->getTypeFromValue()."\nParcelas: ".$parcelas . "\n";
    		    		
    		    		// Obtendo o tipo e o valor do frete
						$pagSeguroShippingType = $pagSeguroShipping->getType(); 
						$valor_frete = $pagSeguroShipping->getCost();
						
						// Valor 1: Pac, valor 2: Sedex, valor 3: não especificado ou cálculo não realizado pelo PagSeguro
    		    		if($pagSeguroShippingType->getValue() != 3){
    		    			$comment .= "\nTipo de frete escolhido no PagSeguro: " . $pagSeguroShippingType->getTypeFromValue() . "\nValor do frete: " . $this->currency->format($valor_frete, $order['currency_code'], false, false);
    		    		}
	    
					    $update_status_alert = false;
					    if($this->config->get('pagseguro_update_status_alert')){
					    	$update_status_alert = true;
					    }
					    
						switch($transactionStatus->getTypeFromValue()){
				
							case 'WAITING_PAYMENT':
								if($order['order_status_id'] != $this->config->get('pagseguro_order_aguardando_pagamento')){
									$this->model_checkout_order->addOrderHistory($id_pedido[0], $this->config->get('pagseguro_order_aguardando_pagamento'), $comment, $update_status_alert);
								}
								break;
											
							case 'IN_ANALYSIS':
								if($order['order_status_id'] != $this->config->get('pagseguro_order_analise')){
									$this->model_checkout_order->addOrderHistory($id_pedido[0], $this->config->get('pagseguro_order_analise'), $comment, $update_status_alert);
								}
								break;
							
							case 'PAID':
								if($order['order_status_id'] != $this->config->get('pagseguro_order_paga')){
									$this->model_checkout_order->addOrderHistory($id_pedido[0], $this->config->get('pagseguro_order_paga'), $comment, $update_status_alert);
								}
								break;
							case 'AVAILABLE':
								//if($order['order_status_id'] != $this->config->get('pagseguro_order_disponivel')){
								//	$this->model_checkout_order->addOrderHistory($id_pedido[0], $this->config->get('pagseguro_order_disponivel'), '', false);
								//}
								break;
							case 'IN_DISPUTE':
								if($order['order_status_id'] != $this->config->get('pagseguro_order_disputa')){
									$this->model_checkout_order->addOrderHistory($id_pedido[0], $this->config->get('pagseguro_order_disputa'), $comment, $update_status_alert);
								}
								break;
							case 'REFUNDED':
								if($order['order_status_id'] != $this->config->get('pagseguro_order_devolvida')){
									$this->model_checkout_order->addOrderHistory($id_pedido[0], $this->config->get('pagseguro_order_devolvida'), $comment, $update_status_alert);
								}
								break;
							case 'CANCELLED':
								if($order['order_status_id'] != $this->config->get('pagseguro_order_cancelada')){
									$this->model_checkout_order->addOrderHistory($id_pedido[0], $this->config->get('pagseguro_order_cancelada'), $comment, $update_status_alert);
								}
								break;																																
						}						
			    		
			    	} catch (PagSeguroServiceException $e) {
						$this->log->write('PagSeguro :: ' . $e->getOneLineMessage());
			    	}					
					break;
				
				default:
					$this->log->write('PagSeguro :: tipo de notificação desconhecido ['.$notificationType->getValue().']');
			}    		
    	}
    	else{
    		$this->log->write('PagSeguro :: Parâmetros de notificação retornado pelo PagSeguro são inválidos.');
    	}	
	}
	
	private function getPesoEmGramas($weight_class_id, $peso){
		
		if($this->weight->getUnit($weight_class_id) == 'g'){
			return $peso;
		}
		return $peso * 1000;
	}
}
?>
