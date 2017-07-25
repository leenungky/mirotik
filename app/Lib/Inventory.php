<?php 

class Inventory{
	public $order_no;
	public $resi_no;
	public $merchant_name;
	
	public function setOrderNo($order_no) {
    	$this->order_no = $order_no;
  	}

  	public function setResiNo($resi_no) {
    	$this->resi_no = $resi_no;
  	}

  	public function setMerchantName($merchant_name) {
    	$this->merchant_name = $merchant_name;
  	}

  	public function setPhone($phone) {
    	$this->phone = $phone;
  	}

  	public function setOrigin($origin) {
    	$this->origin = $origin;
  	}

  	public function setDest($dest) {
    	return $this->dest = $dest;
  	}

  	public function setEmail($email) {
    	return $this->email = $email;
  	}

  	public function setEmail($email) {
    	return $this->email = $email;
  	}

}

?>