<?php
class ModelOpenPosTransaction extends Model {
	public function getTransactions($data = array()) {
		$sql = "SELECT *,s.name as storename,u.username as username FROM `"  . DB_PREFIX . "pos_transaction` p LEFT JOIN  `"  . DB_PREFIX ."store` s ON p.store_id = s.store_id LEFT JOIN  `"  . DB_PREFIX ."user` u ON p.user_id = u.user_id";
		$where = array();
		if(isset($data['store_id']) and $data['store_id'] != 'all' )
		{
			$where[] = "p.store_id=".(int)$data['store_id'];
		}
		if(isset($data['user_id']) and $data['user_id'] != 'all' )
		{
			$where[] = "p.user_id=".(int)$data['user_id'];
		}

		if( !empty($where))
		{
			$sql .= " WHERE ".implode(' AND ',$where);
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
		$this->db->query("INSERT INTO `" . DB_PREFIX . "pos_transaction` SET `type` = '" . $this->db->escape($data['type']) . "', `store_id` = '" . (int)$data['store_id'] . "', money_in = '" . (float)$data['money_in'] . "', money_out = '" . (float)$data['money_out'] . "',`comment` ='".$this->db->escape($data['comment'])."',`date_created` = NOW()");
		$transaction_id = $this->db->getLastId();
		return $transaction_id;
	}

	public function getTotalTransactions($store_id = '',$user_id = '')
	{
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "pos_transaction`";
		$where = array();
		if($store_id !='' and $store_id != 'all' )
		{
			$where[] = "store_id=".(int)$store_id;
		}
		if($user_id !='' and $user_id != 'all' )
		{
			$where[] = "user_id=".(int)$user_id;
		}

		if( !empty($where))
		{
			$sql .= " WHERE ".implode(' AND ',$where);
		}
		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotal($store_id = '',$user_id = '')
	{
		$sql = "SELECT (SUM(money_in) - SUM(money_out)) AS total FROM `" . DB_PREFIX . "pos_transaction`";

		$where = array();
		if($store_id !='' and $store_id != 'all' )
		{
			$where[] = "store_id=".(int)$store_id;
		}
		if($user_id !='' and $user_id != 'all' )
		{
			$where[] = "user_id=".(int)$user_id;
		}

		if( !empty($where))
		{
			$sql .= " WHERE ".implode(' AND ',$where);
		}
		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTransactionUser()
	{
		$sql = "SELECT DISTINCT ts.user_id, u.username FROM `" . DB_PREFIX . "pos_transaction` ts LEFT JOIN `" . DB_PREFIX . "user` u ON ts.user_id = u.user_id ";
		$query = $this->db->query($sql);
		return $query->rows;
	}
}