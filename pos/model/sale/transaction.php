<?php
class ModelSaleTransaction extends Model {
	public function getTransactions($data = array()) {
		$sql = "SELECT *,u.username as username FROM `"  . DB_PREFIX . "pos_transaction` p  LEFT JOIN  `"  . DB_PREFIX ."user` u ON p.user_id = u.user_id";
		
		if(isset($data['store_id']))
		{
			$sql .= " WHERE store_id = '".(int)$data['store_id']."'";
		}
		$sql .= " ORDER BY date_created DESC";
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		$query = $this->db->query($sql);
		
		return $query->rows;
	}
	
	public function addTransaction($data=array())
	{
		$this->db->query("INSERT INTO `" . DB_PREFIX . "pos_transaction` SET `type` = '" . $this->db->escape($data['type']) . "', `store_id` = '" . (int)$data['store_id'] . "',user_id='".$this->user->getId()."', money_in = '" . (float)$data['money_in'] . "', money_out = '" . (float)$data['money_out'] . "',`comment` ='".$this->db->escape($data['comment'])."',`date_created` = NOW()");
		$transaction_id = $this->db->getLastId();
		return $transaction_id;
	}
	
	public function getTotalTransactions($store_id = '')
	{
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "pos_transaction`";
		if($store_id !='')
		{
			$sql .= " WHERE store_id=".(int)$store_id;
		}
		$query = $this->db->query($sql);
		
		return $query->row['total'];
	}
	
	public function getTotal($store_id = '')
	{
		$sql = "SELECT (SUM(money_in) - SUM(money_out)) AS total FROM `" . DB_PREFIX . "pos_transaction`";
		if($store_id !='')
		{
			$sql .= " WHERE store_id=".(int)$store_id;
		}
		$query = $this->db->query($sql);
		
		return $query->row['total'];
	}
}