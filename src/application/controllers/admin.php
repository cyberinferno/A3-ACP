<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller
{
	public function index()
	{
		$data['server'] = $this->config->item('server');
		if($this->session->userdata('admin'))
		{
			$data['user'] = $this->session->userdata('user');
			$data['char'] = $this->session->userdata('char');
			$this->load->view('admin_view', $data);
		}
		else
		{
			$url = base_url();
			header("Location:$url");
		}
	}
}

?>