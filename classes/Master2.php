<!-- classes\Master.php -->
<?php
require_once('../config.php');
error_log("FormData received: " . print_r($_POST, true));
Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	function capture_err(){
		if(!$this->conn->error)
			return false;
		else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}
	
	function save_shop_type(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
			}
		}
		
		$check = $this->conn->query("SELECT * FROM `shop_type_list` where `name` = '{$name}' and delete_flag = 0 ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Shop Type already exists.";
		}else{
			if(empty($id)){
				$sql = "INSERT INTO `shop_type_list` set {$data} ";
			}else{
				$sql = "UPDATE `shop_type_list` set {$data} where id = '{$id}' ";
			}
			$save = $this->conn->query($sql);
			if($save){
				$resp['status'] = 'success';
				if(empty($id))
				$resp['msg'] = " New Shop Type successfully saved.";
				else
				$resp['msg'] = " Shop Type successfully updated.";
			}else{
				$resp['status'] = 'failed';
				$resp['err'] = $this->conn->error."[{$sql}]";
			}
		}
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function delete_shop_type(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `shop_type_list` set delete_flag = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Shop Type successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_category(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
			}
		}
		
		$check = $this->conn->query("SELECT * FROM `category_list` where `name` = '{$name}' and vendor_id = '{$vendor_id}' and delete_flag = 0 ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = " Category already exists.";
		}else{
			if(empty($id)){
				$sql = "INSERT INTO `category_list` set {$data} ";
			}else{
				$sql = "UPDATE `category_list` set {$data} where id = '{$id}' ";
			}
			$save = $this->conn->query($sql);
			if($save){
				$resp['status'] = 'success';
				if(empty($id))
				$resp['msg'] = " New Category successfully saved.";
				else
				$resp['msg'] = " Category successfully updated.";
			}else{
				$resp['status'] = 'failed';
				$resp['err'] = $this->conn->error."[{$sql}]";
			}
		}
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function delete_category(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `category_list` set delete_flag = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Category successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_product(){
		$_POST['description'] = htmlentities($_POST['description']);
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `product_list` where vendor_id = '{$vendor_id}' and `name` = '{$name}' and delete_flag = 0 ".(!empty($id) ? " and id != '{$id}'" : ""))->num_rows;
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = ' Product Name Already exists.';
		}else{
			if(empty($id)){
				$sql = "INSERT INTO `product_list` set {$data} ";
			}else{
				$sql = "UPDATE `product_list` set {$data} where id = '{$id}' ";
			}
			$save = $this->conn->query($sql);
			if($save){
				$pid = empty($id) ? $this->conn->insert_id : $id;
				$resp['pid'] = $pid;
				$resp['status'] = 'success';
				if(empty($id))
					$resp['msg'] = " New Product successfully saved.";
				else
					$resp['msg'] = " Product successfully updated.";
				
				if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
					$uploadDir = base_app."uploads/products/";
					if(!is_dir($uploadDir))
						mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
	
					$fileName = $pid . ".png"; // You can customize the file name if needed
					$filePath = $uploadDir . $fileName;
					
					// Move the uploaded file to the destination directory
					if(move_uploaded_file($_FILES['img']['tmp_name'], $filePath)){
						// Update the image path in the database
						$imagePath = "uploads/products/" . $fileName;
						$this->conn->query("UPDATE `product_list` SET image_path = '{$imagePath}' WHERE id = '{$pid}'");
					}else{
						$resp['msg'] = "Failed to move uploaded file.";
					}
				}
			}else{
				$resp['status'] = 'failed';
				if(empty($id))
					$resp['msg'] = " Product has failed to save.";
				else
					$resp['msg'] = " Product has failed to update.";
				$resp['err'] = $this->conn->error."[{$sql}]";
			}
		}
	
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	
	
	function delete_product(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `product_list` set `delete_flag` = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Product successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function add_to_cart(){
		$_POST['client_id'] = $this->settings->userdata('id');
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM cart_list where product_id = '{$product_id}' && client_id = '{$client_id}'")->num_rows;
		if($check > 0){
			$sql = "UPDATE cart_list set quantity = quantity + {$quantity} where product_id = '{$product_id}' && client_id = '{$client_id}' ";
		}else{
			$sql = "INSERT INTO cart_list set {$data}";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			$resp['msg'] = " Product has added to cart.";
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = " The product has failed to add to the cart.";
			$resp['error'] = $this->conn->error. "[{$sql}]";
		}
		if($resp['status'] == 'success')
		$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function update_cart_qty(){
		extract($_POST);
		$update_cart = $this->conn->query("UPDATE `cart_list` set `quantity` = '{$quantity}' where id = '{$cart_id}'");
		if($update_cart){
			$resp['status'] = 'success';
			$resp['msg'] = ' Product Quantity has updated successfully';
		}else{
			$resp['status'] = 'success';
			$resp['msg'] = ' Product Quantity has failed to update';
			$resp['error'] = $this->conn->error;
		}
		
		if($resp['status'] == 'success')
		$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function delete_cart(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `cart_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$resp['msg'] = " Cart Item has been deleted successfully.";
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = " Cart Item has failed to delete.";
			$resp['error'] = $this->conn->error;
		}
		if($resp['status'] =='success'){
			$this->settings->set_flashdata('success',$resp['msg']);
		}
		return json_encode($resp);
	}
	function place_order(){
		extract($_POST);
		$inserted=[];
		$has_failed=false;
		$gtotal = 0;
		$vendors = $this->conn->query("SELECT * FROM `vendor_list` where id in (SELECT vendor_id from product_list where id in (SELECT product_id FROM `cart_list` where client_id ='{$this->settings->userdata('id')}')) order by `shop_name` asc");
		$prefix = date('Ym-');
		$code = sprintf("%'.05d",1);
		while($vrow = $vendors->fetch_assoc()):
			$data = "";
			while(true){
				$check = $this->conn->query("SELECT * FROM order_list where code = '{$prefix}{$code}' ")->num_rows;
				if($check > 0){
					$code = sprintf("%'.05d",ceil($code) + 1);
				}else{
					break;
				}
			}
			$ref_code = $prefix.$code;
			$data = "('{$ref_code}','{$this->settings->userdata('id')}','{$vrow['id']}','{$this->conn->real_escape_string($delivery_address)}')";
			$sql = "INSERT INTO `order_list` (`code`,`client_id`,`vendor_id`,`delivery_address`) VALUES {$data}";
			$save = $this->conn->query($sql);
			if($save){
				$oid = $this->conn->insert_id;
				$inserted[] = $oid;
				$data = "";
				$gtotal = 0 ;
				$products = $this->conn->query("SELECT c.*, p.name as `name`, p.price,p.image_path FROM `cart_list` c inner join product_list p on c.product_id = p.id where c.client_id = '{$this->settings->userdata('id')}' and p.vendor_id = '{$vrow['id']}' order by p.name asc");
				while($prow = $products->fetch_assoc()):
					$total = $prow['price'] * $prow['quantity'];
					$gtotal += $total;
					if(!empty($data)) $data .= ", ";
						$data .= "('{$oid}', '{$prow['product_id']}', '{$prow['quantity']}', '{$prow['price']}')";
				endwhile;
				$sql2 = "INSERT INTO `order_items` (`order_id`,`product_id`,`quantity`,`price`) VALUES {$data}";
				$save2= $this->conn->query($sql2);
				if($save2){
					$this->conn->query("UPDATE `order_list` set `total_amount` = '{$gtotal}' where id = '{$oid}'");
				}else{
					$has_failed = true;
					$resp['sql'] = $sql2;
					break;
				}
			}else{
				$has_failed = true;
				$resp['sql'] = $sql;
				break;
			}
		endwhile;
		if(!$has_failed){
			$resp['status'] = 'success';
			$resp['msg'] = " Order has been placed";
			$this->conn->query("DELETE FROM `cart_list` where client_id ='{$this->settings->userdata('id')}'");
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = " Order has failed to place";
			$resp['error'] = $this->conn->error;
			if(count($inserted) > 0){
				$this->conn->query("DELETE FROM `order_list` where id in (".(implode(',',array_values($inserted))).") ");
			}
		}
		if($resp['status'] == 'success')
		$this->settings->set_flashdata('success',$resp['msg']);

		return json_encode($resp);
	}
	function cancel_order(){
		extract($_POST);
		$update = $this->conn->query("UPDATE `order_list` set `status` = 5 where id = '{$id}'");
		if($update){
			$resp['status'] = 'success';
			$resp['msg'] = " Order has been cancelled successfully.";
		}else{
			$resp['status'] = 'success';
			$resp['error'] = $this->conn->error;
		}
		if($resp['status'] == 'success')
		$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function update_status(){
		extract($_POST);
		$update = $this->conn->query("UPDATE `order_list` set `status` = '{$status}' where id = '{$id}'");
		if($update){
			$resp['status'] = 'success';
			$resp['msg'] = " Order Status has been updated successfully.";
		}else{
			$resp['status'] = 'success';
			$resp['msg'] = " Order Status has failed to update.";
			$resp['error'] = $this->conn->error;
		}
		if($resp['status'] == 'success')
		$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function save_crop(){
		
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('Id', 'Picture1', 'Picture2', 'Picture3'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
			}
		}
		
		$check = $this->conn->query("SELECT * FROM `crop` WHERE `Name` = '{$Name}' AND `VendorId` = '{$VendorId}' AND `delete_flag` = 0 ".(!empty($id) ? " AND `Id` != '{$id}' " : "") )->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Crop already exists.";
		}else{
			if(empty($id)){
				$sql = "INSERT INTO `crop` SET {$data} ";
				$save = $this->conn->query($sql);
				$id = $this->conn->insert_id; // Get the last inserted ID
			}else{
				$sql = "UPDATE `crop` SET {$data} WHERE `Id` = '{$id}' ";
				$save = $this->conn->query($sql);
			}
			if($save){
				$resp['status'] = 'success';
				if(empty($id))
					$resp['msg'] = "New Crop successfully saved.";
				else
					$resp['msg'] = "Crop successfully updated.";
	
				// Save images and update paths in the database
				$uploadDir = base_app."uploads/crops/";
				if(!is_dir($uploadDir))
					mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
	
				// Handle Picture1 upload
				if(isset($_FILES['Picture1']['tmp_name']) && $_FILES['Picture1']['tmp_name'] != ''){
					$filePath1 = $uploadDir . basename($_FILES["Picture1"]["name"]);
					if(move_uploaded_file($_FILES['Picture1']['tmp_name'], $filePath1)){
						$imagePath1 = "uploads/crops/" . basename($_FILES["Picture1"]["name"]);
						// Update the image path in the database
						$this->conn->query("UPDATE `crop` SET Picture1 = '{$imagePath1}' WHERE `Id` = '{$id}' ");
					}else{
						$resp['msg'] .= " Failed to upload Picture1.";
					}
				}
	
				if(isset($_FILES['Picture2']['tmp_name']) && $_FILES['Picture2']['tmp_name'] != ''){
					$filePath2 = $uploadDir . basename($_FILES["Picture2"]["name"]);
					if(move_uploaded_file($_FILES['Picture2']['tmp_name'], $filePath2)){
						$imagePath2 = "uploads/crops/" . basename($_FILES["Picture2"]["name"]);
						// Update the image path in the database
						$this->conn->query("UPDATE `crop` SET Picture2 = '{$imagePath2}' WHERE `Id` = '{$id}' ");
					}else{
						$resp['msg'] .= " Failed to upload Picture2.";
					}
				}
				
				// Handle Picture3 upload
				if(isset($_FILES['Picture3']['tmp_name']) && $_FILES['Picture3']['tmp_name'] != ''){
					$filePath3 = $uploadDir . basename($_FILES["Picture3"]["name"]);
					if(move_uploaded_file($_FILES['Picture3']['tmp_name'], $filePath3)){
						$imagePath3 = "uploads/crops/" . basename($_FILES["Picture3"]["name"]);
						// Update the image path in the database
						$this->conn->query("UPDATE `crop` SET Picture3 = '{$imagePath3}' WHERE `Id` = '{$id}' ");
					}else{
						$resp['msg'] .= " Failed to upload Picture3.";
					}
				}
	
			}else{
				$resp['status'] = 'failed';
				if(empty($id))
					$resp['msg'] = "Crop failed to save.";
				else
					$resp['msg'] = "Crop failed to update.";
				$resp['err'] = $this->conn->error."[{$sql}]";
			}
		}
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	
	function save_review(){
		// Extract POST data
		extract($_POST);
		
		// Initialize response array
		$resp = array();
	
		// Build data string for SQL query
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id'))){
				if(!empty($data)) $data .= ",";
				$data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
			}
		}
	
		try {
			// Proceed with saving the review unconditionally
			$sql = "INSERT INTO `review` SET {$data}";
			error_log('Save Review Function Called'); // Log that the function is called

// Example log for variables
error_log('Product ID: ' . $product_id);
error_log('Review Data: ' . print_r($_POST, true)); // Log POST data
			// Execute the SQL query
			$save = $this->conn->query($sql);
	
			// Handle response based on save result
			if($save){
				$resp['status'] = 'success';
				$resp['msg'] = "New review successfully saved.";
			} else {
				$resp['status'] = 'failed';
				$resp['err'] = $this->conn->error . "[{$sql}]";
			}
		} catch (Exception $e) {
			// Handle exceptions
			$resp['status'] = 'failed';
			$resp['msg'] = 'Error saving review: ' . $e->getMessage();
		}
	
		// Return JSON response
		return json_encode($resp);
	}
	
	
	public function delete_crop(){
		extract($_POST);
		$resp = array(); // Initialize response array
		
		// Perform the delete operation
		$sql = "UPDATE `crop` SET `delete_flag` = 1 WHERE id = '{$id}'";
		$del = $this->conn->query($sql);
	
		if($del){
			$resp['status'] = 'success';
			$resp['message'] = 'Crop successfully deleted.';
			// Optionally set a flash message, if needed
			// $this->settings->set_flashdata('success', 'Crop successfully deleted.');
		}else{
			$resp['status'] = 'failed';
			$resp['message'] = 'Failed to delete crop.';
			$resp['error'] = $this->conn->error;
	
			// Log the error for debugging
			error_log("Error deleting crop: " . $this->conn->error);
			error_log("SQL Query: " . $sql);
		}
	
		// Return JSON-encoded response
		return json_encode($resp);
	}
	
	
	
	
	
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'save_shop_type':
		echo $Master->save_shop_type();
	break;
	case 'save_review':
		echo $Master->save_review();
	break;
	case 'delete_shop_type':
		echo $Master->delete_shop_type();
	break;
	case 'save_category':
		echo $Master->save_category();
	break;
	case 'delete_category':
		echo $Master->delete_category();
	break;
	case 'save_product':
		echo $Master->save_product();
	break;
	case 'delete_product':
		echo $Master->delete_product();
	break;
	case 'add_to_cart':
		echo $Master->add_to_cart();
	break;
	case 'update_cart_qty':
		echo $Master->update_cart_qty();
	break;
	case 'delete_cart':
		echo $Master->delete_cart();
	break;
	case 'place_order':
		echo $Master->place_order();
	break;
	case 'cancel_order':
		echo $Master->cancel_order();
	break;
	case 'update_status':
		echo $Master->update_status();
	break;
	case 'save_crop':
		echo $Master->save_crop();
	break;
	default:
		// echo $sysset->index();
		break;
}