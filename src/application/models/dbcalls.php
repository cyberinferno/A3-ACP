<?php
class Dbcalls extends CI_Model
{
	public $con;
	public $conhs;
	public $cones;
	
	function Dbcalls()
	{
		parent::__construct();
		$this->con = odbc_connect($this->config->item('char_db'), $this->config->item('db_username') , $this->config->item('db_password')) or die('Could not connect to Character database!');
		$this->conhs = odbc_connect($this->config->item('merc_db'), $this->config->item('db_username') , $this->config->item('db_password')) or die('Could not connect to mercenery database!');
		$this->cones = odbc_connect($this->config->item('eshop_db'), $this->config->item('db_username') , $this->config->item('db_password')) or die('Could not connect to E-shop database!');
		if($this->config->item('logging'))
			$this->conls = odbc_connect($this->config->item('log_db'), $this->config->item('db_username') , $this->config->item('db_password')) or die('Could not connect to Log database!');
	}
	
	public function check_login($username, $password)
	{
		$result = odbc_exec ($this->con, "select * from account where c_id = '$username' and c_headera = '$password'");
		if(!$result)
			return false;
		else
		{
			if(odbc_num_rows($result) == 0)
				return false;
			else
			{
				$this->session->set_userdata('user', $username);
				$ip = $this->session->userdata('ip_address');
				$result1 = odbc_exec($this->con, "select ip, flamez_coins from AccountInfo where account = '$username'");
				$present_ip = trim(odbc_result($result1,'ip'));
				$flamez_coins = (int)odbc_result($result1, 'flamez_coins');
				$this->session->set_userdata('fcoins', $flamez_coins);
				if($present_ip == '127.0.0.1')
					$result2 = odbc_exec($con, "update AccountInfo set ip = '$ip', where account = '$username'");
				$result3 = odbc_exec($this->con, "update AccountInfo set login_ip = '$ip' where account = '$username'");
				if(in_array($username, $this->config->item('admins')))
					$this->session->set_userdata('admin', 1);
				return true;
			}
		}
	}
	
	public function get_chars($username)
	{
		$result = odbc_exec ($this->con, "select c_id from charac0 where c_sheadera = '$username' and c_status = 'A'");
		if(!$result || odbc_num_rows($result) == 0)
			return 'NIL';
		else
		{
			$chars = array();
			while(odbc_fetch_row($result))
			{
				$char = odbc_result($result, 'c_id');
				array_push($chars, $char);
			}
			return $chars;
		}
	}
	
	public function select_char($username, $char)
	{
		$result = odbc_exec ($this->con, "select c_id from charac0 where c_sheadera = '$username' and c_id = '$char' and c_status = 'A'");
		if(!$result || odbc_num_rows($result) == 0)
			return false;
		else
		{
			$result1 = odbc_exec ($this->cones, "select credits from credits_table where char_name = '$char'");
			if(!$result1 || odbc_num_rows($result1) == 0)
				$credits = 0;
			else
				$credits = (int)odbc_result($result1, 'credits');
			$this->session->set_userdata('char', $char);
			$this->session->set_userdata('credits', $credits);
			return true;
		}
	}
	
	public function get_category_data($cat)
	{
		$data = array();
		$sql = "select * from item_table where item_category = '$cat' order by item_id";	
		$result = odbc_exec($this->cones,$sql);
		$num = odbc_num_rows($result);
		if($num == 0)
			return "NIL";
		else
		{
			while (odbc_fetch_row($result))
			{
				$item_id = odbc_result($result, 'item_id');
				$item_name = urldecode(odbc_result($result, 'item_name'));
				$buy_credits = odbc_result($result, 'buy_credits');
				$item_pic = odbc_result($result, 'item_pic');
				array_push($data, array('item_id' => $item_id, 'item_name' => $item_name, 'item_pic' => $item_pic, 'buy_credits' => $buy_credits));
			}
			return $data;
		}
	}
	
	public function get_eshop_history($char)
	{
		$data = array();
		$sql = "select * from delivery_table where char_name = '$char'";
		$result = odbc_exec($this->cones, $sql);
		$num = odbc_num_rows($result);
		if($num == 0)
			return "NIL";
		else
		{
			while (odbc_fetch_row($result))
			{
				$transaction_id = odbc_result($result, 'transaction_id');
				$ip_address = odbc_result($result, 'ip_address');
				$credits_used = odbc_result($result, 'credits_used');
				$item_ids = explode(";", odbc_result($result, 'item_ids'));
				$items = array();
				for($j = 0; $j < count($item_ids); $j++)
				{
					$temp1 = explode(":", $item_ids[$j]);
					$sql1 = "select item_name from item_table where item_id = $temp1[0]";
					$result1 = odbc_exec($this->cones, $sql1);
					$item_name = urldecode(odbc_result($result1, 'item_name'));
					$item_quantity = $temp1[1];
					$items[$j] = array('item_id' => $temp1[0], 'item_name' => $item_name, 'item_quantity' => $item_quantity);
				}
				$delivery_time = odbc_result($result, 'delivery_time');
				array_push($data, array('transaction_id' => $transaction_id, 'ip_address' => $ip_address, 'items' => $items, 'credits_used' => $credits_used, 'delivery_time' => $delivery_time));
			}
			return $data;
		}
	}
	
	public function get_cart($char)
	{
		$data = array();
		$sql = "select item_ids, credits_required, coupon_code from shopping_cart where char_name = '$char'";
		$result = odbc_exec($this->cones, $sql);
		$num = odbc_num_rows($result);
		if($num == 0)
			return 'NIL';
		else
		{
			$temp = array();
			$data['credits_required'] = odbc_result($result, 'credits_required');
			$data['coupon'] = odbc_result($result, 'coupon_code');
			$item_ids = explode(";", odbc_result($result, 'item_ids'));
			for($i = 0; $i < count($item_ids); $i++)
			{
				$temp1 = explode(":", $item_ids[$i]);
				$item_id = (int)$temp1[0];
				$sql1 = "select item_name,buy_credits,item_pic from item_table where item_id = $item_id";
				$result1 = odbc_exec($this->cones, $sql1);
				$item_name = urldecode(odbc_result($result1, 'item_name'));
				$buy_credits = odbc_result($result1, 'buy_credits');
				$item_pic = odbc_result($result1, 'item_pic');
				$quantity = $temp1[1];
				$items[$i] = array('item_name' => $item_name, 'item_id' => $item_id, 'item_pic' => $item_pic, 'quantity' => $quantity, 'buy_credits' => $buy_credits);
			}
			$data['items'] = $items;
			return $data;
		}
	}
	
	public function add_to_cart($char, $item_id, $quantity)
	{
		$sql = "select buy_credits from item_table where item_id = $item_id";
		$result = odbc_exec($this->cones, $sql);
		$num = odbc_num_rows($result);
		if($num == 0)
		{
			$this->session->set_userdata('add_cart_error', 'Invalid item');
			return false;
		}
		else
		{
			$buy_credits = odbc_result($result, 'buy_credits');
			$sql1 = "select * from shopping_cart where char_name = '$char'";
			$result1 = odbc_exec($this->cones, $sql1);
			$num1 = odbc_num_rows($result1);
			if($num1 == 0)
			{
				$new_credits = $buy_credits*$quantity;
				$str = "$item_id:$quantity";
				$sql2 = "insert into shopping_cart(char_name, item_ids, credits_required) values('$char', '$str', $new_credits)";
				$result2 = odbc_exec($this->cones, $sql2);
				return true;
			}
			else
			{
				$found = 0;
				$new_str = "";
				$cur_str = odbc_result($result1, 'item_ids');
				$coupon = odbc_result($result1, 'coupon_code');
				if($coupon == 'NIL')
				{
					$temp = explode(";", $cur_str);
					for($i = 0; $i < count($temp); $i++)
					{
						$temp1 = explode(":", $temp[$i]);
						$itid = (int)$temp1[0];
						$quan = (int)$temp1[1];
						if($itid == $item_id)
						{
							$found = 1;
							$nquan = $quan + $quantity;
							$temp1[1] = "$nquan";
						}
						if($i != 0)
							$new_str = $new_str.";".$temp1[0].":".$temp1[1];
						else
							$new_str = $temp1[0].":".$temp1[1];
					}
					$credits_required = odbc_result($result1, 'credits_required');
					if($found == 0)
					{
						$str = "$item_id:$quantity";
						$new_str = $cur_str.";$str";
					}
					$new_credits = $credits_required + $buy_credits*$quantity;
					$sql2 = "update shopping_cart set item_ids = '$new_str', credits_required = '$new_credits' where char_name='$char'";
					$result2 = odbc_exec($this->cones, $sql2);
					return true;
				}
				else
				{
					$this->session->set_userdata('add_cart_error', 'Please remove discount coupon before adding item!');
					return false;
				}
			}
		}
	}
	
	public function remove_from_cart($char, $item_id, $quantity)
	{
		$sql = "select * from shopping_cart where char_name = '$char'";
		$result = odbc_exec($this->cones, $sql);
		$num = odbc_num_rows($result);
		if($num == 0)
		{
			$this->session->set_userdata('remove_cart_error', 'Shopping cart empty');
			return false;
		}
		else
		{
			$coupon = odbc_result($result, 'coupon_code');
			if($coupon == 'NIL')
			{
				$sql2 = "select buy_credits from item_table where item_id = $item_id";
				$result2 = odbc_exec($this->cones, $sql2);
				$price = (int)odbc_result($result2, 'buy_credits');
				$cur_credit = (int)odbc_result($result, 'credits_required');
				$item_ids = explode(";", odbc_result($result, 'item_ids'));
				$found = 0;
				$new_str = "";
				for($i = 0; $i < count($item_ids); $i++)
				{
					$temp = explode(":", $item_ids[$i]);
					if($temp[0] == "$item_id")
					{
						$found = 1;
						if($quantity > (int)$temp[1] || $quantity == (int)$temp[1])
						{
							$quantity = (int)$temp[1];
							continue;
						}
						else
						{
							$temp[1] = (int)$temp[1] - $quantity;
							if($new_str == "")
								$new_str = $temp[0].":$temp[1]";
							else
								$new_str = $new_str.";".$temp[0].":$temp[1]";
						}
					}
					else if($new_str == "")
					   $new_str = $item_ids[$i];
					else
					   $new_str = $new_str.";".$item_ids[$i];    
				}
				if($found == 0)
				{
					$this->session->set_userdata('remove_cart_error', 'Item not present in cart');
					return false;
				}
				else if($found == 1 && $new_str == "")
				{
					$sql1 = "delete from shopping_cart where char_name='$char'";
					$result1 = odbc_exec($this->cones, $sql1);
					return true;
				}
				else
				{
					$new_credit = $cur_credit - $price*$quantity;
					$sql1 = "update shopping_cart set item_ids='$new_str', credits_required = '$new_credit' where char_name='$char'";
					$result1 = odbc_exec($this->cones, $sql1);
					return true;
				}
			}
			else
			{
				$this->session->set_userdata('remove_cart_error', 'Please remove discount coupon before removing item!');
				return false;
			}
		}
	}
	
