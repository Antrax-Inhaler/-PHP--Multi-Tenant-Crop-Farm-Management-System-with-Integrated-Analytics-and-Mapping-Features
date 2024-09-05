<?php
require_once('../config.php');
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
			if(!in_array($k, array('id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `product_list` where vendor_id = '{$vendor_id}' and `name` = '{$name}' and delete_flag = 0 ".(!empty($id) ? " and id != '{$id}'" : ""))->num_rows;
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = ' Product Name Already exists.';
		} else {
			if(empty($id)){
				$sql = "INSERT INTO `product_list` set {$data} ";
			} else {
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
				
				$uploadDir = base_app."uploads/products/";
				if(!is_dir($uploadDir))
					mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
	
				// Handle image uploads
				$imageFields = ['img' => 'image_path', 'img_2' => 'image_path_2', 'img_3' => 'image_path_3', 'img_4' => 'image_path_4', 'img_5' => 'image_path_5'];
				foreach ($imageFields as $imgField => $dbField) {
					if(isset($_FILES[$imgField]) && $_FILES[$imgField]['tmp_name'] != ''){
						$fileName = $pid . "_{$imgField}.png"; // Customize the file name
						$filePath = $uploadDir . $fileName;
						
						// Move the uploaded file to the destination directory
						if(move_uploaded_file($_FILES[$imgField]['tmp_name'], $filePath)){
							// Update the image path in the database
							$imagePath = "uploads/products/" . $fileName;
							$this->conn->query("UPDATE `product_list` SET {$dbField} = '{$imagePath}' WHERE id = '{$pid}'");
						} else {
							$resp['msg'] = "Failed to move uploaded file.";
						}
					}
				}
			} else {
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
	function delete_archive(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `pest_disease_archive` WHERE id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$resp['msg'] = "Pest/Disease Archive entry has been deleted successfully.";
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to delete Pest/Disease Archive entry.";
			$resp['error'] = $this->conn->error;
		}
		if($resp['status'] == 'success'){
			$this->settings->set_flashdata('success', $resp['msg']);
		}
		return json_encode($resp);
	}
	function delete_crop_activity_suggestion(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `crop_activity_suggestions` WHERE id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$resp['msg'] = "Crop Activity Suggestion has been deleted successfully.";
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to delete Crop Activity Suggestion.";
			$resp['error'] = $this->conn->error;
		}
		if($resp['status'] == 'success'){
			$this->settings->set_flashdata('success', $resp['msg']);
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
	
		// Update the status
		$update = $this->conn->query("UPDATE `order_list` SET `status` = '{$status}' WHERE id = '{$id}'");
	
		// Update the sms column to zero
		$updateSms = $this->conn->query("UPDATE `order_list` SET `sms` = 0 WHERE id = '{$id}'");
	
		if($update && $updateSms){
			$resp['status'] = 'success';
			$resp['msg'] = "Order Status has been updated successfully.";
		} else {
			$resp['status'] = 'error';
			$resp['msg'] = "Order Status has failed to update.";
			$resp['error'] = $this->conn->error;
		}
	
		if($resp['status'] == 'success') {
			$this->settings->set_flashdata('success', $resp['msg']);
		} else {
			$this->settings->set_flashdata('error', $resp['msg']);
		}
	
		return json_encode($resp);
	}
	
	function save_crop(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k, array('Id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
			}
		}
		if(empty($Id)){
			$sql = "INSERT INTO `crop` set {$data} ";
		} else {
			$sql = "UPDATE `crop` set {$data} where Id = '{$Id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$pid = empty($Id) ? $this->conn->insert_id : $Id;
			$resp['pid'] = $pid;
			$resp['status'] = 'success';
			if(empty($Id))
				$resp['msg'] = "New Crop successfully saved.";
			else
				$resp['msg'] = "Crop successfully updated.";
	
			$imageFields = ['Picture1', 'Picture2', 'Picture3'];
			foreach($imageFields as $field){
				if(isset($_FILES[$field]) && $_FILES[$field]['tmp_name'] != ''){
					$uploadDir = base_app."uploads/crops/";
					if(!is_dir($uploadDir))
						mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
	
					$fileName = $pid . "_".substr($field, -1). ".png"; // Customize the file name if needed
					$filePath = $uploadDir . $fileName;
	
					// Move the uploaded file to the destination directory
					if(move_uploaded_file($_FILES[$field]['tmp_name'], $filePath)){
						// Update the image path in the database
						$imagePath = "uploads/crops/" . $fileName;
						$this->conn->query("UPDATE `crop` SET {$field} = '{$imagePath}' WHERE Id = '{$pid}'");
					} else {
						$resp['msg'] = "Failed to move uploaded file.";
					}
				}
			}
		} else {
			$resp['status'] = 'failed';
			if(empty($Id))
				$resp['msg'] = "Crop has failed to save.";
			else
				$resp['msg'] = "Crop has failed to update.";
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
	
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success', $resp['msg']);
		return json_encode($resp);
	}
	function save_pd(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k, array('Id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
			}
		}
		if(empty($Id)){
			$sql = "INSERT INTO `croppestdisease` set {$data} ";
		} else {
			$sql = "UPDATE `croppestdisease` set {$data} where Id = '{$Id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$pid = empty($Id) ? $this->conn->insert_id : $Id;
			$resp['pid'] = $pid;
			$resp['status'] = 'success';
			if(empty($Id))
				$resp['msg'] = "New Pest and Disease successfully saved.";
			else
				$resp['msg'] = "Pest and Disease successfully updated.";
	
			$resp['status'] = 'failed';
			if(empty($Id))
				$resp['msg'] = "Pest and Disease has failed to save.";
			else
				$resp['msg'] = "Pest and Disease has failed to update.";
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
	
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success', $resp['msg']);
		return json_encode($resp);
	}
	function save_harvest(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k, array('Id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
			}
		}
		if(empty($Id)){
			$sql = "INSERT INTO `harvest` set {$data} ";
		} else {
			$sql = "UPDATE `harvest` set {$data} where Id = '{$Id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$pid = empty($Id) ? $this->conn->insert_id : $Id;
			$resp['pid'] = $pid;
			$resp['status'] = 'success';
			if(empty($Id))
				$resp['msg'] = "New harvest successfully saved.";
			else
				$resp['msg'] = "Harvest successfully updated.";

		} else {
			$resp['status'] = 'failed';
			if(empty($Id))
				$resp['msg'] = "Harvest has failed to save.";
			else
				$resp['msg'] = "Harvest has failed to update.";
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
	
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success', $resp['msg']);
		return json_encode($resp);
	}

	function save_farm(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k, array('Id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
			}
		}
		if(empty($Id)){
			$sql = "INSERT INTO `farm` set {$data} ";
		} else {
			$sql = "UPDATE `farm` set {$data} where Id = '{$Id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$pid = empty($Id) ? $this->conn->insert_id : $Id;
			$resp['pid'] = $pid;
			$resp['status'] = 'success';
			if(empty($Id))
				$resp['msg'] = "New farm successfully saved.";
			else
				$resp['msg'] = "Farm successfully updated.";
	
			$imageFields = ['Image'];
			foreach($imageFields as $field){
				if(isset($_FILES[$field]) && $_FILES[$field]['tmp_name'] != ''){
					$uploadDir = base_app."uploads/farms/";
					if(!is_dir($uploadDir))
						mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
	
					$fileName = $pid . "_".substr($field, -1). ".png"; // Customize the file name if needed
					$filePath = $uploadDir . $fileName;
	
					// Move the uploaded file to the destination directory
					if(move_uploaded_file($_FILES[$field]['tmp_name'], $filePath)){
						// Update the image path in the database
						$imagePath = "uploads/farms/" . $fileName;
						$this->conn->query("UPDATE `farm` SET {$field} = '{$imagePath}' WHERE Id = '{$pid}'");
					} else {
						$resp['msg'] = "Failed to move uploaded file.";
					}
				}
			}
		} else {
			$resp['status'] = 'failed';
			if(empty($Id))
				$resp['msg'] = "Farm has failed to save.";
			else
				$resp['msg'] = "Farm has failed to update.";
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
	
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success', $resp['msg']);
		return json_encode($resp);
	}
	public function save_crop_disease() {
        extract($_POST);

        // Escape all POST data to prevent SQL injection
        foreach ($_POST as $key => $value) {
            $_POST[$key] = $this->conn->real_escape_string($value);
        }

        // Check if Name exists in pest_disease_archive to get pest_disease_id
        $pestDiseaseId = null;
        if (!empty($Name)) {
            $name = $this->conn->real_escape_string($Name);
            $query = "SELECT id FROM `pest_disease_archive` WHERE `name` = '{$name}' LIMIT 1";
            $result = $this->conn->query($query);

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $pestDiseaseId = $row['id'];
            }
        }

        // Insert or update data into croppestdisease table
        $data = '';
        foreach ($_POST as $key => $value) {
            if ($key !== 'Id' && $key !== 'images') {
                if (!empty($data)) $data .= ", ";
                $data .= "`{$key}` = '{$value}'";
            }
        }

        if (empty($Id)) {
            $sql = "INSERT INTO `croppestdisease` SET {$data}";
        } else {
            $sql = "UPDATE `croppestdisease` SET {$data} WHERE Id = '{$Id}'";
        }

        $save = $this->conn->query($sql);

        if ($save) {
            $resp['status'] = 'success';
            $resp['cropPestDiseaseId'] = empty($Id) ? $this->conn->insert_id : $Id;
        } else {
            $resp['status'] = 'failed';
            $resp['msg'] = 'An error occurred while saving the pest/disease data.';
            $resp['error'] = $this->conn->error;
        }

        return json_encode($resp);
    }

	public function save_pest_disease_images() {
		// Retrieve data from POST request
		$cropPestDiseaseId = intval($this->conn->real_escape_string($_POST['cropPestDiseaseId']));
		$VendorID = intval($this->conn->real_escape_string($_POST['VendorID']));
		$CropID = intval($this->conn->real_escape_string($_POST['CropID']));
		$pestDiseaseId = intval($this->conn->real_escape_string($_POST['pestDiseaseId']));
	
		// Initialize response array
		$resp = ['status' => 'failed', 'msg' => ''];
	
		// Handle file uploads
		if (isset($_FILES['images']) && count($_FILES['images']['tmp_name']) > 0) {
			$uploadDir = base_app . 'uploads/pestordisease/';
			foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
				$file_name = $_FILES['images']['name'][$key];
				$file_tmp = $_FILES['images']['tmp_name'][$key];
				$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
				$newFileName = 'pest_disease-' . strtotime(date('Y-m-d H:i')) . ".$file_ext";
	
				// Attempt to move the uploaded file to the designated directory
				if (move_uploaded_file($file_tmp, $uploadDir . $newFileName)) {
					$imagePath = 'uploads/pestordisease/' . $newFileName;
					$imagePathEscaped = $this->conn->real_escape_string($imagePath);
	
					// Insert image data into pest_disease_images table
					$insertSql = "INSERT INTO `pest_disease_images` (`image_path`, `vendor_id`, `pest_disease_id`, `crop_id`)
								  VALUES ('{$imagePathEscaped}', '{$VendorID}', '{$pestDiseaseId}', '{$CropID}')";
					if ($this->conn->query($insertSql)) {
						$resp['status'] = 'success';
					} else {
						// Log and report the error
						$resp['msg'] = 'Error inserting image data: ' . $this->conn->error;
						error_log('Error inserting image data: ' . $this->conn->error);
					}
				} else {
					// Log and report the error
					$resp['msg'] = 'Error moving uploaded file';
					error_log('Error moving uploaded file: ' . $file_name);
				}
			}
		} else {
			// Log and report the error
			$resp['msg'] = 'No images to upload';
			error_log('No images to upload');
		}
	
		return json_encode($resp);
	}
	
	
	public function save_harvest2() {
		extract($_POST);
	
		// Sanitize input data
		$CropId = intval($this->conn->real_escape_string($CropId));
		$HarvestedDate = $this->conn->real_escape_string($HarvestedDate);
		$AmountOfHarvest = floatval($this->conn->real_escape_string($AmountOfHarvest));
		$Paid = intval($this->conn->real_escape_string($Paid));
	
		// Insert data into the harvest table
		$sql = "INSERT INTO `harvest` (`CropId`, `HarvestedDate`, `AmountOfHarvest`, `Paid`) 
				VALUES ('$CropId', '$HarvestedDate', '$AmountOfHarvest', '$Paid')";
		if ($this->conn->query($sql) === TRUE) {
			$resp['status'] = 'success';
			$resp['msg'] = 'Harvest data saved successfully.';
		} else {
			$resp['status'] = 'error';
			$resp['msg'] = 'Error saving harvest data: ' . $this->conn->error;
		}
		return json_encode($resp);
	}
	
	public function save_review() {
		extract($_POST);
	
		// Sanitize input data
		$product_id = intval($this->conn->real_escape_string($product_id));
		$order_id = intval($this->conn->real_escape_string($order_id));
		$client_id = intval($this->conn->real_escape_string($client_id));
		$rating = intval($this->conn->real_escape_string($rating));
		$comment = $this->conn->real_escape_string($comment);
	
		// Insert review into the review table
		$sql = "INSERT INTO `review` (`product_id`, `order_id`, `client_id`, `rating`, `comment`) 
				VALUES ('$product_id', '$order_id', '$client_id', '$rating', '$comment')";
		if ($this->conn->query($sql) === TRUE) {
			$resp['status'] = 'success';
			$resp['msg'] = 'Review saved successfully.';
		} else {
			$resp['status'] = 'error';
			$resp['msg'] = 'Error saving review: ' . $this->conn->error;
		}
		return json_encode($resp);
	}
	
	public function save_data() {
        extract($_POST);

        // Sanitize input data
        $data = $this->conn->real_escape_string($data);

        // Insert data into the test table
        $sql = "INSERT INTO `test` (`data`) VALUES ('$data')";
        if ($this->conn->query($sql) === TRUE) {
            $resp['status'] = 'success';
            $resp['msg'] = 'Data saved successfully.';
        } else {
            $resp['status'] = 'error';
            $resp['msg'] = 'Error saving data: ' . $this->conn->error;
        }
        return json_encode($resp);
    }
	public function update_vendor_commission_paid_status() {
		extract($_POST);
		$vendor_id = $this->conn->real_escape_string($vendor_id);
		$month = $this->conn->real_escape_string($month);
		$paid = $this->conn->real_escape_string($paid);
	
		$update = $this->conn->query("UPDATE vendor_commissions SET paid = '{$paid}' WHERE vendor_id = '{$vendor_id}' AND month = '{$month}'");
	
		if ($update) {
			return json_encode(['status' => 'success']);
		} else {
			return json_encode(['status' => 'error', 'message' => $this->conn->error]);
		}
	}

	function save_activity(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `crop_activity` set {$data} ";
		} else {
			$sql = "UPDATE `crop_activity` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$pid = empty($id) ? $this->conn->insert_id : $id;
			$resp['pid'] = $pid;
			$resp['status'] = 'success';
			if(empty($id))
				$resp['msg'] = "New activity successfully saved.";
			else
				$resp['msg'] = "Activity successfully updated.";
	
		} else {
			$resp['status'] = 'failed';
			if(empty($id))
				$resp['msg'] = "Activity has failed to save.";
			else
				$resp['msg'] = "Activity has failed to update.";
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
	
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success', $resp['msg']);
		return json_encode($resp);
	}
	
	function delete_crop(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `crop` SET `is_deleted` = 1 WHERE `Id` = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Crop has been marked as deleted successfully.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function delete_farm(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `farm` SET `delete_flag` = 1 WHERE `Id` = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Farm has been marked as deleted successfully.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function delete_activity(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `crop_activity` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$resp['msg'] = "Activity has been deleted successfully.";
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "Activity has failed to delete.";
			$resp['error'] = $this->conn->error;
		}
		if($resp['status'] == 'success'){
			$this->settings->set_flashdata('success', $resp['msg']);
		}
		return json_encode($resp);
	}
	function delete_report(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `pestanddiseasereport` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$resp['msg'] = "Report has been deleted successfully.";
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "Report has failed to delete.";
			$resp['error'] = $this->conn->error;
		}
		if($resp['status'] == 'success'){
			$this->settings->set_flashdata('success', $resp['msg']);
		}
		return json_encode($resp);
	}
	function delete_croppestanddisease(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `croppestdisease` where Id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$resp['msg'] = "Pest or disease record has been deleted successfully.";
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "Pest or disease record has failed to delete.";
			$resp['error'] = $this->conn->error;
		}
		if($resp['status'] == 'success'){
			$this->settings->set_flashdata('success', $resp['msg']);
		}
		return json_encode($resp);
	}
	function delete_harvest(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `harvest` where Id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$resp['msg'] = "Harvest record has been deleted successfully.";
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "Harvest record has failed to delete.";
			$resp['error'] = $this->conn->error;
		}
		if($resp['status'] == 'success'){
			$this->settings->set_flashdata('success', $resp['msg']);
		}
		return json_encode($resp);
	}
	
	function save_pest_and_disease_report(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `pestanddiseasereport` set {$data} ";
		} else {
			$sql = "UPDATE `pestanddiseasereport` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$pid = empty($id) ? $this->conn->insert_id : $id;
			$resp['pid'] = $pid;
			$resp['status'] = 'success';
			if(empty($id))
				$resp['msg'] = "New report successfully saved.";
			else
				$resp['msg'] = "Report successfully updated.";
	
		} else {
			$resp['status'] = 'failed';
			if(empty($id))
				$resp['msg'] = "Report has failed to save.";
			else
				$resp['msg'] = "Report has failed to update.";
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
	
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success', $resp['msg']);
		return json_encode($resp);
	}
	
	function save_esignature(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `vendor_list` set {$data} ";
		} else {
			$sql = "UPDATE `vendor_list` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$pid = empty($id) ? $this->conn->insert_id : $id;
			$resp['pid'] = $pid;
			$resp['status'] = 'success';
			if(empty($id))
				$resp['msg'] = "New e-signature successfully saved.";
			else
				$resp['msg'] = "E-signature successfully updated.";
				
			if (isset($_FILES['esignature']) && $_FILES['esignature']['tmp_name'] != '') {
				$uploadDir = base_app . "uploads/esignature/";
				if (!is_dir($uploadDir)) {
					mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
				}
	
				$filePath = $uploadDir . basename($_FILES['esignature']['name']);
	
				if (move_uploaded_file($_FILES['esignature']['tmp_name'], $filePath)) {
					$esignaturePath = "uploads/esignature/" . basename($_FILES['esignature']['name']);
					// Update the e-signature path in the database with the original image name
					$this->conn->query("UPDATE `vendor_list` set esignature = '{$esignaturePath}' where id = '{$pid}' ");
				} else {
					$resp['msg'] = "Failed to upload e-signature.";
				}
			}
		} else {
			$resp['status'] = 'failed';
			if(empty($id))
				$resp['msg'] = "E-signature has failed to save.";
			else
				$resp['msg'] = "E-signature has failed to update.";
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
	
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success', $resp['msg']);
		return json_encode($resp);
	}
	function save_croppestdisease(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id', 'CropID'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
			}
		}
	
		// Handle image uploads
		$imageFields = ['Image1', 'Image2', 'Image3', 'Image4', 'Image5'];
		foreach ($imageFields as $i => $field) {
			if (isset($_FILES['images']['tmp_name'][$i]) && $_FILES['images']['tmp_name'][$i] != '') {
				$uploadDir = base_app."uploads/croppestdisease/";
				if (!is_dir($uploadDir)) {
					mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
				}
				$fileName = uniqid() . ".png"; // Generate a unique file name
				$filePath = $uploadDir . $fileName;
				
				// Move the uploaded file to the destination directory
				if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $filePath)) {
					// Prepare the image field data
					if (!empty($data)) $data .= ",";
					$data .= " `{$field}`='uploads/croppestdisease/{$fileName}' ";
				} else {
					$resp['status'] = 'failed';
					$resp['msg'] = "Failed to move uploaded file.";
					return json_encode($resp);
				}
			}
		}
	
		// Check if the pest/disease already exists for the given crop
		$check = $this->conn->query("SELECT * FROM `croppestdisease` WHERE crop_id = '{$CropID}' AND `Name` = '{$Name}' ".(!empty($id) ? " AND id != '{$id}'" : ""))->num_rows;
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Pest/Disease Name Already exists.';
		} else {
			if(empty($id)){
				$sql = "INSERT INTO `croppestdisease` SET `CropID` = '{$CropID}', {$data}";
			} else {
				$sql = "UPDATE `croppestdisease` SET {$data} WHERE id = '{$id}'";
			}
			$save = $this->conn->query($sql);
			if($save){
				$pdid = empty($id) ? $this->conn->insert_id : $id;
				$resp['pdid'] = $pdid;
				$resp['status'] = 'success';
				if(empty($id))
					$resp['msg'] = "New Pest/Disease successfully saved.";
				else
					$resp['msg'] = "Pest/Disease successfully updated.";
			} else {
				$resp['status'] = 'failed';
				if(empty($id))
					$resp['msg'] = "Pest/Disease has failed to save.";
				else
					$resp['msg'] = "Pest/Disease has failed to update.";
				$resp['err'] = $this->conn->error."[{$sql}]";
			}
		}
	
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function save_crop_pd(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k, array('Id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
			}
		}
		if(empty($Id)){
			$sql = "INSERT INTO `croppestdisease` set {$data} ";
		} else {
			$sql = "UPDATE `croppestdisease` set {$data} where Id = '{$Id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$pid = empty($Id) ? $this->conn->insert_id : $Id;
			$resp['pid'] = $pid;
			$resp['status'] = 'success';
			if(empty($Id))
				$resp['msg'] = "New Crop successfully saved.";
			else
				$resp['msg'] = "Crop successfully updated.";
			
			// Set upload directory
			$uploadDir = base_app."uploads/pestordisease/";
			if(!is_dir($uploadDir))
				mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
	
			// Handle image uploads
			$imageFields = ['img' => 'Image1', 'img_2' => 'Image2', 'img_3' => 'Image3', 'img_4' => 'Image4', 'img_5' => 'Image5'];
			foreach ($imageFields as $imgField => $dbField) {
				if(isset($_FILES[$imgField]) && $_FILES[$imgField]['tmp_name'] != ''){
					$fileName = $pid . "_{$imgField}.png"; // Customize the file name
					$filePath = $uploadDir . $fileName;
					
					// Move the uploaded file to the destination directory
					if(move_uploaded_file($_FILES[$imgField]['tmp_name'], $filePath)){
						// Update the image path in the database
						$imagePath = "uploads/pestordisease/" . $fileName;
						$this->conn->query("UPDATE `croppestdisease` SET {$dbField} = '{$imagePath}' WHERE Id = '{$pid}'");
					} else {
						$resp['msg'] = "Failed to move uploaded file.";
					}
				}
			}
		} else {
			$resp['status'] = 'failed';
			if(empty($Id))
				$resp['msg'] = "Crop has failed to save.";
			else
				$resp['msg'] = "Crop has failed to update.";
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
	
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success', $resp['msg']);
		return json_encode($resp);
	}
	function save_pest_disease_archive(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id'))){
				if(!empty($data)) $data .= ",";
				$data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `pest_disease_archive` set {$data} ";
		} else {
			$sql = "UPDATE `pest_disease_archive` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$pid = empty($id) ? $this->conn->insert_id : $id;
			$resp['pid'] = $pid;
			$resp['status'] = 'success';
			if(empty($id))
				$resp['msg'] = "New pest or disease entry successfully saved.";
			else
				$resp['msg'] = "Pest or disease entry successfully updated.";
		} else {
			$resp['status'] = 'failed';
			if(empty($id))
				$resp['msg'] = "Pest or disease entry has failed to save.";
			else
				$resp['msg'] = "Pest or disease entry has failed to update.";
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
	
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success', $resp['msg']);
		return json_encode($resp);
	}
	function save_crop_activity_suggestions(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id'))){
				if(!empty($data)) $data .= ",";
				$data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `crop_activity_suggestions` set {$data} ";
		} else {
			$sql = "UPDATE `crop_activity_suggestions` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$pid = empty($id) ? $this->conn->insert_id : $id;
			$resp['pid'] = $pid;
			$resp['status'] = 'success';
			if(empty($id))
				$resp['msg'] = "New crop activity suggestion successfully saved.";
			else
				$resp['msg'] = "Crop activity suggestion successfully updated.";
		} else {
			$resp['status'] = 'failed';
			if(empty($id))
				$resp['msg'] = "Crop activity suggestion has failed to save.";
			else
				$resp['msg'] = "Crop activity suggestion has failed to update.";
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
	
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success', $resp['msg']);
		return json_encode($resp);
	}
	
	function save_review2(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id'))){
				if(!empty($data)) $data .= ",";
				$data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `review` set {$data} ";
		} else {
			$sql = "UPDATE `review` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$pid = empty($id) ? $this->conn->insert_id : $id;
			$resp['pid'] = $pid;
			$resp['status'] = 'success';
			if(empty($id))
				$resp['msg'] = "New review successfully saved.";
			else
				$resp['msg'] = "Review successfully updated.";
		} else {
			$resp['status'] = 'failed';
			if(empty($id))
				$resp['msg'] = "Review has failed to save.";
			else
				$resp['msg'] = "Review has failed to update.";
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
	
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success', $resp['msg']);
		return json_encode($resp);
	}
	
	function save_interested_client(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id'))){
				if(!empty($data)) $data .= ",";
				$data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `interested_clients` set {$data} ";
		} else {
			$sql = "UPDATE `interested_clients` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$client_id = empty($id) ? $this->conn->insert_id : $id;
			$resp['client_id'] = $client_id;
			$resp['status'] = 'success';
			if(empty($id))
				$resp['msg'] = "New interested client successfully saved.";
			else
				$resp['msg'] = "Interested client information successfully updated.";
		} else {
			$resp['status'] = 'failed';
			if(empty($id))
				$resp['msg'] = "Saving interested client failed.";
			else
				$resp['msg'] = "Updating interested client failed.";
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
	
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success', $resp['msg']);
		return json_encode($resp);
	}
	
	function update_client_interest_status() {
		extract($_POST);
		
		// Prepare the data for the SQL query
		$data = "";
		foreach($_POST as $k => $v) {
			if(!in_array($k, array('interest_id'))) {
				if(!empty($data)) $data .= ",";
				$data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
			}
		}
	
		// Determine if we're inserting a new record or updating an existing one
		if(empty($interest_id)) {
			// Insert a new record
			$sql = "INSERT INTO `interested_clients` SET {$data}";
		} else {
			// Update an existing record
			$sql = "UPDATE `interested_clients` SET {$data} WHERE `id` = '{$interest_id}'";
		}
	
		// Execute the query
		$save = $this->conn->query($sql);
	
		// Prepare the response
		if($save) {
			$client_id = empty($interest_id) ? $this->conn->insert_id : $interest_id;
			$resp['client_id'] = $client_id;
			$resp['status'] = 'success';
			if(empty($interest_id))
				$resp['msg'] = "New interested client status successfully saved.";
			else
				$resp['msg'] = "Interested client status successfully updated.";
		} else {
			$resp['status'] = 'failed';
			if(empty($interest_id))
				$resp['msg'] = "Saving interested client status failed.";
			else
				$resp['msg'] = "Updating interested client status failed.";
			$resp['error'] = $this->conn->error . "[{$sql}]";
		}
	
		// Set flash data if save was successful
		if($resp['status'] == 'success') {
			$this->settings->set_flashdata('success', $resp['msg']);
		}
		
		// Return the response as JSON
		return json_encode($resp);
	}
	
	
	
	
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'save_interested_client':
		echo $Master->save_interested_client();
	break;
	case 'update_client_interest_status':
		echo $Master->update_client_interest_status();
	break;
	case 'save_crop':
		echo $Master->save_crop();
	break;
	case 'save_review2':
		echo $Master->save_review2();
	break;
	case 'save_crop_pd':
		echo $Master->save_crop_pd();
	break;
	case 'save_croppestdisease':
		echo $Master->save_croppestdisease();
	break;
	case 'save_crop_activity_suggestions':
		echo $Master->save_crop_activity_suggestions();
	break;
	case 'delete_crop_activity_suggestion':
		echo $Master->delete_crop_activity_suggestion();
	break;
	case 'delete_archive':
		echo $Master->delete_archive();
	break;	
	case 'save_pest_disease_archive':
		echo $Master->save_pest_disease_archive();
	break;
	case 'save_pest_and_disease_report':
		echo $Master->save_pest_and_disease_report();
	break;
	case 'save_pd':
		echo $Master->save_pd();
	break;
	case 'save_activity':
		echo $Master->save_activity();
	break;
	case 'save_harvest':
		echo $Master->save_harvest();
	break;
	case 'save_crop_disease':
		echo $Master->save_crop_disease();
	break;
	case 'save_pest_disease_images':
		echo $Master->save_pest_disease_images();
	break;
	case 'save_farm':
		echo $Master->save_farm();
	break;
	case 'save_shop_type':
		echo $Master->save_shop_type();
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
	case 'delete_crop':
		echo $Master->delete_crop();
	break;
	case 'delete_farm':
		echo $Master->delete_farm();
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
	case 'delete_activity':
		echo $Master->delete_activity();
		case 'delete_report':
			echo $Master->delete_report();
	break;
	case 'delete_harvest':
		echo $Master->delete_harvest();
	break;
	case 'delete_croppestanddisease':
		echo $Master->delete_croppestanddisease();
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
	case 'save_esignature':
		echo $Master->save_esignature();
	break;
	case 'save_review':
		echo $Master->save_review();
	break;
	case 'save_data':
        echo $Master->save_data();
        break;
		case 'update_vendor_commission_paid_status':
			echo $Master->update_vendor_commission_paid_status();
			break;
		
	default:
		// echo $sysset->index();
		break;
}
?>