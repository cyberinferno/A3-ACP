<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_utils extends CI_Controller
{
	function Admin_utils() 
	{
		parent::__construct();
		if(!$this->session->userdata('admin'))
			die(json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'No access')));
	}
	
	public function add_credits($char, $credits)
	{
		$char = preg_replace("/[^A-Za-z0-9]/", "", $char);
		$credits = preg_replace("/[^0-9]/", "", $credits);
		$result = $this->dbcalls->add_credits($char, $credits);
		if(!$result)
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Could not update database'));
		else
			echo json_encode(array('RESULT' => 'SUCCESS'));
	}
	
	public function add_items($item_name, $item_cat, $item_code, $items_count, $item_pic, $buy_credits, $rent_credits = 0)
	{
		$items_count = preg_replace("/[^0-9]/", "", $items_count);
		$buy_credits = preg_replace("/[^0-9]/", "", $buy_credits);
		$rent_credits = preg_replace("/[^0-9]/", "", $rent_credits);
		$item_pic = urldecode(base64_decode($item_pic));
		$item_code = urldecode(base64_decode($item_code));
		$result = $this->dbcalls->add_items($item_name, $item_cat, $item_code, $items_count, $item_pic, $buy_credits, $rent_credits);
		if(!$result)
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Could not update database'));
		else
			echo json_encode(array('RESULT' => 'SUCCESS'));
	}
}

?>