<!DOCTYPE html>
<?php
  if ( !isset( $_SERVER['HTTP_X_HTTPS'] ) OR ( $_SERVER['HTTP_X_HTTPS'] != 'on' ) ) header( "Location: https://$_SERVER[SERVER_NAME]$_SERVER[REQUEST_URI]" );
  else {
    include '../config.ini.php';
 ?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <title>預選系統-操作紀錄</title>
  <link rel="icon" href="../images/logo.icon.jpg" type="image/x-icon">
  <link rel="stylesheet" href="../styles.css">
</head>
<body>
  <div class="container-fluid">
  	<!-- 標題列 -->
  	<div class="row d-none d-sm-block">
  		<div class="col-12 m-0 pt-3 pb-1 bg-primary">
  			<h3 class="text-center text-white"><?php echo $vhSchool; ?>預選系統</h4>
  		</div>
  	</div>
		<div class="mt-3 text-center">
			<a class="btn btn-secondary" href="dailyList.php?func=1">每日操作人次</a>
			<a class="btn btn-secondary" href="operateTimesListByTimes.php?func=1">每人操作次數(由大到小排列)</a>
			<a class="btn btn-secondary" href="operateTimesListByClass.php?func=1">每人操作次數--以班查詢</a>
    </div>      					 					
	</div>
</body> 
</html>
<?php } ?>