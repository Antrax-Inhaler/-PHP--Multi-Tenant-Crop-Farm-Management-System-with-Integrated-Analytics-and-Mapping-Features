<?php
require_once('../config.php');
Class Users extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function save_nafa_user(){
        extract($_POST);

        // Hash password before saving
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $chk = $this->conn->query("SELECT * FROM `NAFA` WHERE username ='{$username}'")->num_rows;
        if($chk > 0){
            return 3; // Username already exists
        }

        $qry = $this->conn->query("INSERT INTO NAFA (username, password) VALUES ('{$username}', '{$hashed_password}')");

        if($qry){
            return 1; // User saved successfully
        }else{
            return 2; // Failed to save user details
        }
    }
	public function save_users(){
		extract($_POST);
		$data = '';
		$chk = $this->conn->query("SELECT * FROM `users` where username ='{$username}' ".($id > 0 ? " and id!= '{$id}' " : ""))->num_rows;
		if($chk > 0){
			return 3; // Username already exists
			exit;
		}
		foreach($_POST as $k => $v){
			if(!in_array($k,array('id','password'))){
				if(!empty($data)) $data .= " , ";
				$data .= " {$k} = '{$v}' ";
			}
		}
		if(!empty($password)){
			$password = md5($password);
			if(!empty($data)) $data .= " , ";
			$data .= " `password` = '{$password}' ";
		}
	
		if(empty($id)){
			$qry = $this->conn->query("INSERT INTO users SET {$data}");
			if($qry){
				$id = $this->conn->insert_id;
				$this->settings->set_flashdata('success','User Details successfully saved.');
				if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
					$uploadDir = base_app."uploads/";
					if(!is_dir($uploadDir))
						mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
	
					$fileName = 'avatar-' . $id . ".png"; // You can customize the file name if needed
					$filePath = $uploadDir . $fileName;
					
					// Move the uploaded file to the destination directory
					if(move_uploaded_file($_FILES['img']['tmp_name'], $filePath)){
						// Update the avatar path in the database
						$avatarPath = "uploads/" . $fileName;
						$this->conn->query("UPDATE users SET avatar = '{$avatarPath}' WHERE id = '{$id}'");
					}else{
						return 4; // Failed to move uploaded file
					}
				}
				return 1; // User saved successfully
			}else{
				return 2; // Failed to save user details
			}
		}else{
			$qry = $this->conn->query("UPDATE users SET $data WHERE id = {$id}");
			if($qry){
				$this->settings->set_flashdata('success','User Details successfully updated.');
				if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
					$uploadDir = base_app."uploads/";
					if(!is_dir($uploadDir))
						mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
	
					$fileName = 'avatar-' . $id . ".png"; // You can customize the file name if needed
					$filePath = $uploadDir . $fileName;
					
					// Move the uploaded file to the destination directory
					if(move_uploaded_file($_FILES['img']['tmp_name'], $filePath)){
						// Update the avatar path in the database
						$avatarPath = "uploads/" . $fileName;
						$this->conn->query("UPDATE users SET avatar = '{$avatarPath}' WHERE id = '{$id}'");
					}else{
						return 4; // Failed to move uploaded file
					}
				}
				return 1; // User updated successfully
			}else{
				return "UPDATE users SET $data WHERE id = {$id}";
			}
		}
	}
	
	
	public function delete_users(){
		extract($_POST);
		$avatar = $this->conn->query("SELECT avatar FROM users where id = '{$id}'")->fetch_array()['avatar'];
		$qry = $this->conn->query("DELETE FROM users where id = $id");
		if($qry){
			$this->settings->set_flashdata('success','User Details successfully deleted.');
			if(is_file(base_app.$avatar))
				unlink(base_app.$avatar);
			$resp['status'] = 'success';
		}else{
			$resp['status'] = 'failed';
		}
		return json_encode($resp);
	}
	public function save_vendor(){
		if (!empty($_POST['password'])) {
			$_POST['password'] = md5($_POST['password']);
		} else {
			unset($_POST['password']);
		}
	
		if (empty($_POST['id'])) {
			$prefix = date('Ym-');
			$code = sprintf("%'.05d",1);
			while(true){
				$check = $this->conn->query("SELECT * FROM `vendor_list` where code = '{$prefix}{$code}'")->num_rows;
				if ($check > 0) {
					$code = sprintf("%'.05d",ceil($code) + 1);
				} else {
					break;
				}
			}
			$_POST['code'] = $prefix.$code;
		}
	
		extract($_POST);
	
		if (isset($oldpassword) && !empty($id)) {
			$current_pass = $this->conn->query("SELECT * FROM `vendor_list` where id = '{$id}'")->fetch_array()['password'];
			if (md5($oldpassword) != $current_pass) {
				$resp['status'] = 'failed';
				$resp['msg'] = ' Incorrect Current Password';
				return json_encode($resp);
				exit;
			}
		}
	
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k,['id','cpassword','oldpassword']) && !is_array($_POST[$k])) {
				$v = $this->conn->real_escape_string($v);
				if (!empty($data)) $data .= ", ";
				$data .= "`{$k}`='{$v}'";
			}
		}
	
		$check = $this->conn->query("SELECT * FROM `vendor_list` where username = '{$username}' and delete_flag = 0 ".(!empty($id) ? " and id !='{$id}'" : ''))->num_rows;
		if ($check > 0) {
			$resp['status'] = 'failed';
			$resp['msg'] = " Username already exists";
		} else {
			if (empty($id)) {
				$sql = "INSERT INTO `vendor_list` set {$data}";
			} else {
				$sql = "UPDATE `vendor_list` set {$data} where id = '{$id}'";
			}
			$save = $this->conn->query($sql);
			if ($save) {
				$resp['status'] = "success";
				$vid = empty($id) ? $this->conn->insert_id : $id;
				if (empty($id)) {
					if (strpos($_SERVER['HTTP_REFERER'], 'vendor/register.php') > -1) {
						$resp['msg'] = " Your account has been registered successfully.";
					} else {
						$resp['msg'] = " Member's Account has been registered successfully.";
					}
				} else {
					if ($this->settings->userdata('login_type') == 2) {
						$resp['msg'] = " Your account details has been updated successfully.";
					} else {
						$resp['msg'] = " Member's Account Details has been updated successfully.";
					}
				}
				 // Handle e-signature upload
				 if (isset($_FILES['esignature']) && $_FILES['esignature']['tmp_name'] != '') {
					$uploadDir = base_app . "uploads/esignature/";
					if (!is_dir($uploadDir)) {
						mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
					}
	
					$filePath = $uploadDir . basename($_FILES['esignature']['name']);
	
					if (move_uploaded_file($_FILES['esignature']['tmp_name'], $filePath)) {
						$esignaturePath = "uploads/esignature/" . basename($_FILES['esignature']['name']);
						// Update the e-signature path in the database with the original image name
						$this->conn->query("UPDATE `vendor_list` set esignature = '{$esignaturePath}' where id = '{$vid}' ");
					} else {
						$resp['msg'] = "Failed to upload e-signature.";
					}
				}
				if (isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
					$uploadDir = base_app."uploads/vendors/";
					if (!is_dir($uploadDir)) {
						mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
					}
	
					$filePath = $uploadDir . basename($_FILES['img']['name']);
	
					if (move_uploaded_file($_FILES['img']['tmp_name'], $filePath)) {
						$imagePath = "uploads/vendors/" . basename($_FILES['img']['name']);
						// Update the image path in the database with the original image name
						$this->conn->query("UPDATE `vendor_list` set avatar = '{$imagePath}' where id = '{$vid}' ");
					} else {
						$resp['msg'] = "Failed to upload image.";
					}
				}
			} else {
				$resp['status'] = 'failed';
				$resp['msg'] = " An error occurred while saving the account details.";
				$resp['error'] = $this->conn->error;
			}
		}
	
		if ($resp['status'] == 'success') {
			$this->settings->set_flashdata('success', $resp['msg']);
		}
	
		return json_encode($resp);
	}
	
	public function save_client(){
		// Hash password if provided
		if(!empty($_POST['password'])) {
			$_POST['password'] = md5($_POST['password']);
		} else {
			unset($_POST['password']);
		}
	
		// Generate unique code for new clients
		if(empty($_POST['id'])){
			$prefix = date('Ym-');
			$code = sprintf("%'.05d", 1);
			while(true){
				$check = $this->conn->query("SELECT * FROM `client_list` WHERE code = '{$prefix}{$code}'")->num_rows;
				if($check > 0){
					$code = sprintf("%'.05d", ceil($code) + 1);
				} else {
					break;
				}
			}
			$_POST['code'] = $prefix.$code;
		}
	
		extract($_POST);
	
		// Check old password for updates
		if(isset($oldpassword) && !empty($id)){
			$current_pass = $this->conn->query("SELECT * FROM `client_list` WHERE id = '{$id}'")->fetch_array()['password'];
			if(md5($oldpassword) != $current_pass){
				$resp['status'] = 'failed';
				$resp['msg'] = 'Incorrect Current Password';
				return json_encode($resp);
				exit;
			}
		}
	
		// Prepare data for SQL query
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k,['id','cpassword','oldpassword']) && !is_array($_POST[$k])){
				$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=", ";
				$data .= "`{$k}`='{$v}'";
			}
		}
	
		// Check if email already exists
		$check = $this->conn->query("SELECT * FROM `client_list` WHERE email = '{$email}' AND delete_flag = 0 " . (!empty($id) ? " AND id != '{$id}'" : ''))->num_rows;
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Email already exists";
		} else {
			// Insert or update client data
			if(empty($id)){
				$sql = "INSERT INTO `client_list` SET {$data}";
			} else {
				$sql = "UPDATE `client_list` SET {$data} WHERE id = '{$id}'";
			}
			$save = $this->conn->query($sql);
	
			if($save){
				$resp['status'] = "success";
				$vid = empty($id) ? $this->conn->insert_id : $id;
				if(empty($id)){
					$resp['msg'] = "Your account has been registered successfully.";
				} else {
					$resp['msg'] = "Your account details have been updated successfully.";
				}
	
				// Handle avatar upload
				if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
					$uploadDir = base_app."uploads/clients/";
					if(!is_dir($uploadDir))
						mkdir($uploadDir, 0777, true);
	
					$fileName = $vid . '.png';
					$filePath = $uploadDir . $fileName;
	
					// Move uploaded file to destination directory
					if(move_uploaded_file($_FILES['img']['tmp_name'], $filePath)){
						$avatarPath = "uploads/clients/" . $fileName;
						$this->conn->query("UPDATE `client_list` SET avatar = '{$avatarPath}' WHERE id = '{$vid}'");
					} else {
						$resp['msg'] .= " But Image failed to upload due to an unknown reason.";
					}
				}
			} else {
				$resp['status'] = 'failed';
				$resp['msg'] = "An error occurred while saving the account details.";
				$resp['error'] = $this->conn->error;
			}
		}
	
		if($resp['status'] == 'success') {
			$this->settings->set_flashdata('success', $resp['msg']);
		}
	
		return json_encode($resp);
	}
	
	public function delete_client(){
		extract($_POST);
		$qry = $this->conn->query("UPDATE client_list set delete_flag = 1 where id = $id");
		if($qry){
			$this->settings->set_flashdata('success',' Client Details successfully deleted.');
			$resp['status'] = 'success';
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = 'An error occured while deleting the data.';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
}

$users = new users();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
switch ($action) {
	case 'save_nafa_user':
        echo $users->save_nafa_user();
        break;
	case 'save':
		echo $users->save_users();
	break;
	case 'delete':
		echo $users->delete_users();
	break;
	case 'save_vendor':
		echo $users->save_vendor();
	break;
	case 'delete_vendor':
		echo $users->delete_vendor();
	break;
	case 'save_client':
		echo $users->save_client();
	break;
	case 'delete_client':
		echo $users->delete_client();
	default:
		// echo $sysset->index();
		break;
}