	public function deliver_items($account, $char)
	{
		$sql = "select * from shopping_cart where char_name = '$char'";
		$result = odbc_exec($this->cones, $sql);
		$num = odbc_num_rows($result);
		if($num == 0)
		{
			$this->session->set_userdata('deliver_items_error', 'Shopping cart empty');
			return false;
		}
		else
		{
			$credits = $this->session->userdata('credits');
			$credits_required = odbc_result($result, 'credits_required');
			$coupon_used = odbc_result($result, 'coupon_code');
			if($credits < $credits_required)
			{
				$this->session->set_userdata('deliver_items_error', 'Not enough credits');
				return false;
			}
			else
			{
				if($this->check_online($char))
				{
					$this->session->set_userdata('deliver_items_error', 'Character is online');
					return false;
				}
				else
				{
					$temp = $this->get_char_mbody($char);
					$INVEN = explode("=",$temp[6]);
					$temp45 = explode(";", $INVEN[1]);
					if($temp45[0] != "6144")
					{
						$this->session->set_userdata('deliver_items_error', 'Keep a UJ in First Slot!');
						return false;
					}
					else
					{
						$slot = 0;
						$transaction_id = $this->get_transaction_id();
						$temp_ids = odbc_result($result, 'item_ids');
						$items = explode(";", odbc_result($result, 'item_ids'));
						for($i = 0; $i < count($items); $i++)
						{
							$t = explode(":", $items[$i]);
							$item_id = (int)$t[0];
							$quantity = (int)$t[1];
							$sql1 = "select item_code, items_count from item_table where item_id = $item_id";
							$result1 = odbc_exec($this->cones, $sql1);
							$items_count = odbc_result($result1, 'items_count');
							$item_code = odbc_result($result1, 'item_code');
							$str_slot = (string)$slot;
							if($items_count == 1)
							{
								for($k = 1; $k <= $quantity; $k++)
								{
									$rnum = $this->get_random_code();
									$result5 = odbc_exec($this->cones, "insert into buy_uniq_code(transaction_id, item_code, unique_code) values('$transaction_id', '$item_code', '$rnum')");
									$ncode = $item_code.";".$rnum;
									if($slot == 0)
										$INVEN[1] = $ncode.";".$str_slot;
									else
										$INVEN[1] = $INVEN[1].";".$ncode.";".$str_slot;
									$slot++;
									$str_slot = (string)$slot;
								}
							}
							else
							{
								$temp1 = explode(":", $item_code);
								for($j = 0; $j < count($temp1); $j++)
								{
									for($k = 1; $k <= $quantity; $k++)
									{
										$icode = $temp1[$j];
										$rnum = $this->get_random_code();
										$result5 = odbc_exec($this->cones, "insert into buy_uniq_code(transaction_id, item_code, unique_code) values('$transaction_id', '$icode', '$rnum')");
										$ncode = $icode.";".$rnum;
										if($slot == 0)
											$INVEN[1] = $ncode.";".$str_slot;
										else
											$INVEN[1] = $INVEN[1].";".$ncode.";".$str_slot;
										$slot++;
										$str_slot = (string)$slot;
									}
								}
							}
						}
						$newString = $temp[0]."\_1".$temp[1]."\_1".$temp[2]."\_1".$temp[3]."\_1".$temp[4]."\_1".$temp[5]."\_1".$INVEN[0]."=".$INVEN[1]."\_1".$temp[7]."\_1".$temp[8]."\_1".$temp[9]."\_1".$temp[10]."\_1".$temp[11]."\_1".$temp[12]."\_1".$temp[13]."\_1".$temp[14]."\_1".$temp[15]."\_1".$temp[16]."\_1".$temp[17]."\_1".$temp[18]."\_1".$temp[19]."\_1".$temp[20]."\_1".$temp[21]."\_1".$temp[22]."\_1";
						$sql2 = "update charac0 set m_body = '$newString' where c_id = '$char'";
						$result2 = odbc_exec($this->con,$sql2);
						$cdt = $this->get_current_datetime();
						$ip = $this->session->userdata('ip_address');
						if($coupon_used == 'NIL')
							$sql3 = "insert into delivery_table(transaction_id, account_name, char_name, item_ids, delivery_time, credits_used, ip_address) values('$transaction_id', '$account', '$char', '$temp_ids', '$cdt', '$credits_required', '$ip')";
						else
							$sql3 = "insert into delivery_table(transaction_id, account_name, char_name, item_ids, delivery_time, credits_used, coupon_code, ip_address) values('$transaction_id', '$account', '$char', '$temp_ids', '$cdt', '$credits_required', '$coupon_used', '$ip')";
						$result3 = odbc_exec($this->cones, $sql3);
						$rcredits = $credits - $credits_required;
						$this->session->set_userdata('credits', $rcredits);
						$sql4 = "update credits_table set credits = $rcredits where char_name = '$char'";
						$result4 = odbc_exec($this->cones, $sql4);
						$sql6 = "delete from shopping_cart where char_name='$char'";
						$result6 = odbc_exec($this->cones, $sql);
						return true;
					}
				}
			}
		}
	}
	
	public function clear_cart($char)
	{
		$result1 = odbc_exec($this->cones, "select coupon_code from shopping_cart where char_name = '$char'");
		if(odbc_num_rows($result1) != 0)
		{
			$coupon = odbc_result($result1, 'coupon_code');
			if($coupon != 'NIL')
				$result2 = odbc_exec($this->cones, "update coupon_table set flag = 0 where coupon_code = '$coupon'");
		}
		$sql = "delete from shopping_cart where char_name='$char'";
		$result = odbc_exec($this->cones, $sql);
		if(!$result)
			return false;
		else
			return true;
	}
	
	public function get_char_mbody($char)
	{
		$sql = "select m_body from charac0 where c_id = '$char'";
		$result = odbc_exec($this->con,$sql);
		$charstring = odbc_result($result, 'm_body');
		$temp = explode("\_1",$charstring);
		return $temp;
	}

	public function get_random_code()
	{
		while(true)
		{
			$rnum = mt_rand(100000, 999999);
			$sql = "select * from buy_uniq_code where unique_code = '$rnum'";
			$result = odbc_exec($this->cones, $sql);
			$num = odbc_num_rows($result);
			if($num == 0)
				break;
		}
		return (string)$rnum;
	}

	public function get_current_datetime()
	{
		date_default_timezone_set("Asia/Calcutta");
		return date('Y-m-d H:i:s');
	}

	public function get_transaction_id()
	{
		while(true)
		{
			$tid = substr(sha1(uniqid(rand(),true)),0, 10);
			$sql = "select char_name from delivery_table where transaction_id = '$tid'";
			$result = odbc_exec($this->cones, $sql);
			$num = odbc_num_rows($result);
			if($num == 0)
				break;
		}
		return $tid;
	}
	
	public function add_credits($char, $credits)
	{
		$result = odbc_exec($this->cones, "select credits from credits_table where char_name = '$char'");
		if(odbc_num_rows($result) != 0)
		{
			$cur_credits = (int)odbc_result($result, 'credits');
			$new_credits = $cur_credits + (int)$credits;
			$result1 = odbc_exec($this->cones, "update credits_table set credits = $new_credits where char_name = '$char'");
		}
		else
		{
			$result2 = odbc_exec($this->con, "select c_sheadera from charac0 where c_id = '$char'");
			$account = odbc_result($result2, 'c_sheadera');
			$result1 = odbc_exec($this->cones, "insert into credits_table(account_name, char_name, credits) values('$account', '$char', $credits)");
		}
		return $result1;
	}
	
	public function add_items($item_name, $item_cat, $item_code, $items_count, $item_pic, $buy_credits, $rent_credits)
	{
		$result = odbc_exec($this->cones, "select item_code from item_table");
		$next_id = (int)odbc_num_rows($result) + 1;
		$result1 = odbc_exec($this->cones, "insert into item_table(item_id, item_name, item_category, item_code, items_count, item_pic, buy_credits, rent_credits) values($next_id, '$item_name', '$item_cat', '$item_code', $items_count, '$item_pic', $buy_credits, $rent_credits)");
		return $result1;
	}
	
	public function convert_time($char)
	{
		$result = odbc_exec($this->con, "select * from onlinerecords where charname='$char' and used = 0");
		$total_time = 0;
		while(odbc_fetch_row($result))
		{
			$time = odbc_result($result,'online_time');
			$total_time = $total_time + $time;
		}
		if($total_time < 60)
			return 0;
		else
		{
			$total_credits = round($total_time/60);
			$result1 = $this->add_credits($char, $total_credits);
			if($result1)
			{
				$result2 = odbc_exec($this->con, "update onlinerecords set used = 1 where charname='$char' and used = 0");
				$t1 = $this->session->userdata('credits');
				$t2 = $total_credits + (int)$t1;
				$this->session->set_userdata('credits', $t2);
				return $total_credits;
			}
			else
				return 0;
		}
	}
	
	public function convert_wz($char, $wz)
	{
		if($this->check_online($char))
			return 0;
		else
		{
			$result = odbc_exec($this->con, "select c_headerc from charac0 where c_id='$char'");
			$cur_wz = (int)odbc_result($result, 'c_headerc');
			if($cur_wz < $wz*1000000000)
				return 0;
			else
			{
				$credits = $wz*10;
				$result1 = $this->add_credits($char, $credits);
				if($result1)
				{
					$rem = $cur_wz - $wz*1000000000;
					$result2 = odbc_exec($this->con, "UPDATE charac0 SET c_headerc = '$rem' WHERE c_id = '$char'");
					$t1 = $this->session->userdata('credits');
					$t2 = $credits + (int)$t1;
					$this->session->set_userdata('credits', $t2);
					return $credits;
				}
				else
					return 0;
			}
		}
	}
	
	public function convert_gold($char, $slots)
	{
		if($this->check_online($char))
			return 0;
		else
		{
			$wz = 0;
			$temp = $this->get_char_mbody($char);
			$INVEN = explode("=", $temp[6]);
			$ITEM = explode(";",$INVEN[1]);
			for($i = 0;$i < $slots;$i++)
			{
				if($ITEM[$i*4] == '9814')
					$wz++;
				else if($ITEM[$i*4] == '9933')
					$wz += 2;
				else if($ITEM[$i*4] == '9934')
					$wz += 4;
			}
			$credits = $wz*10;
			$result1 = $this->add_credits($char, $credits);
			if($result1)
			{
				$INVEN[1] = "";
				$newString = $temp[0]."\_1".$temp[1]."\_1".$temp[2]."\_1".$temp[3]."\_1".$temp[4]."\_1".$temp[5]."\_1".$INVEN[0]."=".$INVEN[1]."\_1".$temp[7]."\_1".$temp[8]."\_1".$temp[9]."\_1".$temp[10]."\_1".$temp[11]."\_1".$temp[12]."\_1".$temp[13]."\_1".$temp[14]."\_1".$temp[15]."\_1".$temp[16]."\_1".$temp[17]."\_1".$temp[18]."\_1".$temp[19]."\_1".$temp[20]."\_1".$temp[21]."\_1".$temp[22]."\_1";
				$rs10 = odbc_exec($this->con, "update charac0 set m_body = '$newString' where c_id = '$char'");
				$t1 = $this->session->userdata('credits');
				$t2 = $credits + (int)$t1;
				$this->session->set_userdata('credits', $t2);
				return $credits;
			}
			else
				return 0;
		}
	}
	
	public function get_time($char)
	{
		$result = odbc_exec($this->con, "select * from onlinerecords where charname='$char'");
		$total_time = 0;
		while(odbc_fetch_row($result))
		{
			$time = odbc_result($result,'online_time');
			$total_time = $total_time + $time;
		}
		$result1 = odbc_exec($this->con, "select * from onlinerecords where charname='$char' and used = 0");
		$total_time1 = 0;
		while(odbc_fetch_row($result1))
		{
			$time1 = odbc_result($result1,'online_time');
			$total_time1 = $total_time1 + $time1;
		}
		$result2 = odbc_exec($this->con, "select * from onlinerecords where charname='$char' and used = 1");
		$total_time2 = 0;
		while(odbc_fetch_row($result2))
		{
			$time2 = odbc_result($result2,'online_time');
			$total_time2 = $total_time2 + $time2;
		}
		if($total_time != 0)
			$total_time = $this->min2hr($total_time);
		if($total_time1 != 0)
			$total_time1 = $this->min2hr($total_time1);
		if($total_time2 != 0)
			$total_time2 = $this->min2hr($total_time2);
		return array('total_time' => $total_time, 'used_time' => $total_time2, 'rem_time' => $total_time1);
	}
	
