<?php
$db_server = 'localhost:27017';
$db_options = array('connect' => true);
$db_name = 'test_db';
$tb_name = 'test_table';

$data = !empty($_POST) ? $_POST : array();

if(!empty($data)){
	if(!empty($data['__DB__'])){
		$db_name = $data['__DB__'];
		unset($data['__DB__']);
	}

	if(!empty($data['__TB__'])){
		$tb_name = $data['__TB__'];
		unset($data['__TB__']);
	}

	$m = new MongoClient("mongodb://" . $db_server, $db_options);
	$db = $m->selectDB($db_name);
	$db->{$tb_name}->insert($data);
	$m->close();
}
