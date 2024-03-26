<!DOCTYPE html>
<?php
  if ( !isset( $_SERVER['HTTP_X_HTTPS'] ) OR ( $_SERVER['HTTP_X_HTTPS'] != 'on' ) ) header( "Location: https://$_SERVER[SERVER_NAME]$_SERVER[REQUEST_URI]" );
  else {
    include '../config.ini.php';

    // 資料庫連線
    $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $guestId, $guestPw);

    // 查詢此生姓名
    $sql = 'SELECT student.name AS studentName FROM student WHERE student.id = "'.$_REQUEST['studentId'].'";';
    $result = $pdo->query($sql);
    $field = $result->fetch(PDO::FETCH_ASSOC);
    $studentName = $field['studentName'];
  
    /*********************
    * 查詢此生的操作紀錄 *
    *********************/
    $sql = 'SELECT'
    	. ' TVEREOperateRecord.action AS action,'
    	. ' CONCAT(TVEREDepartment.id, TVERESchool.title, TVEREDepartment.title) AS title,'
    	. ' at + INTERVAL 8 HOUR AS actionTime,'
    	. ' fromIP AS fromIP'
    . ' FROM TVEREOperateRecord'
    . ' LEFT JOIN student ON TVEREOperateRecord.studentId = student.id'
    . ' LEFT JOIN TVERESchool ON LEFT(TVEREOperateRecord.departmentId,3) = TVERESchool.id'
    . ' LEFT JOIN TVEREDepartment ON TVEREOperateRecord.departmentId = TVEREDepartment.id'
    . ' WHERE TVEREOperateRecord.studentId = "'.$_GET['studentId'].'"'
    . ' ORDER BY actionTime ASC;';
	  $result = $pdo->query($sql);
	
 ?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <title>預選系統-操作紀錄</title>
  <link rel="icon" href="../images/<?php echo ( $_SERVER["SERVER_NAME"] == "yy33.us" ? "website-design.png" : "logo.icon.png"); ?>" type="image/x-icon">
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
    
    <div class="row">
      <div class="col-12 col-lg-8 offset-lg-2 mt-3">
        <div class="card">
          <div class="card-header bg-secondary text-center text-white">
            <h4><?php echo $studentName; ?>&nbsp;的預選操作紀錄</h4>
          </div>
          <div class="card-body">
            <table class="table table-sm table-bordered table-hover table-striped">
              <thead class="table-primary">
                <tr>
                  <th class="text-center align-middle">序</th>
                  <th class="text-center align-middle">時間</th>
                  <th class="text-center align-middle">動作</th>
                  <th class="text-center align-middle">校系代碼及名稱</th>
                  <th class="text-center align-middle">來自</th>
                </tr>
              </thead>
              <tbody>
                <?php $i=1; while ($record = $result->fetch(PDO::FETCH_ASSOC)) { ?>
                <tr>
                  <td class="text-center align-middle"><?php echo $i++; ?></td>
                  <td class="text-center align-middle <?php echo ( $record['action'] == 'A' ? '' : 'text-danger'); ?>"><?php echo $record['actionTime']; ?></td>
                  <td class="text-center align-middle <?php echo ( $record['action'] == 'A' ? '' : 'text-danger'); ?>"><?php echo ( $record['action'] == 'A' ? '增加' : '刪除'); ?></td>
                  <td class="<?php echo ( $record['action'] == 'A' ? '' : 'text-danger text-decoration-line-through'); ?>"><?php echo $record['title']; ?></td>
                  <td class="text-center align-middle <?php echo ( $record['action'] == 'A' ? '' : 'text-danger'); ?>"><?php echo $record['fromIP']; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
	</div>
</body> 
</html>
<?php } ?>