	public function min2hr($Minutes)
	{
		if ($Minutes < 0)
			$Min = Abs($Minutes);
		else
			$Min = $Minutes;
		$iHours = Floor($Min / 60);
		$Minutes = ($Min - ($iHours * 60)) / 100;
		$tHours = $iHours + $Minutes;
		if ($Minutes < 0)
			$tHours = $tHours * (-1);
		$aHours = explode(".", $tHours);
		$iHours = $aHours[0];
		if (empty($aHours[1]))
			$aHours[1] = "00";
		$Minutes = $aHours[1];
		if (strlen($Minutes) < 2)
			$Minutes = $Minutes ."0";
		$tHours = $iHours ." hours and ". $Minutes." minutes";
		return $tHours;		
	}
	
	public function check_online($char)
	{
		$online = odbc_exec($this->con, "select * from charloginlog where c_id = '$char'");
		$num_rows = odbc_fetch_row($online);
		if($num_rows == 0)
			return false;
		else
			return true;
	}
	
	public function get_online_players()
	{
		$sql = "select * from charloginlog order by datetime";
		$rs = odbc_exec($this->con,$sql);
		$players = array();
		$towns = array();
		$rebirths = array();
		$types = array();
		$levels = array();
		while (odbc_fetch_row($rs))
		{
			$hero = odbc_result($rs, "c_id");
			$level = odbc_result($rs, "lvl");
			$rb = odbc_result($rs, "rb");
			$char_type = odbc_result($rs, "class");
			$nation = odbc_result($rs, "Nation");
			if ($char_type == '0')
				$class = "Warrior";
			else if ($char_type == '1')
				$class = "Holy Knight";
			else if ($char_type == '2')
				$class = "Mage";
			else if ($char_type == '3')
				$class = "Archer";
			if($nation == 0)
				$town = 'Temoz';
			else
				$town = 'Quanato';
			array_push($players, $hero);
			array_push($rebirths, $rb);
			array_push($towns, $town);
			array_push($types, $class);
			array_push($levels, $level);
		}
		if(count($players) == 0)
			$data = 'NIL';
		else
			$data = array('PLAYERS' => $players, 'RBS' => $rebirths, 'TOWNS' => $towns, 'TYPES' => $types, 'LEVELS' => $levels);
		return $data;
	}
	
	public function get_boh()
	{
		$result = odbc_exec($this->con, "select * from charac0 where c_status = 'A' order by cast(rb as int) desc, cast(c_sheaderc as int) desc, d_udate desc, c_id");
		$count = 0;
		$players = array();
		$rebirths = array();
		$types = array();
		$levels = array();
		while (odbc_fetch_row($result))
		{
			$count = $count + 1;
			if($count == 26)
				break;
			else
			{
				$hero = odbc_result($result, "c_id");
				$level = odbc_result($result, "c_sheaderc");
				$rb = odbc_result($result, "rb");
				$char_type = odbc_result($result, "c_sheaderb");
				if ($char_type == '0')
					$class = "Warrior";
				else if ($char_type == '1')
					$class = "Holy Knight";
				else if ($char_type == '2')
					$class = "Mage";
				else if ($char_type == '3')
					$class = "Archer";
				array_push($players, $hero);
				array_push($rebirths, $rb);
				array_push($types, $class);
				array_push($levels, $level);
			}
		}
		if(count($players) == 0)
			$data = 'NIL';
		else
			$data = array('PLAYERS' => $players, 'RBS' => $rebirths, 'TYPES' => $types, 'LEVELS' => $levels);
		return $data;
	}
	
	public function change_pass($char, $account, $opasswd, $npasswd)
	{
		if($this->check_online($char))
			return 'NIL';
		else
		{
			$result = odbc_exec($this->con, "select * from account where c_id = '$account' and c_headera = '$opasswd'");
			if(odbc_num_rows($result) == 0)
				return 'NIL';
			else
			{
				$email = odbc_result($result, 'c_headerb');
				$result1 = odbc_exec($this->con, "update account set c_headera = '$npasswd' where c_id = '$account'");
				if($result1)
				{
					if($this->config->item('logging'))
					{
						$tid = $this->create_transaction($char, 'password');
						if($tid != 0)
							$result2 = odbc_exec($this->conls, "insert into password_log(transaction_id, character, old_passwd, new_passwd) values($tid, '$char', '$opasswd', '$npasswd')");
					}
					return $email;
				}
				else
					return 'NIL';
			}
		}
	}
	
	public function offline_tp($char)
	{
		if($this->check_online($char))
			return false;
		else
		{
			$result = odbc_exec($this->con, "update charac0 set c_headerb = '1;32383' where c_id = '$char'");
			if($result)
				return true;
			else
				return false;
		}
	}
	
	public function new_gift($char)
	{
		if($this->check_online($char))
			return false;
		else
		{
			$result = odbc_exec($this->con, "select c_sheaderb, set_gift from charac0 where c_id = '$char'");
			$gift_taken = odbc_result($result,'set_gift');
			if($gift_taken == 0)
			{
				$temp = $this->get_char_mbody($char);
				$WEAR = explode("=", $temp[5]);
				$SKILL = explode("=", $temp[1]);
				$PETACT = explode("=", $temp[18]);
				$char_type = odbc_result($result,'c_sheaderb');
				switch($char_type)
				{
					case "0":
						$WEAR[1] = "1030;2154;771496972;3403;3105;269622284;36151;4129;156113932;3393;5153;208149516;3423;6177;185474060;36181;7217;98311180";
						$SKILL[1] = "4294967124;0;0";
						$PETACT[1] = "1012;76684069;4152360961;4294160367";
						break;
					case "1":
						$WEAR[1] = "3563;481;742136844;1090;1450;670702604;3533;3105;96607244;3513;4129;220732428;3523;21537;259660812;3553;6177;192289804;3543;7201;186129420";
						$SKILL[1] = "1065353198;0;0";
						$PETACT[1] = "1013;76290853;4152360961;4294160495";
						break;
					case "2":
						$WEAR[1] = "2106;2730;736893964;3578;3105;157555724;36336;4129;284302348;36341;5153;107355148;3588;6177;270933004;3583;30415905;210902028";
						$SKILL[1] = "4290723710;2147483648;0";
						$PETACT[1] = "1014;75897637;4152360961;4294160379";
						break;
					case "3":
						$WEAR[1] = "17518;100;4294967295;1128;1633;622074892;36451;3105;271326220;3663;4129;172366860;3673;70689;201858060;3703;6177;161356812;3693;1073749025;109059084";
						$SKILL[1] = "131070;0;0";	
						$PETACT[1] = "1015;76028709;4152360961;4294160367";
						break;
				}
				$newString = $temp[0]."\_1".$SKILL[0]."=".$SKILL[1]."\_1".$temp[2]."\_1".$temp[3]."\_1".$temp[4]."\_1".$WEAR[0]."=".$WEAR[1]."\_1".$temp[6]."\_1".$temp[7]."\_1".$temp[8]."\_1".$temp[9]."\_1".$temp[10]."\_1".$temp[11]."\_1".$temp[12]."\_1".$temp[13]."\_1".$temp[14]."\_1".$temp[15]."\_1".$temp[16]."\_1".$temp[17]."\_1".$PETACT[0]."=".$PETACT[1]."\_1".$temp[19]."\_1".$temp[20]."\_1".$temp[21]."\_1".$temp[22]."\_1";
				$result1 = odbc_exec($this->con, "update charac0 set m_body = '$newString', set_gift = 1 where c_id = '$char'");
				if($result1)
					return true;
				else
					return false;
			}
			else
				return false;
		}
	}
	
	public function merc_rb($char, $merc)
	{
		if($this->check_online($char))
		{
			$this->session->set_userdata('merc_rb_error', 'Character is online!');
			return false;
		}
		else
		{
			$result = odbc_exec($this->conhs, "select * from HSTABLE where HSName = '$merc' and MasterName = '$char'");
			if(odbc_num_rows($result) == 0)
			{
				$this->session->set_userdata('merc_rb_error', 'Invalid mercenery name!');
				return false;
			}
			else
			{
				$mType = odbc_result($result,'Type');
				$mLevel = odbc_result($result,'HSLevel');
				$result1 = odbc_exec($this->conhs, "select rb from MERC where HSName = '$merc'");
				if(odbc_num_rows($result1) == 0)
				{
					$rb = 0;
					$result2 = odbc_exec($this->conhs,"insert into MERC(HSNAME, MasterName, Type, rb) values('$merc','$char',$mType,0)");
				}
				else
					$rb = odbc_result($result1, 'rb');
				$result3 = odbc_exec($this->con, "select c_headerc from charac0 where c_id = '$char'");
				$old_woonz = $check_woonz = odbc_result($result3, 'c_headerc');
				switch($rb)
				{
					case 0:
						$needed_wz = 1000000000;
						$needed_level = 130;
						$points = "900";
						break;
					case 1:
						$needed_wz = 2000000000;
						$needed_level = 160;
						$points = "1200";
						break;
					case 2:
						$needed_wz = 3000000000;
						$needed_level = 190;
						$points = "1500";
						break;
					case 3:
						$needed_wz = 4000000000;
						$needed_level = 220;
						$points = "1800";
						break;
					case 4:
						$needed_wz = 4000000000;
						$needed_level = 250;
						$points = "2100";
						break;
					default :
						break;
				}
				if($check_woonz >= $needed_wz && $mLevel >= $needed_level)
				{
					$old_rb = $rb;
					$rb = $rb + 1;
					$check_woonz -= $needed_wz;
					$mLevel = 1;
					switch($mType)
					{
						case 0:$ability="30;0;20;150;0;".$points.";0;300;0;";break;
						case 1:$ability="30;0;20;150;0;".$points.";0;240;0;";break;
						case 2:$ability="20;30;20;150;0;".$points.";0;180;0;";break;
						case 3:$ability="30;0;20;150;0;".$points.";0;240;0;";break;
					}
					$result4 = odbc_exec($this->conhs, "update HSTABLE set Ability = '$ability', HSLevel = $mLevel, HSExp = 0 where HSName = '$merc'");
					if(!$result4)
					{
						$this->session->set_userdata('merc_rb_error', 'Database update failed!');
						return false;
					}
					else
					{
						$result5 = odbc_exec($this->conhs, "update MERC set rb = $rb where HSName = '$merc'");
						if(!$result5)
						{
							$this->session->set_userdata('merc_rb_error', 'Database update failed!');
							return false;
						}
						else
						{
							$result6 = odbc_exec($this->con, "update charac0 set c_headerc = '$check_woonz' where c_id = '$char'");
							if($this->config->item('logging'))
							{
								$tid = $this->create_transaction($char, 'merci_rebirth');
								if($tid != 0)
									$result2 = odbc_exec($this->conls, "insert into merci_rebirth_log(transaction_id, character, merci_name, old_rebirth, old_woonz) values($tid, '$char', '$merc', $old_rb, $old_woonz)");
							}
							return true;							
						}
					}
				}
				else
				{
					$this->session->set_userdata('merc_rb_error', 'Please check requirements!');
					return false;
				}
			}
		}
	}
	
