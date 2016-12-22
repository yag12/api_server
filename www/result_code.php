<?php
include_once dirname(__DIR__) . '/config/Result.php';
$result = new \Config\Result;
$oClass = new ReflectionClass($result);
$constants = $oClass->getConstants();
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<title>result code page</title>
</head>
<body>

<?php if(!empty($constants)): ?>
<table border=1 cellpadding="2">
	<tr>
		<th>코드번호</th>
		<th>코드키</th>
		<th>설명</th>
	</tr>
	<?php foreach($constants as $key=>$val): ?>
	<?php if(substr($key, 0, 3) == 'MSG') continue; ?>
	<tr>
		<th><?php echo $val; ?></th>
		<td><?php echo $key; ?></td>
		<td><?php echo !empty($constants['MSG_' . $key]) ? $constants['MSG_' . $key] : '-'; ?></td>
	</tr>
	<?php endforeach; ?>
</table>
<?php endif; ?>

</body>
</html>
