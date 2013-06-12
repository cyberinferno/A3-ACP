<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller
{
	public function index()
	{
		$data['server'] = $this->config->item('server');
		if($this->session->userdata('user') && $this->session->userdata('char'))
		{
			$data['user'] = $this->session->userdata('user');
			$data['char'] = $this->session->userdata('char');
			$data['fcoins'] = $this->session->userdata('fcoins');
			$this->load->view('acp_view', $data);
		}
		else if($this->session->userdata('user'))
		{
			$chars = $this->dbcalls->get_chars($this->session->userdata('user'));
			if($chars == 'NIL')
			{
				$this->session->sess_destroy();
				$url = base_url();
				header("Location:$url");
			}
			else
			{
				$data['chars'] = $chars;
				$this->load->view('login_view', $data);
			}
		}
		else
			$this->load->view('login_view', $data);
	}
	
	public function login($username, $password)
	{
		if(isset($username) && isset($password))
		{
			$username = preg_replace("/[^A-Za-z0-9]/", "", base64_decode($username));
			$password = preg_replace("/[^A-Za-z0-9]/", "", base64_decode($password));
			if($this->dbcalls->check_login($username, $password))			
				echo json_encode(array('RESULT' => 'SUCCESS'));
			else
				echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Invalid username/password'));
		}
		else
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Incomplete data'));
	}
	
	public function select($char)
	{
		if($this->session->userdata('user'))
		{
			if(isset($char))
			{
				$char = preg_replace("/[^A-Za-z0-9]-/", "", base64_decode($char));
				if($this->dbcalls->select_char($this->session->userdata('user'), $char))
					echo json_encode(array('RESULT' => 'SUCCESS'));
				else
					echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Please try again!'));
			}
			else
				echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Incomplete data'));
		}
		else
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Not logged in!'));
	}
	
	public function logout()
	{
		$this->session->sess_destroy();
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		header('Pragma: no-cache');
		$url = base_url();
		header("Location:$url");
	}
	
	public function change_char()
	{
		$this->session->unset_userdata('char');
		$url = base_url();
		header("Location:$url");
	}

	public function forgot_pass()
	{
		$data['server'] = $this->config->item('server');
		$this->load->view('forgot_view', $data);
	}

	public function do_forgot($username)
	{
		$npasswd = substr(sha1(uniqid(rand(),true)), 0, 10);
		$username = preg_replace("/[^A-Za-z0-9]/", "", $username);
		$result = $this->dbcalls->set_pass($username, $npasswd);
		if($result != 'NIL')
		{
			$this->load->library('email');
			$this->email->from('noreply@server.com', 'Webmaster server');
			$this->email->reply_to('support@server.com', 'server Support');
			$this->email->to($result); 
			$this->email->subject('Password Retrieval');
			$this->email->message('Dear player,<br><br>As you had forgotten your password we took our time to update your account with a randomly generated password. Account details are as follows:<br><br><b>Username:</b> '.$username.'<br><b>Password:</b> '.$npasswd.'<br><br>Please change your password as soon as possible using our change password service!<br><br>Regards,<br>Server Team');
			$this->email->send();
			echo json_encode(array('RESULT' => 'SUCCESS'));
		}			
		else
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Invalid username!'));
	}

	public function get_boh()
	{
		$data = $this->dbcalls->get_boh();
		if($data == 'NIL')
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'No Players found!'));
		else
			echo json_encode(array('RESULT' => 'SUCCESS', 'DATA' => $data));
	}

	public function get_online_players()
	{
		$data = $this->dbcalls->get_online_players();
		if($data == 'NIL')
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'No Players found!'));
		else
			echo json_encode(array('RESULT' => 'SUCCESS', 'DATA' => $data));
	}

	public function get_pk()
	{
		$data = $this->dbcalls->get_pk();
		if($data == 'NIL')
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'No Players found!'));
		else
			echo json_encode(array('RESULT' => 'SUCCESS', 'DATA' => $data));
	}

	public function create()
	{
		$data['server'] = $this->config->item('server');
		$this->load->view('create_view', $data);
	}

	public function do_create($user, $passwd, $name, $email, $contact, $code)
	{
		$user = preg_replace("/[^A-Za-z0-9]/", "", urldecode(base64_decode($user)));
		$passwd = preg_replace("/[^A-Za-z0-9]/", "", urldecode(base64_decode($passwd)));
		$name = preg_replace("/[^A-Za-z0-9]/", "", $name);
		$code = preg_replace("/[^A-Za-z0-9]/", "", $code);
		$contact = preg_replace("/[^0-9]/", "", $contact);
		if($code == $this->session->userdata('security_code'))
		{
			if(filter_var($email, FILTER_VALIDATE_EMAIL))
			{
				$this->session->unset_userdata('security_code');
				if($this->dbcalls->create_account($user, $passwd, $name, $email, $contact))
				{
					$act_id = $this->session->userdata('act_id');
					$this->session->unset_userdata('act_id');
					$this->load->library('email');
					$this->email->from('noreply@server.com', 'Webmaster server');
					$this->email->reply_to('support@server.com', 'server Support');
					$this->email->to($email);
					$this->email->subject('Welcome to server episode 5 server!');
					$this->email->message('Dear '. $name .',<br><br>server Team appreciates your interest and welcomes you to play our server.Your login details are as follows:<br><br><b>Username:</b> '.$user.'<br><b>Password:</b> '.$passwd.'<br><br>Please activate your account by following the link below.<br><a href="'.base_url().'index.php/main/activate/'.$act_id.'">'.base_url().'index.php/main/activate/'.$act_id.'</a><br><br>Regards,<br>server Team');
					$this->email->send();					
					echo json_encode(array('RESULT' => 'SUCCESS'));
				}
				else
				{
					echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => $this->session->userdata('create_error')));
					$this->session->unset_userdata('security_code');
				}
			}
			else
				echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Invalid E-mail address'));
			$this->session->unset_userdata('create_error');
		}
		else
			echo json_encode(array('RESULT' => 'FAILURE', 'REASON' => 'Invalid security code!'));
	}

	public function activate($act_id)
	{
		$act_id = preg_replace("/[^A-Za-z0-9]/", "", $act_id);
		if($this->dbcalls->activate($act_id))
			echo "<center><b>Account activation successful!</b></center>";
		else
			echo "<center><b>Either your account is already active or activation code is invalid!</b></center>";
	}
}

/* End of file main.php */
/* Location: ./application/controllers/main.php */
?>