	public function char_rb($char)
	{
		if($this->check_online($char))
		{
			$this->session->set_userdata('char_rb_error', 'Character is online!');
			return false;
		}
		else
		{
			$result = odbc_exec($this->con, "select rb, c_headerc, c_sheaderb, c_sheaderc from charac0 where c_id = '$char'");
			$rb = $old_rebirth = $char_rb = odbc_result($result,'rb');
			$old_wz = $check_woonz = odbc_result($result, 'c_headerc');
			$charType = odbc_result($result, 'c_sheaderb');
			$check_level = $old_lvl = (int)odbc_result($result, 'c_sheaderc');
			$temp = $this->get_char_mbody($char);
			$INVEN = explode("=", $temp[6]);
			$WEAR = explode("=", $temp[5]);
			$EXP = explode("=", $temp[0]);
			$old_inv = $INVEN[1];
			$old_wear = $WEAR[1];
			$ITEM = explode(";",$INVEN[1]);			
			switch($rb)
			{
				case 0:
					if($check_level >= 100)
					{
						$char_rb = $rb + 1;
						$char_level = 1;  
						$EXP[1]= "0";
						switch($charType)
						{
							case '0':
								$stat_string = "30;0;16;35;130;150;75;20;120;120";
								break;
							case '1':
								$stat_string = "30;0;20;35;130;150;50;30;110;120";
								break;
							case '2':
								$stat_string = "20;26;12;35;130;150;30;80;30;120";
								break;
							case '3':
								$stat_string = "30;0;16;35;130;150;75;37;100;120";
								break;
						}
						$newString = $EXP[0]."=".$EXP[1]."\_1".$temp[1]."\_1".$temp[2]."\_1".$temp[3]."\_1".$temp[4]."\_1".$temp[5]."\_1".$temp[6]."\_1".$temp[7]."\_1".$temp[8]."\_1".$temp[9]."\_1".$temp[10]."\_1".$temp[11]."\_1".$temp[12]."\_1".$temp[13]."\_1".$temp[14]."\_1".$temp[15]."\_1".$temp[16]."\_1".$temp[17]."\_1".$temp[18]."\_1".$temp[19]."\_1".$temp[20]."\_1".$temp[21]."\_1".$temp[22]."\_1";
						$result1 = odbc_exec($this->con, "update charac0 set m_body = '$newString', rb = $char_rb, c_sheaderc = '$char_level',c_headerc = '$check_woonz', c_headera= '$stat_string' where c_id = '$char'");
						if(!$result1)
						{
							$this->session->set_userdata('char_rb_error', 'Database update failed!');
							return false;
						}
						else
						{
							if($this->config->item('logging'))
							{
								$tid = $this->create_transaction($char, 'rebirth');
								if($tid != 0)
									$result2 = odbc_exec($this->conls, "insert into rebirth_log(transaction_id, character, old_rebirth, old_inv, old_wear, old_lvl, old_wz) values($tid, '$char', '$old_rebirth', '$old_inv', '$old_wear', '$old_lvl', '$old_wz')");
							}
							return true;
						}
					}
					else
					{
						$this->session->set_userdata('char_rb_error', 'Check all requirements!');
						return false;
					}
					break;
				case 1 :
					if($check_level >= 110 && $check_woonz >= 100000000)
					{
						$char_rb = $rb + 1;
						$char_level = 1;  
						$EXP[1]= "0";
						$check_woonz -= 100000000;
						switch($charType)
						{
							case '0':
								$stat_string = "30;0;16;35;130;300;75;20;120;120";
								break;
							case '1':
								$stat_string = "30;0;20;35;130;300;50;30;110;120";
								break;
							case '2':
								$stat_string = "20;26;12;35;130;300;30;80;30;120";
								break;
							case '3':
								$stat_string = "30;0;16;35;130;300;75;37;100;120";
								break;
						}
						$newString = $EXP[0]."=".$EXP[1]."\_1".$temp[1]."\_1".$temp[2]."\_1".$temp[3]."\_1".$temp[4]."\_1".$temp[5]."\_1".$temp[6]."\_1".$temp[7]."\_1".$temp[8]."\_1".$temp[9]."\_1".$temp[10]."\_1".$temp[11]."\_1".$temp[12]."\_1".$temp[13]."\_1".$temp[14]."\_1".$temp[15]."\_1".$temp[16]."\_1".$temp[17]."\_1".$temp[18]."\_1".$temp[19]."\_1".$temp[20]."\_1".$temp[21]."\_1".$temp[22]."\_1";
						$result1 = odbc_exec($this->con, "update charac0 set m_body = '$newString', rb = $char_rb, c_sheaderc = '$char_level', c_headera= '$stat_string', c_headerc = '$check_woonz' where c_id = '$char'");
						if(!$result1)
						{
							$this->session->set_userdata('char_rb_error', 'Database update failed!');
							return false;
						}
						else
						{
							if($this->config->item('logging'))
							{
								$tid = $this->create_transaction($char, 'rebirth');
								if($tid != 0)
									$result2 = odbc_exec($this->conls, "insert into rebirth_log(transaction_id, character, old_rebirth, old_inv, old_wear, old_lvl, old_wz) values($tid, '$char', '$old_rebirth', '$old_inv', '$old_wear', '$old_lvl', '$old_wz')");
							}
							return true;
						}
					}
					else
					{
						$this->session->set_userdata('char_rb_error', 'Check all requirements!');
						return false;
					}
					break;
				case 2 :
					if($check_level >= 150 && $check_woonz >= 200000000)
					{
						$char_rb = $rb + 1;
						$char_level = 1;  
						$EXP[1]= "0";
						$check_woonz -= 200000000;
						switch($charType)
						{
							case '0':
								$stat_string = "30;0;16;35;130;450;75;20;120;120";
								break;
							case '1':
								$stat_string = "30;0;20;35;130;450;50;30;110;120";
								break;
							case '2':
								$stat_string = "20;26;12;35;130;450;30;80;30;120";
								break;
							case '3':
								$stat_string = "30;0;16;35;130;450;75;37;100;120";
								break;
						}
						$newString = $EXP[0]."=".$EXP[1]."\_1".$temp[1]."\_1".$temp[2]."\_1".$temp[3]."\_1".$temp[4]."\_1".$temp[5]."\_1".$temp[6]."\_1".$temp[7]."\_1".$temp[8]."\_1".$temp[9]."\_1".$temp[10]."\_1".$temp[11]."\_1".$temp[12]."\_1".$temp[13]."\_1".$temp[14]."\_1".$temp[15]."\_1".$temp[16]."\_1".$temp[17]."\_1".$temp[18]."\_1".$temp[19]."\_1".$temp[20]."\_1".$temp[21]."\_1".$temp[22]."\_1";
						$result1 = odbc_exec($this->con, "update charac0 set m_body = '$newString', rb = $char_rb, c_sheaderc = '$char_level', c_headera= '$stat_string', c_headerc = '$check_woonz' where c_id = '$char'");
						if(!$result1)
						{
							$this->session->set_userdata('char_rb_error', 'Database update failed!');
							return false;
						}
						else
						{
							if($this->config->item('logging'))
							{
								$tid = $this->create_transaction($char, 'rebirth');
								if($tid != 0)
									$result2 = odbc_exec($this->conls, "insert into rebirth_log(transaction_id, character, old_rebirth, old_inv, old_wear, old_lvl, old_wz) values($tid, '$char', '$old_rebirth', '$old_inv', '$old_wear', '$old_lvl', '$old_wz')");
							}
							return true;
						}
					}
					else
					{
						$this->session->set_userdata('char_rb_error', 'Check all requirements!');
						return false;
					}
					break;
				case 3 :
					if($check_level >= 151 && $ITEM[0] == '9736' && $ITEM[4] == '9737' && $check_woonz >= 50000000)
					{
						$char_rb = $rb + 1;
						$char_level = 1;  
						$EXP[1]= "0";
						$check_woonz -= 50000000;
						switch($charType)
						{
							case '0':
								$stat_string = "30;0;16;35;130;600;75;20;120;120";
								break;
							case '1':
								$stat_string = "30;0;20;35;130;600;50;30;110;120";
								break;
							case '2':
								$stat_string = "20;26;12;35;130;600;30;80;30;120";
								break;
							case '3':
								$stat_string = "30;0;16;35;130;600;75;37;100;120";
								break;
						}
						$INVEN[1] = "";
						$newString = $EXP[0]."=".$EXP[1]."\_1".$temp[1]."\_1".$temp[2]."\_1".$temp[3]."\_1".$temp[4]."\_1".$temp[5]."\_1".$INVEN[0]."=".$INVEN[1]."\_1".$temp[7]."\_1".$temp[8]."\_1".$temp[9]."\_1".$temp[10]."\_1".$temp[11]."\_1".$temp[12]."\_1".$temp[13]."\_1".$temp[14]."\_1".$temp[15]."\_1".$temp[16]."\_1".$temp[17]."\_1".$temp[18]."\_1".$temp[19]."\_1".$temp[20]."\_1".$temp[21]."\_1".$temp[22]."\_1";
						$result1 = odbc_exec($this->con, "update charac0 set m_body = '$newString', rb = $char_rb, c_sheaderc = '$char_level', c_headera= '$stat_string', c_headerc = '$check_woonz' where c_id = '$char'");
						if(!$result1)
						{
							$this->session->set_userdata('char_rb_error', 'Database update failed!');
							return false;
						}
						else
						{
							if($this->config->item('logging'))
							{
								$tid = $this->create_transaction($char, 'rebirth');
								if($tid != 0)
									$result2 = odbc_exec($this->conls, "insert into rebirth_log(transaction_id, character, old_rebirth, old_inv, old_wear, old_lvl, old_wz) values($tid, '$char', '$old_rebirth', '$old_inv', '$old_wear', '$old_lvl', '$old_wz')");
							}
							return true;
						}
					}
					else
					{
						$this->session->set_userdata('char_rb_error', 'Check all requirements!');
						return false;
					}
					break;
				case 4 :
					if($check_level >= 154 && $ITEM[0] == '9738'&& $ITEM[4] == '9739' && $check_woonz >= 50000000)
					{
						$char_rb = $rb + 1;
						$char_level = 1;  
						$EXP[1]= "0";
						$check_woonz -= 50000000;
						switch($charType)
						{
							case '0':
								$stat_string = "30;0;16;35;130;750;75;20;120;120";
								break;
							case '1':
								$stat_string = "30;0;20;35;130;750;50;30;110;120";
								break;
							case '2':
								$stat_string = "20;26;12;35;130;750;30;80;30;120";
								break;
							case '3':
								$stat_string = "30;0;16;35;130;750;75;37;100;120";
								break;
						}
						$INVEN[1] = "";
						$newString = $EXP[0]."=".$EXP[1]."\_1".$temp[1]."\_1".$temp[2]."\_1".$temp[3]."\_1".$temp[4]."\_1".$temp[5]."\_1".$INVEN[0]."=".$INVEN[1]."\_1".$temp[7]."\_1".$temp[8]."\_1".$temp[9]."\_1".$temp[10]."\_1".$temp[11]."\_1".$temp[12]."\_1".$temp[13]."\_1".$temp[14]."\_1".$temp[15]."\_1".$temp[16]."\_1".$temp[17]."\_1".$temp[18]."\_1".$temp[19]."\_1".$temp[20]."\_1".$temp[21]."\_1".$temp[22]."\_1";
						$result1 = odbc_exec($this->con, "update charac0 set m_body = '$newString', rb = $char_rb, c_sheaderc = '$char_level', c_headera= '$stat_string', c_headerc = '$check_woonz' where c_id = '$char'");
						if(!$result1)
						{
							$this->session->set_userdata('char_rb_error', 'Database update failed!');
							return false;
						}
						else
						{
							if($this->config->item('logging'))
							{
								$tid = $this->create_transaction($char, 'rebirth');
								if($tid != 0)
									$result2 = odbc_exec($this->conls, "insert into rebirth_log(transaction_id, character, old_rebirth, old_inv, old_wear, old_lvl, old_wz) values($tid, '$char', '$old_rebirth', '$old_inv', '$old_wear', '$old_lvl', '$old_wz')");
							}
							return true;
						}
					}
					else
					{
						$this->session->set_userdata('char_rb_error', 'Check all requirements!');
						return false;
					}
					break;
				case 5 :
					if($check_level >= 158 && $ITEM[0] == '9645'&& $ITEM[4] == '9651' && $check_woonz >= 100000000)
					{
						$char_rb = $rb + 1;
						$char_level = 1;  
						$EXP[1]= "0";
						$check_woonz -= 100000000;
						switch($charType)
						{
							case '0':
								$stat_string = "30;0;16;35;130;900;75;20;120;120";
								break;
							case '1':
								$stat_string = "30;0;20;35;130;900;50;30;110;120";
								break;
							case '2':
								$stat_string = "20;26;12;35;130;900;30;80;30;120";
								break;
							case '3':
								$stat_string = "30;0;16;35;130;900;75;37;100;120";
								break;
						}
						$INVEN[1] = "";
						$newString = $EXP[0]."=".$EXP[1]."\_1".$temp[1]."\_1".$temp[2]."\_1".$temp[3]."\_1".$temp[4]."\_1".$temp[5]."\_1".$INVEN[0]."=".$INVEN[1]."\_1".$temp[7]."\_1".$temp[8]."\_1".$temp[9]."\_1".$temp[10]."\_1".$temp[11]."\_1".$temp[12]."\_1".$temp[13]."\_1".$temp[14]."\_1".$temp[15]."\_1".$temp[16]."\_1".$temp[17]."\_1".$temp[18]."\_1".$temp[19]."\_1".$temp[20]."\_1".$temp[21]."\_1".$temp[22]."\_1";
						$result1 = odbc_exec($this->con, "update charac0 set m_body = '$newString', rb = $char_rb, c_sheaderc = '$char_level', c_headera= '$stat_string', c_headerc = '$check_woonz' where c_id = '$char'");
						if(!$result1)
						{
							$this->session->set_userdata('char_rb_error', 'Database update failed!');
							return false;
						}
						else
						{
							if($this->config->item('logging'))
							{
								$tid = $this->create_transaction($char, 'rebirth');
								if($tid != 0)
									$result2 = odbc_exec($this->conls, "insert into rebirth_log(transaction_id, character, old_rebirth, old_inv, old_wear, old_lvl, old_wz) values($tid, '$char', '$old_rebirth', '$old_inv', '$old_wear', '$old_lvl', '$old_wz')");
							}
							return true;
						}
					}
					else
					{
						$this->session->set_userdata('char_rb_error', 'Check all requirements!');
						return false;
					}
					break;
				case 6 :
					if($check_level >= 160 && $ITEM[0] == '9659'&& $ITEM[4] == '9658' && $check_woonz >= 100000000)
					{
						$char_rb = $rb + 1;
						$char_level = 1;  
						$EXP[1]= "0";
						$check_woonz -= 100000000;
						switch($charType)
						{
							case '0':
								$stat_string = "30;0;16;35;130;1050;75;20;120;120";
								break;
							case '1':
								$stat_string = "30;0;20;35;130;1050;50;30;110;120";
								break;
							case '2':
								$stat_string = "20;26;12;35;130;1050;30;80;30;120";
								break;
							case '3':
								$stat_string = "30;0;16;35;130;1050;75;37;100;120";
								break;
						}
						$INVEN[1] = "";
						$newString = $EXP[0]."=".$EXP[1]."\_1".$temp[1]."\_1".$temp[2]."\_1".$temp[3]."\_1".$temp[4]."\_1".$temp[5]."\_1".$INVEN[0]."=".$INVEN[1]."\_1".$temp[7]."\_1".$temp[8]."\_1".$temp[9]."\_1".$temp[10]."\_1".$temp[11]."\_1".$temp[12]."\_1".$temp[13]."\_1".$temp[14]."\_1".$temp[15]."\_1".$temp[16]."\_1".$temp[17]."\_1".$temp[18]."\_1".$temp[19]."\_1".$temp[20]."\_1".$temp[21]."\_1".$temp[22]."\_1";
						$result1 = odbc_exec($this->con, "update charac0 set m_body = '$newString', rb = $char_rb, c_sheaderc = '$char_level', c_headera= '$stat_string', c_headerc = '$check_woonz' where c_id = '$char'");
						if(!$result1)
						{
							$this->session->set_userdata('char_rb_error', 'Database update failed!');
							return false;
						}
						else
						{
							if($this->config->item('logging'))
							{
								$tid = $this->create_transaction($char, 'rebirth');
								if($tid != 0)
									$result2 = odbc_exec($this->conls, "insert into rebirth_log(transaction_id, character, old_rebirth, old_inv, old_wear, old_lvl, old_wz) values($tid, '$char', '$old_rebirth', '$old_inv', '$old_wear', '$old_lvl', '$old_wz')");
							}
							return true;
						}
					}
					else
					{
						$this->session->set_userdata('char_rb_error', 'Check all requirements!');
						return false;
					}
					break;
				case 7 :
					if($check_level >= 162 && $ITEM[0] == '9654'&& $ITEM[4] == '9656' && $ITEM[8] == '9657' && $check_woonz >= 100000000)
					{
						$char_rb = $rb + 1;
						$char_level = 1;  
						$EXP[1]= "0";
						$check_woonz -= 100000000;
						switch($charType)
						{
							case '0':
								$stat_string = "30;0;16;35;130;1200;75;20;120;120";
								break;
							case '1':
								$stat_string = "30;0;20;35;130;1200;50;30;110;120";
								break;
							case '2':
								$stat_string = "20;26;12;35;130;1200;30;80;30;120";
								break;
							case '3':
								$stat_string = "30;0;16;35;130;1200;75;37;100;120";
								break;
						}
						$INVEN[1] = "";
						$newString = $EXP[0]."=".$EXP[1]."\_1".$temp[1]."\_1".$temp[2]."\_1".$temp[3]."\_1".$temp[4]."\_1".$temp[5]."\_1".$INVEN[0]."=".$INVEN[1]."\_1".$temp[7]."\_1".$temp[8]."\_1".$temp[9]."\_1".$temp[10]."\_1".$temp[11]."\_1".$temp[12]."\_1".$temp[13]."\_1".$temp[14]."\_1".$temp[15]."\_1".$temp[16]."\_1".$temp[17]."\_1".$temp[18]."\_1".$temp[19]."\_1".$temp[20]."\_1".$temp[21]."\_1".$temp[22]."\_1";
						$result1 = odbc_exec($this->con, "update charac0 set m_body = '$newString', rb = $char_rb, c_sheaderc = '$char_level', c_headera= '$stat_string', c_headerc = '$check_woonz' where c_id = '$char'");
						if(!$result1)
						{
							$this->session->set_userdata('char_rb_error', 'Database update failed!');
							return false;
						}
						else
						{
							if($this->config->item('logging'))
							{
								$tid = $this->create_transaction($char, 'rebirth');
								if($tid != 0)
									$result2 = odbc_exec($this->conls, "insert into rebirth_log(transaction_id, character, old_rebirth, old_inv, old_wear, old_lvl, old_wz) values($tid, '$char', '$old_rebirth', '$old_inv', '$old_wear', '$old_lvl', '$old_wz')");
							}
							return true;
						}
					}
					else
					{
						$this->session->set_userdata('char_rb_error', 'Check all requirements!');
						return false;
					}
					break;
				case 8 :
					if($check_level >= 163 && $ITEM[0] == '9649'&& $ITEM[4] == '9650' && $ITEM[8] == '9652' && $ITEM[12] == '9647' && $ITEM[16] == '9933')
					{
						$char_rb = $rb + 1;
						$char_level = 1;  
						$EXP[1]= "0";
						switch($charType)
						{
							case '0':
								$stat_string = "30;0;16;35;130;1500;75;20;120;120";
								break;
							case '1':
								$stat_string = "30;0;20;35;130;1500;50;30;110;120";
								break;
							case '2':
								$stat_string = "20;26;12;35;130;1500;30;80;30;120";
								break;
							case '3':
								$stat_string = "30;0;16;35;130;1500;75;37;100;120";
								break;
						}
						$INVEN[1] = "";
						$newString = $EXP[0]."=".$EXP[1]."\_1".$temp[1]."\_1".$temp[2]."\_1".$temp[3]."\_1".$temp[4]."\_1".$temp[5]."\_1".$INVEN[0]."=".$INVEN[1]."\_1".$temp[7]."\_1".$temp[8]."\_1".$temp[9]."\_1".$temp[10]."\_1".$temp[11]."\_1".$temp[12]."\_1".$temp[13]."\_1".$temp[14]."\_1".$temp[15]."\_1".$temp[16]."\_1".$temp[17]."\_1".$temp[18]."\_1".$temp[19]."\_1".$temp[20]."\_1".$temp[21]."\_1".$temp[22]."\_1";
						$result1 = odbc_exec($this->con, "update charac0 set m_body = '$newString', rb = $char_rb, c_sheaderc = '$char_level', c_headera= '$stat_string' where c_id = '$char'");
						if(!$result1)
						{
							$this->session->set_userdata('char_rb_error', 'Database update failed!');
							return false;
						}
						else
						{
							if($this->config->item('logging'))
							{
								$tid = $this->create_transaction($char, 'rebirth');
								if($tid != 0)
									$result2 = odbc_exec($this->conls, "insert into rebirth_log(transaction_id, character, old_rebirth, old_inv, old_wear, old_lvl, old_wz) values($tid, '$char', '$old_rebirth', '$old_inv', '$old_wear', '$old_lvl', '$old_wz')");
							}
							return true;
						}
					}
					else
					{
						$this->session->set_userdata('char_rb_error', 'Check all requirements!');
						return false;
					}
					break;
				case 9 :
					if($check_level >= 165 && $ITEM[0] == '9740'&& $ITEM[4] == '9647' && $ITEM[8] == '9648' && $ITEM[12] == '9653' && $ITEM[16] == '9650' && $ITEM[20] == '9934')
					{
						$char_rb = $rb + 1;
						$char_level = 1;  
						$EXP[1]= "0";
						switch($charType)
						{
							case '0':
								$stat_string = "30;0;16;35;130;1900;75;20;120;120";
								break;
							case '1':
								$stat_string = "30;0;20;35;130;1900;50;30;110;120";
								break;
							case '2':
								$stat_string = "20;26;12;35;130;1900;30;80;30;120";
								break;
							case '3':
								$stat_string = "30;0;16;35;130;1900;75;37;100;120";
								break;
						}
						$INVEN[1] = "";
						$newString = $EXP[0]."=".$EXP[1]."\_1".$temp[1]."\_1".$temp[2]."\_1".$temp[3]."\_1".$temp[4]."\_1".$temp[5]."\_1".$INVEN[0]."=".$INVEN[1]."\_1".$temp[7]."\_1".$temp[8]."\_1".$temp[9]."\_1".$temp[10]."\_1".$temp[11]."\_1".$temp[12]."\_1".$temp[13]."\_1".$temp[14]."\_1".$temp[15]."\_1".$temp[16]."\_1".$temp[17]."\_1".$temp[18]."\_1".$temp[19]."\_1".$temp[20]."\_1".$temp[21]."\_1".$temp[22]."\_1";
						$result1 = odbc_exec($this->con, "update charac0 set m_body = '$newString', rb = $char_rb, c_sheaderc = '$char_level', c_headera= '$stat_string' where c_id = '$char'");
						if(!$result1)
						{
							$this->session->set_userdata('char_rb_error', 'Database update failed!');
							return false;
						}
						else
						{
							if($this->config->item('logging'))
							{
								$tid = $this->create_transaction($char, 'rebirth');
								if($tid != 0)
									$result2 = odbc_exec($this->conls, "insert into rebirth_log(transaction_id, character, old_rebirth, old_inv, old_wear, old_lvl, old_wz) values($tid, '$char', '$old_rebirth', '$old_inv', '$old_wear', '$old_lvl', '$old_wz')");
							}
							return true;
						}
					}
					else
					{
						$this->session->set_userdata('char_rb_error', 'Check all requirements!');
						return false;
					}
					break;
				case 10 :
					if($check_level >= 165 && $ITEM[0] == '9761')
					{
						$char_rb = $rb + 1;
						$char_level = 1;  
						$EXP[1]= "0";
						switch($charType)
						{
							case '0':
								$stat_string = "30;0;16;35;130;2500;75;20;120;120";
								break;
							case '1':
								$stat_string = "30;0;20;35;130;2500;50;30;110;120";
								break;
							case '2':
								$stat_string = "20;26;12;35;130;2500;30;80;30;120";
								break;
							case '3':
								$stat_string = "30;0;16;35;130;2500;75;37;100;120";
								break;
						}
						$INVEN[1] = "";
						$newString = $EXP[0]."=".$EXP[1]."\_1".$temp[1]."\_1".$temp[2]."\_1".$temp[3]."\_1".$temp[4]."\_1".$temp[5]."\_1".$INVEN[0]."=".$INVEN[1]."\_1".$temp[7]."\_1".$temp[8]."\_1".$temp[9]."\_1".$temp[10]."\_1".$temp[11]."\_1".$temp[12]."\_1".$temp[13]."\_1".$temp[14]."\_1".$temp[15]."\_1".$temp[16]."\_1".$temp[17]."\_1".$temp[18]."\_1".$temp[19]."\_1".$temp[20]."\_1".$temp[21]."\_1".$temp[22]."\_1";
						$result1 = odbc_exec($this->con, "update charac0 set m_body = '$newString', rb = $char_rb, c_sheaderc = '$char_level', c_headera= '$stat_string' where c_id = '$char'");
						if(!$result1)
						{
							$this->session->set_userdata('char_rb_error', 'Database update failed!');
							return false;
						}
						else
						{
							if($this->config->item('logging'))
							{
								$tid = $this->create_transaction($char, 'rebirth');
								if($tid != 0)
									$result2 = odbc_exec($this->conls, "insert into rebirth_log(transaction_id, character, old_rebirth, old_inv, old_wear, old_lvl, old_wz) values($tid, '$char', '$old_rebirth', '$old_inv', '$old_wear', '$old_lvl', '$old_wz')");
							}
							return true;
						}
					}
					else
					{
						$this->session->set_userdata('char_rb_error', 'Check all requirements!');
						return false;
					}
					break;
					default :
						$this->session->set_userdata('char_rb_error', 'Further rebirths are yet to be implemented!');
						return false;
						break;
					
			}
		}
	}
	
