<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Utils extends CI_Controller
{
	function Utils() 
	{
		parent::__construct();
		if(!$this->session->userdata('char'))
			die(json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Not logged in or not selected character')));
	}
	
	public function get_category($cat)
	{
		if(isset($cat))
		{
			$cat = preg_replace("/[^A-Za-z0-9]/", "", base64_decode($cat));
			$data = $this->dbcalls->get_category_data($cat);
			if($data == 'NIL')
				echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Category not found!'));
			else
				echo json_encode(array('RESULT' => 'SUCCESS', 'DATA' => $data));
		}
		else
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Incomplete data'));
	}
	
	public function get_history()
	{
		$data = $this->dbcalls->get_eshop_history($this->session->userdata('char'));
		if($data == 'NIL')
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'No deliveries done!'));
		else
			echo json_encode(array('RESULT' => 'SUCCESS', 'DATA' => $data));
	}
	
	public function get_cart()
	{
		$data = $this->dbcalls->get_cart($this->session->userdata('char'));
		if($data == 'NIL')
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Cart is empty!'));
		else
			echo json_encode(array('RESULT' => 'SUCCESS', 'DATA' => $data));
	}
	
	public function add_item($item_id, $quantity = 1)
	{
		$item_id = preg_replace("/[^A-Za-z0-9]/", "", $item_id);
		$quantity = preg_replace("/[^0-9]/", "", $quantity);
		if($this->dbcalls->add_to_cart($this->session->userdata('char'), $item_id, $quantity))
			echo json_encode(array('RESULT' => 'SUCCESS'));
		else
		{
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => $this->session->userdata('add_cart_error')));
			$this->session->unset_userdata('add_cart_error');
		}
	}
	
	public function remove_item($item_id, $quantity = 1)
	{
		$item_id = preg_replace("/[^A-Za-z0-9]/", "", $item_id);
		$quantity = preg_replace("/[^0-9]/", "", $quantity);
		if($this->dbcalls->remove_from_cart($this->session->userdata('char'), $item_id, $quantity))
			echo json_encode(array('RESULT' => 'SUCCESS'));
		else
		{
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => $this->session->userdata('remove_cart_error')));
			$this->session->unset_userdata('remove_cart_error');
		}
	}
	
	public function deliver()
	{
		if($this->dbcalls->deliver_items($this->session->userdata('user'), $this->session->userdata('char')))
			echo json_encode(array('RESULT' => 'SUCCESS', 'CREDITS' => $this->session->userdata('credits')));
		else
		{
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => $this->session->userdata('deliver_items_error')));
			$this->session->unset_userdata('deliver_items_error');
		}
	}
	
	public function clear_cart()
	{
		if($this->dbcalls->clear_cart($this->session->userdata('char')))
			echo json_encode(array('RESULT' => 'SUCCESS'));
		else
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Could not process request. Please try again later!'));
	}
	
	public function convert_time()
	{
		$result = $this->dbcalls->convert_time($this->session->userdata('char'));
		echo json_encode(array('RESULT' => 'SUCCESS', 'CREDITS' => $result, 'TOTAL' => $this->session->userdata('credits')));
	}
	
	public function convert_wz($wz)
	{
		$wz = preg_replace("/[^0-9]/", "", $wz);
		$result = $this->dbcalls->convert_wz($this->session->userdata('char'), $wz);
		echo json_encode(array('RESULT' => 'SUCCESS', 'CREDITS' => $result, 'TOTAL' => $this->session->userdata('credits')));
	}
	
	public function convert_gold($slots)
	{
		$slots = preg_replace("/[^0-9]/", "", $slots);
		if($slots > 0 && $slots <= 21)
		{
			$slots = preg_replace("/[^0-9]/", "", $slots);
			$result = $this->dbcalls->convert_wz($this->session->userdata('char'), $slots);
			echo json_encode(array('RESULT' => 'SUCCESS', 'CREDITS' => $result, 'TOTAL' => $this->session->userdata('credits')));
		}
		else
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Invalid slot count!'));
	}
	
	public function get_time()
	{
		$data = $this->dbcalls->get_time($this->session->userdata('char'));
		if($data == 'NIL')
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Time not found!'));
		else
			echo json_encode(array('RESULT' => 'SUCCESS', 'DATA' => $data));
	}

	public function get_coupons()
	{
		$data = $this->dbcalls->get_coupons($this->session->userdata('char'));
		if($data == 'NIL')
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'No coupons found!'));
		else
			echo json_encode(array('RESULT' => 'SUCCESS', 'DATA' => $data));
	}

	public function apply_coupon($coupon)
	{
		$coupon = preg_replace("/[^A-Za-z0-9]/", "", $coupon);
		if($this->dbcalls->apply_coupon($coupon, $this->session->userdata('char')))
			echo json_encode(array('RESULT' => 'SUCCESS'));
		else
		{
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => $this->session->userdata('apply_coupon_error')));
			$this->session->unset_userdata('apply_coupon_error');
		}
	}

	public function remove_coupon()
	{
		if($this->dbcalls->remove_coupon($this->session->userdata('char')))
			echo json_encode(array('RESULT' => 'SUCCESS'));
		else
		{
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => $this->session->userdata('remove_coupon_error')));
			$this->session->unset_userdata('remove_coupon_error');
		}
	}
}

?>