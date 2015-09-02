<?php
class ModelOpenposPosProduct extends Model {

	function addProductMeta($product_id,$meta_key,$meta_value)
	{
		$this->db->query("DELETE  FROM `" . DB_PREFIX . "pos_product_meta` WHERE `product_id` ='".$product_id."' AND `meta_key`='".$meta_key."'");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "pos_product_meta` SET `product_id` = '" . $product_id . "', `meta_key` = '" . $meta_key . "', `meta_value` = '" . $meta_value . "'");
	}
	
	function isDummy($product_id)
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pos_product_meta` WHERE `product_id` ='".$product_id."' AND `meta_key`='dummy'");
		$rs = $query->row;
		
		if(isset($rs['meta_value']) and $rs['meta_value'] == 1)
		{
			return true;
		}else{
			return false;
		}
	}
}