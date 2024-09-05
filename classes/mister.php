<?php

function save_review(){
    extract($_POST);
    $data = "";
    foreach($_POST as $k =>$v){
        if(!in_array($k,array('id'))){
            if(!empty($data)) $data .=",";
            $data .= " `{$k}`='{$this->conn->real_escape_string($v)}' ";
        }
    }
    if(empty($id)){
        $sql = "INSERT INTO `review` set {$data} ";
    
    $save = $this->conn->query($sql);
			if($save){
				$resp['status'] = 'success';
				if(empty($id))
				$resp['msg'] = " Review successfully saved.";
			}else{
				$resp['status'] = 'failed';
				$resp['err'] = $this->conn->error."[{$sql}]";
			}
		}
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
    }

?>