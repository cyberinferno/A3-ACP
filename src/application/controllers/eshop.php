<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Eshop extends CI_Controller
{
	public function index()
	{
		$data['server'] = $this->config->item('server');
		if($this->session->userdata('user') && $this->session->userdata('char'))
		{
			$data['user'] = $this->session->userdata('user');
			$data['char'] = $this->session->userdata('char');
			$data['credits'] = $this->session->userdata('credits');
			$this->load->view('main_view', $data);
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
}
?>