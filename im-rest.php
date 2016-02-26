<?php
include('http.php');
include_once('cred.php');

function get_auth_data() {

	$fields = array("id","type","host","username","password","proxy","token_type","project","public_key","private_key", "certificate");
    $user = $_SESSION['user'];
    $password = $_SESSION['password'];
    // esto por si usamos la autorizacion del servidor web
    //$user = $_SERVER['PHP_AUTH_USER']
    //$password = $_SERVER['PHP_AUTH_PW']

    $auth = NULL;
    $creds = get_credentials($user, $password);
    if (!is_null($creds)) {
        $auth = "";
        foreach ($creds as $cred) {
            if ($cred['enabled']) {
                foreach ($fields as $field) {
                	if (!is_null($cred[$field]) && strlen(trim($cred[$field])) > 0) {
	                	if ($field == "certificate") {
	                		$auth = $auth . "password = " . $cred[$field] . "; ";
	                	} else {
	                		$auth = $auth . $field ." = " . $cred[$field] . "; ";
	                	}
                	}
                }
                $auth = substr( $auth, 0, strlen($auth)-2 ). "\\n";
            }
        }
    }

    return $auth;
}

function GetErrorMessage($output) {
	$pos_ini = strpos($output, "<pre>");
	$pos_fin = strpos($output, "</pre>");

	if ($pos_ini && $pos_fin) {
		$len_fin = $pos_fin - $pos_ini - 5;
		return substr($output, $pos_ini+5, $len_fin);
	} else {
		return $output;
	}
}

function BasicRESTCall($verb, $host, $port, $path, $params=array(), $extra_headers=array()) {
	include('config.php');
	$auth = get_auth_data();
	$headers = array("Authorization:" . $auth);
	$headers = array_merge($headers, $extra_headers);

	if ($im_use_rest_ssl) {
		$protocol = 'https';
	} else {
		$protocol = 'http';
	}
	
	try {
		$res = Http::connect($host, $port, $protocol)
		->setHeaders($headers)
		->exec($verb, $path, $params);
			
		$status = $res->getStatus();
		$output = $res->getOutput();
	} catch (Exception $e) {
		$status = 600;
		$output = "Exception: " . $e->getMessage();
	}

	$res = $output;
	if ($status != 200) {
		$res = 'Error: Code: ' . strval($status) . '. ' . GetErrorMessage($output);
	}

	return new Http_response($status, $res);
}

function GetInfrastructureList($host, $port) {
	$res = BasicRESTCall("GET", $host, $port, '/infrastructures');

	if ($res->getStatus() != 200) {
		return $res->getOutput();
	} else {
		$inf_urls = split("\n", $res->getOutput());
		$inf_ids = array();
		foreach ($inf_urls as $inf_url) {
			$inf_id = trim(basename(parse_url($inf_url, PHP_URL_PATH)));
			if (strlen($inf_id) > 0) {
				$inf_ids[] = $inf_id;
			}
		}
		return $inf_ids;
	}
}

function GetInfrastructureInfo($host, $port, $id) {
	$res = BasicRESTCall("GET", $host, $port, '/infrastructures/'.$id);

	if ($res->getStatus() != 200) {
		return 'Error: Code: ' . strval($res->getStatus()) . '. ' . GetErrorMessage($output);
	} else {
		$vm_urls = split("\n", $res->getOutput());
		$vm_ids = array();
		foreach ($vm_urls as $vm_url) {
			$vm_id = trim(basename(parse_url($vm_url, PHP_URL_PATH)));
			if (strlen($vm_id) > 0) {
				$vm_ids[] = $vm_id;
			}
		}
		return $vm_ids;
	}
}

function GetInfrastructureState($host, $port, $id) {
	$res = BasicRESTCall("GET", $host, $port, '/infrastructures/'.$id.'/state');

	if ($res->getStatus() != 200) {
		return $res->getOutput();
	} else {
		return json_decode($res->getOutput())->state;
	}
}

function DestroyInfrastructure($host, $port, $id) {
	$res = BasicRESTCall("DELETE", $host, $port, '/infrastructures/'.$id);
	
	if ($res->getStatus() != 200) {
		return $res->getOutput();
	} else {
		return "";
	}
}

function GetVMInfo($host, $port, $inf_id, $vm_id) {
	$res = BasicRESTCall("GET", $host, $port, '/infrastructures/' . $inf_id . '/vms/' . $vm_id);
	return $res->getOutput();
}

function GetInfrastructureContMsg($host, $port, $id) {
	$res = BasicRESTCall("GET", $host, $port, '/infrastructures/'.$id.'/contmsg');
	return $res->getOutput();
}

function GetVMProperty($host, $port, $inf_id, $vm_id, $property) {
	$res = BasicRESTCall("GET", $host, $port, '/infrastructures/' . $inf_id . '/vms/' . $vm_id . "/" . $property);
	return $res->getOutput();
}

function GetVMContMsg($host, $port, $inf_id, $vm_id) {
	$res = BasicRESTCall("GET", $host, $port, '/infrastructures/' . $inf_id . '/vms/' . $vm_id . "/contmsg");
	return $res->getOutput();
}

function CreateInfrastructure($host, $port, $radl) {
	$headers = array('Content-Type: text/plain', 'Content-Length: ' . strlen($radl));
	$res = BasicRESTCall("POST", $host, $port, '/infrastructures', $radl, $headers);
	return $res->getOutput();
}

function StartVM($host, $port, $inf_id, $vm_id) {
	$res = BasicRESTCall("PUT", $host, $port, '/infrastructures/' . $inf_id . '/vms/' . $vm_id . "/start");
	return $res->getOutput();
}

function StopVM($host, $port, $inf_id, $vm_id) {
	$res = BasicRESTCall("PUT", $host, $port, '/infrastructures/' . $inf_id . '/vms/' . $vm_id . "/stop");
	return $res->getOutput();
}

function AddResource($host, $port, $inf_id, $radl) {
	$headers = array('Content-Type: text/plain', 'Content-Length: ' . strlen($radl));
	$res = BasicRESTCall("POST", $host, $port, '/infrastructures/' . $inf_id, $radl, $headers);
	return $res->getOutput();
}

function RemoveResource($host, $port, $inf_id, $vm_id) {
	$res = BasicRESTCall("DELETE", $host, $port, '/infrastructures/' . $inf_id . '/vms/' . $vm_id);
	return $res->getOutput();
}

function Reconfigure($host, $port, $inf_id, $radl) {
	$headers = array('Content-Type: text/plain', 'Content-Length: ' . strlen($radl));
	$res = BasicRESTCall("PUT", $host, $port, '/infrastructures/' . $inf_id . '/reconfigure', $radl, $headers);
	return $res->getOutput();
}
?>