	public function create_transaction($char, $type)
	{
		$time_now = $this->get_current_datetime();
		$ip = $this->session->userdata('ip_address');
		$result = odbc_exec($this->conls, "select count(*) as num from transaction_log");
		$num = odbc_result($result,'num');
		$count = $num + 1;
		$result1 = odbc_exec($this->conls, "insert into transaction_log(transaction_id, character, action_type, action_time, action_ip) values($count, '$char', '$type', '$time_now', '$ip')");
		if($result1)
			return $count;
		else
			return 0;
	}

	public function get_email($user)
	{
		$result = odbc_exec($this->con, "select c_headerb from account where c_id = '$user'");
		if(!$result)
			return 'NIL';
		else
			return odbc_result($result, 'c_headerb');
	}

	public function check_deals($char, $account)
	{
		$result = odbc_exec($this->con,"select * from Deals where deal_status = 1 and character != '$char'");
		if(odbc_num_rows($result) == 0)
			return 'NIL';
		else
		{
			$data = array();
			while (odbc_fetch_row($result))
			{
				$char_name = odbc_result($result, 'character');
				$result1 = odbc_exec($this->con,"select c_sheadera from charac0 where c_id='$char_name'");
				$dacc = odbc_result($result1,'c_sheadera');
				if($dacc != $account)
				{
					$deal_id = odbc_result($result, 'deal_id');
					$item_name = odbc_result($result,'item_name');
					$flamez_coins = odbc_result($result, 'flamez_coins');
					array_push($data, array('deal_id' => $deal_id, 'char_name' => $char_name, 'item_name' => $item_name, 'flamez_coins' => $flamez_coins));
				}
			}
			if(count($data) == 0)
				return 'NIL';
			else
				return $data;
		}
	}

