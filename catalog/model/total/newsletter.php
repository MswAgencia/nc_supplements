<?php
class ModelTotalNewsletter extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {

		$user_news = $this->customer->getNewsletter();

		if ($user_news == 1 && $this->config->get('newsletter_status')) {

					$this->language->load('total/newsletter');
					
					$sub_total = $this->cart->getSubTotal();
					$discount = $this->config->get('newsletter_discount');
					$type = $this->config->get('newsletter_type');
					$value = $this->config->get('newsletter_discount');

					if (isset($discount)) {
						if ($type == 1) {
							$sub_total =  $value;
						}else{
							$sub_total =  $sub_total * ($value / 100);
						}
					} else {
							$sub_total =  $sub_total * 0.02;
					}

					$total_data[] = array( 
						'code'       => 'newsletter',
						'title'      => $this->language->get('text_total'),
						'text'       => $this->currency->format($sub_total),
						'value'      => $sub_total,
						'sort_order' => $this->config->get('newsletter_sort_order')
					);
					
					$total += $sub_total;
		}

	}
}
?>