<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Acp_utils extends CI_Controller
{
	function Acp_utils() 
	{
		parent::__construct();
		if(!$this->session->userdata('char'))
			die(json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Not logged in or not selected character')));
	}

	public function char_rb()
	{
		if($this->dbcalls->char_rb($this->session->userdata('char')))
			echo json_encode(array('RESULT' => 'SUCCESS'));
		else
		{
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => $this->session->userdata('char_rb_error')));
			$this->session->unset_userdata('char_rb_error');
		}
	}

	public function merc_rb($merc)
	{
		$merc = preg_replace("/[^A-Za-z0-9]/", "", $merc);
		if($this->dbcalls->merc_rb($this->session->userdata('char'), $merc))
			echo json_encode(array('RESULT' => 'SUCCESS'));
		else
		{
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => $this->session->userdata('merc_rb_error')));
			$this->session->unset_userdata('merc_rb_error');
		}
	}

	public function new_gift()
	{
		if($this->dbcalls->new_gift($this->session->userdata('char')))
			echo json_encode(array('RESULT' => 'SUCCESS'));
		else
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Character is either online or has taken the gift before!'));
	}

	public function offline_tp()
	{
		if($this->dbcalls->offline_tp($this->session->userdata('char')))
			echo json_encode(array('RESULT' => 'SUCCESS'));
		else
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Character is either online or update has failed!'));
	}

	public function change_pass($opasswd, $npasswd)
	{
		$opasswd = preg_replace("/[^A-Za-z0-9]/", "", urldecode(base64_decode($opasswd)));
		$npasswd = preg_replace("/[^A-Za-z0-9]/", "", urldecode(base64_decode($npasswd)));
		$result = $this->dbcalls->change_pass($this->session->userdata('char'), $this->session->userdata('user'), $opasswd, $npasswd);
		if($result != 'NIL')
		{
			$this->load->library('email');
			$this->email->from('webmaster@server.com', 'Webmaster server');
			$this->email->reply_to('support@server.com', 'server Support');
			$this->email->to($result); 
			$this->email->subject('Your password has been changed');
			$this->email->message('Dear '.$this->session->userdata('user').',<br><br>Your password has been successfully updated. Your new password is <b>'.$npasswd.'</b>.<br><br>From now on please use this password to login in game as well as in ACP. <br>For your information the IP from which we received this request was '.$_SERVER['REMOTE_ADDR'].' . If you have not updated the password then please contact A3Flamez admins.<br><br>Regards,<br>A3Flamez Team');
			$this->email->send();
			echo json_encode(array('RESULT' => 'SUCCESS'));
		}
		else
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Character is either online or invalid old password!'));
	}

	public function check_deals()
	{
		$data = $this->dbcalls->check_deals($this->session->userdata('char'), $this->session->userdata('user'));
		if($data == 'NIL')
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'No deals found!'));
		else
			echo json_encode(array('RESULT' => 'SUCCESS', 'DATA' => $data));
	}

	public function my_deals()
	{
		$data = $this->dbcalls->my_deals($this->session->userdata('char'), $this->session->userdata('user'));
		if($data == 'NIL')
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'No deals found!'));
		else
			echo json_encode(array('RESULT' => 'SUCCESS', 'DATA' => $data));
	}

	public function buy_from_mart($deal_id)
	{
		$deal_id = preg_replace("/[^0-9]/", "", $deal_id);
		if($this->dbcalls->buy_from_mart($this->session->userdata('char'), $this->session->userdata('user'), $deal_id))
			echo json_encode(array('RESULT' => 'SUCCESS', 'FCOINS' => $this->session->userdata('fcoins')));

		else
		{
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => $this->session->userdata('buy_mart_error')));
			$this->session->unset_userdata('buy_mart_error');
		}
	}

	public function cancel_deal($deal_id)
	{
		$deal_id = preg_replace("/[^0-9]/", "", $deal_id);
		if($this->dbcalls->cancel_deal($this->session->userdata('char'), $this->session->userdata('user'), $deal_id))
			echo json_encode(array('RESULT' => 'SUCCESS'));
		
		else
		{
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => $this->session->userdata('cancel_mart_error')));
			$this->session->unset_userdata('cancel_mart_error');
		}
	}

	public function post_deal($name, $fcoins)
	{
		$name = preg_replace("/[^A-Za-z0-9] ()-_/", "", urldecode(base64_decode($name)));
		$fcoins = preg_replace("/[^0-9]./", "", $fcoins);
		if($this->dbcalls->post_deal($this->session->userdata('char'), $fcoins, $name))
			echo json_encode(array('RESULT' => 'SUCCESS'));
		else
		{
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => $this->session->userdata('post_deal_error')));
			$this->session->unset_userdata('post_deal_error');
		}
	}

	public function gold_to_coins($slots)
	{
		$slots = preg_replace("/[^0-9]/", "", $slots);
		if($slots > 0 && $slots <= 21)
		{
			if($this->dbcalls->gold_to_coins($this->session->userdata('char'), $this->session->userdata('user'), $slots))
				echo json_encode(array('RESULT' => 'SUCCESS', 'TOTAL' => $this->session->userdata('fcoins')));
			else
				echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Character is either online or database update failed!'));
		}
		else
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Invalid slot count!'));
	}

	public function coins_to_gold($amount)
	{
		$amount = preg_replace("/[^0-9]/", "", $amount);
		if($amount > 0 && $amount <= 80)
		{
			if($this->dbcalls->coins_to_gold($this->session->userdata('char'), $this->session->userdata('user'), $amount))
				echo json_encode(array('RESULT' => 'SUCCESS', 'TOTAL' => $this->session->userdata('fcoins')));
			else
				echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Character is either online or no enough coins!'));
		}
		else
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Amount is too high to fit in your inventory!'));
	}

	public function wz_to_coins($wz)
	{
		$wz = preg_replace("/[^0-9]/", "", $wz);
		if($wz > 0 && $wz <= 4000000000)
		{
			if($this->dbcalls->wz_to_coins($this->session->userdata('char'), $this->session->userdata('user'), $wz))
				echo json_encode(array('RESULT' => 'SUCCESS', 'TOTAL' => $this->session->userdata('fcoins')));
			else
				echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Character is either online or no enough woonz!'));
		}
		else
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Invalid woonz amount!'));
	}

	public function rb_gift($rb)
	{
		$rb = preg_replace("/[^0-9]/", "", $rb);
		if($this->dbcalls->rebirth_gift($this->session->userdata('char'), $rb))
			echo json_encode(array('RESULT' => 'SUCCESS'));
		else
		{
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => $this->session->userdata('rb_gift_error')));
			$this->session->unset_userdata('rb_gift_error');
		}
	}

	public function buy_lore()
	{
		if($this->dbcalls->buy_lore($this->session->userdata('char')))
			echo json_encode(array('RESULT' => 'SUCCESS'));
		else
		{
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => $this->session->userdata('buy_lore_error')));
			$this->session->unset_userdata('buy_lore_error');
		} 
	}
}

?>