	public function buy_from_mart($char, $account, $deal_id)
	{
		if($this->check_online($char))
		{
			$this->session->set_userdata('buy_mart_error', 'Character is online!');
			return false;
		}
		else
		{
			$result = odbc_exec($this->con, "select * from Deals where deal_id = $deal_id");
			if(odbc_num_rows($result) == 0)
			{
				$this->session->set_userdata('buy_mart_error', 'Invalid deal!');
				return false;
			}
			else
			{
				$result1 = odbc_exec($this->con, "select flamez_coins from AccountInfo where account='$account'");
				$old_points = $cpoints = (int)odbc_result($result1,"flamez_coins");
				$rpoints = (int)odbc_result($result, "flamez_coins");
				$item_code = odbc_result($result, "item_code");
				$dchar = odbc_result($result, "character");
				$result2 = odbc_exec($this->con, "select c_sheadera from charac0 where c_id='$dchar'");
				$dacc = odbc_result($result2,'c_sheadera');
				if(strtolower($dacc) == strtolower($account))
				{
					$this->session->set_userdata('buy_mart_error', 'Cannot buy from your own deal!');
					return false;
				}
				else
				{
					if($cpoints < $rpoints)
					{
						$this->session->set_userdata('buy_mart_error', 'Insufficient Flamez Coins!');
						return false;
					}
					else
					{
						$result3 = odbc_exec($this->con, "select flamez_coins from AccountInfo where account='$dacc'");
						$dpoints = odbc_result($result3,"flamez_coins");
						$cpoints = $cpoints - $rpoints;
						$dpoints = $dpoints + $rpoints;
						$temp = $this->get_char_mbody($char);
						$INVEN = explode("=",$temp[6]);
						$temp45 = explode(";", $INVEN[1]);
						if($temp45[0] != "6144")
						{
							$this->session->set_userdata('buy_mart_error', 'Keep a UJ in first slot!');
							return false;
						}
						else
						{
							$old_inv = $INVEN[1];
							$INVEN[1] = $item_code.";0";
							$newString = $temp[0]."\_1".$temp[1]."\_1".$temp[2]."\_1".$temp[3]."\_1".$temp[4]."\_1".$temp[5]."\_1".$INVEN[0]."=".$INVEN[1]."\_1".$temp[7]."\_1".$temp[8]."\_1".$temp[9]."\_1".$temp[10]."\_1".$temp[11]."\_1".$temp[12]."\_1".$temp[13]."\_1".$temp[14]."\_1".$temp[15]."\_1".$temp[16]."\_1".$temp[17]."\_1".$temp[18]."\_1".$temp[19]."\_1".$temp[20]."\_1".$temp[21]."\_1".$temp[22]."\_1";
							$result4 = odbc_exec($this->con,"update charac0 set m_body = '$newString' where c_id = '$char'");
							if(!$result4)
							{
								$this->session->set_userdata('buy_mart_error', 'Database update failed!');
								return false;
							}
							else
							{
								$result5 = odbc_exec($this->con, "update AccountInfo set flamez_coins=$cpoints where account='$account'");
								$result6 = odbc_exec($this->con, "update AccountInfo set flamez_coins=$dpoints where account='$dacc'");
								$result7 = odbc_exec($this->con, "update Deals set deal_status=0, bcharacter='$char' where deal_id='$deal_id'");
								if($this->config->item('logging'))
								{
									$tid = $this->create_transaction($char, 'mart_buy');
									if($tid != 0)
										$result8 = odbc_exec($this->conls, "insert into mart_buy_log(transaction_id, character, old_inv, old_points, deal_id) values($tid, '$char', '$old_inv', $old_points, $deal_id)");
								}
								$this->session->set_userdata('fcoins', $cpoints);
								return true;
							}
						}
					}
				}
			}
		}
	}

	public function my_deals($char)
	{
		$result = odbc_exec($this->con,"select * from Deals where deal_status = 1 and character = '$char'");
		if(odbc_num_rows($result) == 0)
			return 'NIL';
		else
		{
			$data = array();
			while (odbc_fetch_row($result))
			{
				$char_name = odbc_result($result, 'character');
				$deal_id = odbc_result($result, 'deal_id');
				$item_name = odbc_result($result,'item_name');
				$flamez_coins = odbc_result($result, 'flamez_coins');
				array_push($data, array('deal_id' => $deal_id, 'char_name' => $char_name, 'item_name' => $item_name, 'flamez_coins' => $flamez_coins));
				
			}
			if(count($data) == 0)
				return 'NIL';
			else
				return $data;
		}
	}

	public function cancel_deal($char, $account, $deal_id)
	{
		if($this->check_online($char))
		{
			$this->session->set_userdata('cancel_mart_error', 'Character is online!');
			return false;
		}
		else
		{
			$result = odbc_exec($this->con, "select * from Deals where deal_id = $deal_id");
			if(odbc_num_rows($result) == 0)
			{
				$this->session->set_userdata('cancel_mart_error', 'Invalid deal!');
				return false;
			}
			else
			{
				$result1 = odbc_exec($this->con, "select flamez_coins from AccountInfo where account='$account'");
				$old_points = $cpoints = odbc_result($result1,"flamez_coins");
				$item_code = odbc_result($result, "item_code");
				$dchar = odbc_result($result, "character");
				$result2 = odbc_exec($this->con, "select c_sheadera from charac0 where c_id='$dchar'");
				$dacc = odbc_result($result2,'c_sheadera');
				if(strtolower($dacc) != strtolower($account))
				{
					$this->session->set_userdata('cancel_mart_error', 'Cannot cancel others\' deal!');
					return false;
				}
				else
				{
					$temp = $this->get_char_mbody($char);
					$INVEN = explode("=",$temp[6]);
					$temp45 = explode(";", $INVEN[1]);
					if($temp45[0] != "6144")
					{
						$this->session->set_userdata('cancel_mart_error', 'Keep a UJ in first slot!');
						return false;
					}
					else
					{
						$old_inv = $INVEN[1];
						$INVEN[1] = $item_code.";0";
						$newString = $temp[0]."\_1".$temp[1]."\_1".$temp[2]."\_1".$temp[3]."\_1".$temp[4]."\_1".$temp[5]."\_1".$INVEN[0]."=".$INVEN[1]."\_1".$temp[7]."\_1".$temp[8]."\_1".$temp[9]."\_1".$temp[10]."\_1".$temp[11]."\_1".$temp[12]."\_1".$temp[13]."\_1".$temp[14]."\_1".$temp[15]."\_1".$temp[16]."\_1".$temp[17]."\_1".$temp[18]."\_1".$temp[19]."\_1".$temp[20]."\_1".$temp[21]."\_1".$temp[22]."\_1";
						$result4 = odbc_exec($this->con,"update charac0 set m_body = '$newString' where c_id = '$char'");
						if(!$result4)
						{
							$this->session->set_userdata('cancel_mart_error', 'Database update failed!');
							return false;
						}
						else
						{
							$result7 = odbc_exec($this->con, "update Deals set deal_status=2, bcharacter='$char' where deal_id=$deal_id");
							if($this->config->item('logging'))
							{
								$tid = $this->create_transaction($char, 'mart_cancel');
								if($tid != 0)
									$result8 = odbc_exec($this->conls, "insert into mart_buy_log(transaction_id, character, old_inv, old_points, deal_id) values($tid, '$char', '$old_inv', $old_points, $deal_id)");
							}
							return true;
						}
					}					
				}
			}
		}
	}

