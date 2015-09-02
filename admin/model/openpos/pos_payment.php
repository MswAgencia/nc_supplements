<?php
class ModelOpenposPosPayment extends Model {
	function addPayment($store_id,$data)
	{
		$this->db->query("DELETE  FROM `" . DB_PREFIX . "pos_payment` WHERE `store_id` ='".$store_id."'");
		foreach($data as $payment)
		{
			$this->db->query("INSERT INTO `" . DB_PREFIX . "pos_payment` SET `payment_name` = '" . $payment['payment_name'] . "', `store_id` = '" . $store_id . "', `is_cash` = '" . $payment['is_cash'] . "'");
		}
	}
	
	function getPayment($store_id)
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pos_payment` WHERE `store_id` ='".$store_id."'");
		return $query->rows;
	}
}