	public function post_deal($char, $flamez_coins, $name)
	{
		if($this->check_online($char))
		{
			$this->session->set_userdata('post_deal_error', 'Character is online!');
			return false;
		}
		else
		{
			$result = odbc_exec($this->con, "select count(*) as num from Deals");
			$num = odbc_result($result,'num');
			$count = $num + 1;
			$temp = $this->get_char_mbody($char);
			$INVEN = explode("=",$temp[6]);
			$old_inv = $INVEN[1];
			$ITEM = explode(";",$INVEN[1]);
			if($INVEN != null && $ITEM != null && count($ITEM) != 4)
			{
				$icode = $ITEM[0].";".$ITEM[1].";".$ITEM[2].";";
				$INVEN[1] = "0;0;0;0";
				$newString = $temp[0]."\_1".$temp[1]."\_1".$temp[2]."\_1".$temp[3]."\_1".$temp[4]."\_1".$temp[5]."\_1".$INVEN[0]."=".$INVEN[1]."\_1".$temp[7]."\_1".$temp[8]."\_1".$temp[9]."\_1".$temp[10]."\_1".$temp[11]."\_1".$temp[12]."\_1".$temp[13]."\_1".$temp[14]."\_1".$temp[15]."\_1".$temp[16]."\_1".$temp[17]."\_1".$temp[18]."\_1".$temp[19]."\_1".$temp[20]."\_1".$temp[21]."\_1".$temp[22]."\_1";
				$result1 = odbc_exec($this->con, "update charac0 set m_body = '$newString' where c_id = '$char'");
				if($result1)
				{
					$ip = $this->session->userdata('ip_address');
					$ctime = $this->get_current_datetime();
					$result2 = odbc_exec($this->con,"insert into Deals(deal_id,character ,item_name, item_code, flamez_coins, seller_ip, deal_time) values($count,'$char', '$name', '$icode', $flamez_coins, '$ip', '$ctime')");
					if($this->config->item('logging'))
					{
						$tid = $this->create_transaction($char, 'post_deal');
						if($tid != 0)
							$result3 = odbc_exec($this->conls, "insert into mart_sell_log(transaction_id, deal_id, character, old_inv) values($tid, $count, '$char', '$old_inv')");
					}
					return true;				
				}
				else
				{
					$this->session->set_userdata('post_deal_error', 'Database update failed!');
					return false;
				}
			}
			else
			{
				$this->session->set_userdata('post_deal_error', 'Please check your inventory if it has only 1 item not less not more!');
				return false;
			}
		}
	}

	public function gold_to_coins($char, $account, $slots)
	{
		if($this->check_online($char))
			return false;
		else
		{
			$temp = $this->get_char_mbody($char);
			$INVEN = explode("=",$temp[6]);
			$old_inv = $INVEN[1];
			$result = odbc_exec($this->con, "select flamez_coins from AccountInfo where account='$account'");
			$old_points = $ccoins = odbc_result($result, 'flamez_coins');
			$coins = 0;
			$ITEM = explode(";", $INVEN[1]);
			if(count($ITEM)/4 < $slots)
				$slots = count($ITEM)/4;
			for($i = 0;$i < $slots;$i++)
			{
				if($ITEM[$i*4] == '9814')
					$coins = $coins + 1;
				else if($ITEM[$i*4] == '9933')
					$coins = $coins + 2;
				else if($ITEM[$i*4] == '9934')
					$coins = $coins + 4;
			}
			$ccoins = $ccoins + $coins;
			$INVEN[1] = "";
			$newString = $temp[0]."\_1".$temp[1]."\_1".$temp[2]."\_1".$temp[3]."\_1".$temp[4]."\_1".$temp[5]."\_1".$INVEN[0]."=".$INVEN[1]."\_1".$temp[7]."\_1".$temp[8]."\_1".$temp[9]."\_1".$temp[10]."\_1".$temp[11]."\_1".$temp[12]."\_1".$temp[13]."\_1".$temp[14]."\_1".$temp[15]."\_1".$temp[16]."\_1".$temp[17]."\_1".$temp[18]."\_1".$temp[19]."\_1".$temp[20]."\_1".$temp[21]."\_1".$temp[22]."\_1";
			$result1 = odbc_exec($this->con, "update charac0 set m_body = '$newString' where c_id = '$char'");
			if(!$result1)
				return false;
			else
			{
				$result2 = odbc_exec($this->con, "update AccountInfo set flamez_coins=$ccoins where account='$account'");
				$this->session->set_userdata('fcoins', $ccoins);
				if($this->config->item('logging'))
				{
					$tid = $this->create_transaction($char, 'convert');
					if($tid != 0)
						$result3 = odbc_exec($this->conls, "insert into convert_log(transaction_id, character, action_type, old_inv, old_wz, old_points, quantity) values($tid, '$char', 'Gold to coins', '$old_inv', 0, $old_points, $coins)");
				}
				return true;
			}
		}
	}

	public function coins_to_gold($char, $account, $amount)
	{
		if($this->check_online($char))
			return false;
		else
		{
			$result = odbc_exec($this->con, "select c_headerc from charac0 where c_id='$char'");
			$old_wz = $check_woonz = odbc_result($result,'c_headerc');
			if($check_woonz <= 100000000)
			{
				$result1 = odbc_exec($con, "select flamez_coins from AccountInfo where account='$account'");
				$old_coins = $ccoins = odbc_result($result1, 'flamez_coins');
				if($amount <= $ccoins)
				{
					$ccoins = $ccoins - $amount;
					if($amount > 4)
					{
						$temp = $this->get_char_mbody($char);
						$INVEN = explode("=",$temp[6]);
						$old_inv = $INVEN[1];
						if($amount%4 == 0)
						{
							$lc = $amount/4;
							$wz = 0;
						}
						else
						{
							$l = $amount/4;
							$lc = (int)$l;
							$rem = $amount - ($lc * 4);
							$wz = $rem * 1000000000;
						}
						$check_woonz = $check_woonz + $wz;
						$INVEN[1] = "";
						for($i = 0; $i <  $lc; $i++)
						{
							$INVEN[1] = $INVEN[1]."9934;0;0;".$i;
							$j = $i + 1;
							if($j != $lc)
								$INVEN[1] = $INVEN[1].";";
						}
						$newString = $temp[0]."\_1".$temp[1]."\_1".$temp[2]."\_1".$temp[3]."\_1".$temp[4]."\_1".$temp[5]."\_1".$INVEN[0]."=".$INVEN[1]."\_1".$temp[7]."\_1".$temp[8]."\_1".$temp[9]."\_1".$temp[10]."\_1".$temp[11]."\_1".$temp[12]."\_1".$temp[13]."\_1".$temp[14]."\_1".$temp[15]."\_1".$temp[16]."\_1".$temp[17]."\_1".$temp[18]."\_1".$temp[19]."\_1".$temp[20]."\_1".$temp[21]."\_1".$temp[22]."\_1";
						$result2 = odbc_exec($this->con, "update charac0 set m_body = '$newString', c_headerc = '$check_woonz' where c_id = '$char'");
						if(!$result2)
							return false;
						else
						{
							$result3 = odbc_exec($this->con, "update AccountInfo set flamez_coins=$ccoins where account='$username'");
							$this->session->set_userdata('fcoins', $ccoins);
							if($this->config->item('logging'))
							{
								$tid = $this->create_transaction($char, 'convert');
								if($tid != 0)
									$result4 = odbc_exec($this->conls, "insert into convert_log(transaction_id, character, action_type, old_inv, old_wz, old_points, quantity) values($tid, '$char', 'Coins to woonz', '$old_inv', $old_wz, $old_points, $amount)");
							}
							return true;
						}							
					}
					else
					{
						$wz = $amount * 1000000000;
						$check_woonz = $check_woonz + $wz;
						$result2 = odbc_exec($this->con, "update charac0 set c_headerc = '$check_woonz' where c_id = '$char'");
						if(!$result2)
							return false;
						else
						{
							$result3 = odbc_exec($this->con, "update AccountInfo set flamez_coins=$ccoins where account='$username'");
							if($this->config->item('logging'))
							{
								$tid = $this->create_transaction($char, 'convert');
								if($tid != 0)
									$result4 = odbc_exec($this->conls, "insert into convert_log(transaction_id, character, action_type, old_inv, old_wz, old_points, quantity) values($tid, '$char', 'Coins to woonz', 'Not Needed', $old_wz, $old_points, $amount)");
							}
							return true;
						}
					}
				}
				else
					return false;
			}
			else
				return false;
		}
	}

	public function wz_to_coins($char, $account, $amount)
	{
		if($this->check_online($char))
			return false;
		else
		{
			$result = odbc_exec($this->con, "select c_headerc from charac0 where c_id='$char'");
			$old_wz = $check_woonz = odbc_result($result,'c_headerc');
			if($amount >= 100000000 && $amount <= $check_woonz)
			{
				$check_woonz = $check_woonz - $amount;
				$coins = $amount / 1000000000;
				$coins = round($coins,1);
				$result1 = odbc_exec($con, "select flamez_coins from AccountInfo where account='$account'");
				$old_points = $ccoins = odbc_result($result1, 'flamez_coins');
				$ccoins = $ccoins + $coins;
				$result2 = odbc_exec($this->con, "update charac0 set c_headerc = '$check_woonz' where c_id = '$char'");
				if(!$result2)
					return false;
				else
				{
					$result3 = odbc_exec($this->con, "update AccountInfo set flamez_coins=$ccoins where account='$username'");
					$this->session->set_userdata('fcoins', $ccoins);
					if($this->config->item('logging'))
					{
						$tid = $this->create_transaction($char, 'convert');
						if($tid != 0)
							$result4 = odbc_exec($this->conls, "insert into convert_log(transaction_id, character, action_type, old_inv, old_wz, old_points, quantity) values($tid, '$char', 'Coins to woonz', 'Not Needed', $old_wz, $old_points, $amount)");
					}
					return true;
				}
			}
			else
				return false;
		}
	}
	
	public function get_coupons($char)
	{
		$result = odbc_exec($this->con, "select * from coupon_table where character = '$char'");
		if(odbc_num_rows($result) == 0)
			$data = 'NIL';
		else
		{
			$data = array();
			while(odbc_fetch_row($result))
			{
				$id = odbc_result($result, 'id');
				$coupon = odbc_result($result, 'coupon_code');
				$used = odbc_result($result, 'flag');
				$min = odbc_result($result, 'min_amt');
				$percent = odbc_result($result, 'discount');
				array_push($data, array('id' => $id, 'coupon' => $coupon, 'used' => $used, 'min' => $min, 'percent' => $percent));
			}
		}
		return $data;
	}

	public function apply_coupon($coupon, $char)
	{
		$result = odbc_exec($this->cones, "select * from coupon_table where coupon_code = '$coupon' and flag = 0 and character ='$char'");
		if(odbc_num_rows($result) == 0)
		{
			$this->session->set_userdata('apply_coupon_error', 'Invalid coupon code!');
			return false;
		}
		else
		{
			$min = odbc_result($result, 'min_amt');
			$percent = odbc_result($result, 'discount');
			$result1 = odbc_exec($this->cones, "select credits_required, coupon_code from shopping_cart where char_name = '$char'");
			if(odbc_num_rows($result1) == 0)
			{
				$this->session->set_userdata('apply_coupon_error', 'Cart empty!');
				return false;
			}
			else
			{
				$c = odbc_result($result1, 'coupon_code');
				if($c == 'NIL')
				{
					$req = odbc_result($result1, 'credits_required');
					if($req < $min)
					{
						$this->session->set_userdata('apply_coupon_error', 'Please make sure your total purchase credits in your cart is at least !'.$min);
						return false;
					}
					else
					{
						$discount = (int)($req * $percent / 100);
						$new_req = $req - $discount;
						$result2 = odbc_exec($this->cones, "update shopping_cart set coupon_code = '$coupon', discount = $discount, credits_required = $new_req where char_name = '$char'");
						if($result2)
						{
							$result3 = odbc_exec($this->cones, "update coupon_table set flag = 1 where coupon_code = '$coupon'");
							if($result3)
								return true;
							else
							{
								$result4 = odbc_exec($this->cones, "update shopping_cart set coupon_code = 'NIL', discount = 0, credits_required = $req where char_name = '$char'");
								$this->session->set_userdata('apply_coupon_error', 'Database update failed!');
								return false;
							}
						}
						else
						{
							$this->session->set_userdata('apply_coupon_error', 'Database update failed!');
							return false;
						}
					}
				}
				else
				{
					$this->session->set_userdata('apply_coupon_error', 'Only one coupon can be applied at a time!');
					return false;
				}
			}
		}
	}

	public function remove_coupon($char)
	{
		$result = odbc_exec($this->cones, "select credits_required, coupon_code, discount from shopping_cart where char_name = '$char'");
		if(odbc_num_rows($result) == 0)
		{
			$this->session->set_userdata('remove_coupon_error', 'Cart empty!');
			return false;
		}
		else
		{
			$coupon = odbc_result($result, 'coupon_code');
			if($coupon == 'NIL')
			{
				$this->session->set_userdata('remove_coupon_error', 'No coupon has been applied yet!');
				return false;
			}
			else
			{
				$discount = odbc_result($result, 'discount');
				$req = odbc_result($result, 'credits_required');
				$new_req = $req + $discount;
				$result1 = odbc_exec($this->cones, "update shopping_cart set coupon_code = 'NIL', discount = 0, credits_required = $new_req where char_name = '$char'");
				if($result1)
				{
					$result2 = odbc_exec($this->cones, "update coupon_table set flag = 0 where coupon_code = '$coupon'");
					return true;
				}
				else
				{
					$this->session->set_userdata('remove_coupon_error', 'Database update failed!');
					return false;
				}
			}
		}
	}

	public function set_pass($username, $password)
	{
		$date = $this->get_current_datetime();
		$result = odbc_exec($this->con,"select c_headerb from account where c_id = '$username'");
		if(odbc_num_rows($result) == 0)
			return 'NIL';
		else
		{
			$to = odbc_result($result,'c_headerb');
			$result1 = odbc_exec($this->con, "update account set c_sheadera = 'reserve', c_sheaderb = 'reserve', c_sheaderc = 'reserve', c_headera = '$password', c_headerc = 'reserve', d_udate = CONVERT(DATETIME, '$date', 102), m_body = 'reserve' where c_id = '$username'");
			return $to;
		}
	}

	public function create_account($user, $passwd, $name, $email, $contact)
	{
		$result = odbc_exec ($this->con, "select * from account where c_id = '$user'");
		if(odbc_num_rows($result) == 0)
		{
			$result1 = odbc_exec ($this->con, "select * from account where c_headerb = '$email'");
			if(odbc_num_rows($result1) == 0)
			{
				$date = $this->get_current_datetime();
				$ip = $this->session->userdata('ip_address');
				$act_id = substr(sha1(uniqid(rand(),true)), 0, 20);
				$result2 = odbc_exec($this->con, "insert into AccountInfo(account,contact,name,email,ip,event_points,cevent_points,refresh_count,ref_add_allow,referer) values('$user','$contact','$name','$email','$ip',0,0,0,1,'NULL')");
				$result3 = odbc_exec($this->con, "INSERT INTO account (c_id, c_sheadera, c_sheaderb, c_sheaderc, c_headera, c_headerb, c_headerc, d_cdate, d_udate, c_status, m_body, acc_status, salary, last_salary) VALUES ('$user', 'reserve', 'reserve', 'reserve', '$passwd', '$email', 'reserve', CONVERT(DATETIME, '$date', 102), CONVERT(DATETIME, '$date', 102), 'F', 'reserve', 'Normal', CONVERT(DATETIME, '$date', 102), CONVERT(DATETIME, '$date', 102))");
				$result4  = odbc_exec($this->con,"insert into Activation(act_id,account) values('$act_id','$user')");
				if($result3)
				{
					$this->session->set_userdata('act_id', $act_id);
					return true;
				}
				else
				{
					$this->session->set_userdata('create_error', 'Database update failed!');
					return false;
				}
			}
			else
			{
				$this->session->set_userdata('create_error', 'E-mail already exists!');
				return false;
			}
		}
		else
		{
			$this->session->set_userdata('create_error', 'Username already exists!');
			return false;
		}
	}

	public function activate($act_id)
	{
		$result = odbc_exec($this->con,"select * from Activation where act_id='$act_id'");
		if(odbc_num_rows($result) == 0)
			return false;
		else
		{
			$account = odbc_result($result,'account');
			$result1 = odbc_exec($this->con, "select c_status from account where c_id='$account'");
			$status = odbc_result($result1, 'c_status');
			if($status == 'A')
				return false;
			else if($status == 'F')
			{
				$result2 = odbc_exec($this->con,"update account set c_status='A' where c_id='$account'");
				if($result2)
					return true;
				else
					return false;
			}
		}
	}

	public function get_pk()
	{
		$result = odbc_exec($this->con, "select top 10 * from playerpkinfo order by id desc");
		if(odbc_num_rows($result) == 0)
			return 'NIL';
		else
		{
			$akiller = array();
			$akilled = array();
			$akrrb = array();
			$akrlvl = array();
			$akdrb = array();
			$akdlvl = array();
			$aloc = array();
			$akrt = array();
			$akdt = array();
			while(odbc_fetch_row($result))
			{
				$killer = odbc_result($result,'pker');
				$killed = odbc_result($result,'pked');
				$krrb = odbc_result($result,'pker_rb');
				$krlvl = odbc_result($result,'pker_lvl');
				$kdrb = odbc_result($result,'pked_rb');
				$kdlvl = odbc_result($result,'pked_lvl');
				$loc = odbc_result($result,'loc');
				$krt = trim(odbc_result($result,'pker_nation'));
				$kdt = trim(odbc_result($result,'pked_nation'));
				array_push($akiller, $killer);
				array_push($akilled, $killed);
				array_push($akrrb, $krrb);
				array_push($akrlvl, $krlvl);
				array_push($akdrb, $kdrb);
				array_push($akdlvl, $kdlvl);
				array_push($aloc, $loc);
				array_push($akrt, $krt);
				array_push($akdt, $kdt);
			}
			if(count($akiller) == 0)
				$data = 'NIL';
			else
				$data = array('KILLER' => $akiller, 'KILLED' => $akilled, 'KILLER_RB' => $akrrb, 'KILLER_LVL' => $akrlvl, 'KILLED_RB' => $akdrb, 'KILLED_LVL' => $akdlvl, 'LOCATION' => $aloc, 'KILLER_TOWN' => $akrt, 'KILLED_TOWN' => $akdt);
			return $data;
		}
	}

	public function rebirth_gift($char, $rb)
	{
		if($this->check_online($char))
		{
			$this->session->set_userdata('rb_gift_error', 'Character is online!');
			return false;
		}
		else
		{
			$result = odbc_exec($this->con, "select rb,c_sheaderc from charac0 where c_id='$char'");
			$crb = (int)odbc_result($result, 'rb');
			$level = odbc_result($result, 'c_sheaderc');
			if($crb >= $rb)
			{
				switch((int)$rb)
				{
					case 11:
						if($level == 165)
						{
							$result = odbc_exec($this->con, "update charac0 set c_sheaderc='166' where c_id='$char'");
							if($result)
								return true;
							else
							{
								$this->session->set_userdata('rb_gift_error', 'Database update failed!');
								return false;
							}
						}
						else
						{
							$this->session->set_userdata('rb_gift_error', 'Make sure your character level is 165!');
							return false;
						}
						break;
					default:
						$this->session->set_userdata('rb_gift_error', 'Rebirth '.$rb.' gift not implemented!');
						return false;
						break;
				}
			}
			else
			{
				$this->session->set_userdata('rb_gift_error', 'Character rebirth low!');
				return false;
			}
		}
	}

	public function buy_lore($char)
	{
		if($this->check_online($char))
		{
			$this->session->set_userdata('buy_lore_error', 'Character is online!');
			return false;
		}
		else
		{
			$result = odbc_exec($this->con, "select c_headerc from charac0 where c_id = '$char'");
			$check_woonz = odbc_result($result,'c_headerc');
			if($check_woonz >= 150000000 )
			{
				$check_woonz = $check_woonz - 150000000;
				$temp = $this->get_char_mbody($char);
				$LORE = explode("=", $temp[19]);
				$LORE[1] += 1000000;
				$newString = $temp[0]."\_1".$temp[1]."\_1".$temp[2]."\_1".$temp[3]."\_1".$temp[4]."\_1".$temp[5]."\_1".$temp[6]."\_1".$temp[7]."\_1".$temp[8]."\_1".$temp[9]."\_1".$temp[10]."\_1".$temp[11]."\_1".$temp[12]."\_1".$temp[13]."\_1".$temp[14]."\_1".$temp[15]."\_1".$temp[16]."\_1".$temp[17]."\_1".$temp[18]."\_1".$LORE[0]."=".$LORE[1]."\_1".$temp[20]."\_1".$temp[21]."\_1".$temp[22]."\_1";
				$result1 = odbc_exec($this->con, "update charac0 set m_body = '$newString', c_headerc = '$check_woonz' where c_id = '$char'");
				if($result1)
				{
					if($this->config->item('logging'))
						$tid = $this->create_transaction($char, 'buy_lore');
					return true;
				}
				else
				{
					$this->session->set_userdata('buy_lore_error', 'Database update failed!');
					return false;
				}
			}
			else
			{
				$this->session->set_userdata('buy_lore_error', 'Not enough woonz in inventory!');
				return false;
			}
		}
	}
	
	public function mailer_get_email($char)
	{
		$result = odbc_exec($this->con,"select c_sheadera from charac0 where c_id = '$char'");
		if(odbc_num_rows($result) == 0)
			return 'NIL';
		else
		{
			$username = odbc_result($result, 'c_sheadera');
			$result1 = odbc_exec($this->con,"select c_headerb from account where c_id = '$username'");
			$email = odbc_result($result1, 'c_headerb');
			return $email;
		}
	}
}
?>