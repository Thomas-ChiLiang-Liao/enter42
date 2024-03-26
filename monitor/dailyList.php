<!DOCTYPE html>
<?php
  if ( !isset( $_SERVER['HTTP_X_HTTPS'] ) OR ( $_SERVER['HTTP_X_HTTPS'] != 'on' ) ) header( "Location: https://$_SERVER[SERVER_NAME]$_SERVER[REQUEST_URI]" );
  else {
    include '../config.ini.php';

    // 資料庫連線
    $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $guestId, $guestPw);    
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
      <?php
        switch ($_GET['func']) {
        case 1:
      ?> 
      <div class="col-12 offset-lg-2 col-lg-8 offset-xxl-3 col-xxl-6 mt-3">
        <div class="card">
          <div class="card-header bg-secondary text-center text-white">
            <h4>每日操作人次列表<h4>
          </div>
          <div class="card-body">
            <table class="table table-sm table-bordered table-hover table-striped">
              <thead class="table-primary">
                <tr>
                  <th class="text-center align-middle">日期</th>
                  <th class="text-center align-middle">人次</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  // 讀取每日操作人次
                  $sql = 'SELECT DATE(at + INTERVAL 8 HOUR) AS operateDate, COUNT(*) AS personTime FROM TVEREOperateRecord WHERE 1 GROUP BY DATE(at + INTERVAL 8 HOUR);';
                  $result = $pdo->query($sql);
                  while ($record = $result->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <tr class="text-center align-middle">
                  <td><a href="dailyList.php?func=2&operateDate=<?php echo $record['operateDate'] ?>&count=<?php echo $record['personTime'] ?>" class="text-primary fw-bold text-decoration-none"><?php echo $record['operateDate'] ?></a></td>
                  <td><a href="dailyList.php?func=2&operateDate=<?php echo $record['operateDate'] ?>&count=<?php echo $record['personTime'] ?>" class="text-primary fw-bold text-decoration-none"><?php echo $record['personTime'] ?></a></td>
                </tr>              
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>		 
      </div>
      <?php 
        break;
        case 2:
      ?>
      <div class="col-12 offset-lg-2 col-lg-8 offset-xxl-3 col-xxl-6 mt-3">      
        <div class="card">
          <div class="card-header bg-secondary text-center text-white">
            <h4><?php echo $_GET['operateDate'] ?>&nbsp;共&nbsp;<?php echo $_GET['count'] ?>&nbsp;人<h4>
          </div>
          <div class="card-body">
            <table class="table table-sm table-bordered table-hover table-striped">
              <thead class="table-primary">
                <tr>
                  <th class="text-center align-middle">時間</th>
                  <th class="text-center align-middle">人次</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $sql = 'SELECT HOUR( at + INTERVAL 8 HOUR ) AS operateHour,'
                    . ' COUNT( * ) AS personTime'
                    . ' FROM TVEREOperateRecord'
                    . ' WHERE DATE( at + INTERVAL 8 HOUR ) = "'.$_GET['operateDate'].'"'
                    . ' GROUP BY HOUR( at + INTERVAL 8 HOUR )'
                    . ' ORDER BY HOUR( at + INTERVAL 8 HOUR );';
                  $result = $pdo->query($sql);
                  while ($record = $result->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <tr class="text-center align-middle">
                  <td><a href="dailyList.php?func=3&operateDate=<?php echo $_GET['operateDate'] ?>&hour=<?php echo $record['operateHour'] ?>" class="text-primary fw-bold text-decoration-none"><?php echo $record['operateHour'] ?></a></td>
                  <td><a href="dailyList.php?func=3&operateDate=<?php echo $_GET['operateDate'] ?>&hour=<?php echo $record['operateHour'] ?>" class="text-primary fw-bold text-decoration-none"><?php echo $record['personTime'] ?></a></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <?php
        break;
        case 3:
      ?>
      <div class="col-12 offset-lg-2 col-lg-8 mt-3">      
        <div class="card">
          <div class="card-header bg-secondary text-center text-white">
            <h4><?php echo $_GET['operateDate'].' '.$_GET['hour'].':00:00 - '.$_GET['hour'].':59:59' ?></h4>
          </div>
          <div class="card-body">
            <table class="table table-sm table-bordered table-hover table-striped">
              <thead class="table-primary">
                <tr>
                  <th class="text-center align-middle">班級</th>
                  <th class="text-center align-middle">座號</th>
                  <th class="text-center align-middle">姓名</th>
                  <th class="text-center align-middle">時間</th>
                  <th class="text-center align-middle">紀錄</th>
                  <th class="text-center align-middle">來自IP</th>
                </tr>
              </thead>
              <tbody>
              <?php
                $sql = 'SELECT class.title AS classTitle,'
                    . ' student.id AS studentId,'
                    . ' RIGHT(student.id,2) AS seatNo,'
                    . ' student.name AS studentName,'
                    . ' TIME(at + INTERVAL 8 HOUR) AS operateTime,'
                    . ' TVEREOperateRecord.action AS action,'
                    . ' CONCAT(IF(TVEREOperateRecord.action = \'A\', "加選 ", "刪除 "),TVEREDepartment.id, TVERESchool.title, TVEREDepartment.title) AS department,'
                    . ' TVEREOperateRecord.fromIP AS fromIP'
                    . ' FROM TVEREOperateRecord'
                    . ' LEFT JOIN student ON student.id = TVEREOperateRecord.studentId'
                    . ' LEFT JOIN TVERESchool ON LEFT(TVEREOperateRecord.departmentId,3) = TVERESchool.id'
                    . ' LEFT JOIN class ON LEFT(student.id,3) = class.id'
                    . ' LEFT JOIN TVEREDepartment ON TVEREOperateRecord.departmentId = TVEREDepartment.id'
                    . ' WHERE at + INTERVAL 8 HOUR BETWEEN "'.$_GET['operateDate'].' '.$_GET['hour'].':00" and "'.$_GET['operateDate'].' '.$_GET['hour'].':59:59"'
                    . ' ORDER BY at;';
                $result = $pdo->query($sql);
                $lastId='aaaaaa';
                $lastClassTitle='真班假班';   
                while ($record = $result->fetch(PDO::FETCH_ASSOC)) {   
              ?>   
                <tr>
                  <td class="text-center align-middle"><?php echo ($record['classTitle'] == $lastClassTitle ? '' : $record['classTitle']) ?></td>
                  <td class="text-center align-middle"><?php echo ($record['studentId'] == $lastId ? '' : $record['seatNo']) ?></td>
                  <td class="text-center align-middle"><a href="studentOperateRecordList.php?studentId=<?php echo $record['studentId'] ?>" class="text-primary fw-bold text-decoration-none"><?php echo ($record['studentId'] == $lastId ? '' : $record['studentName']) ?></a></td>
                  <td class="text-center align-middle"><?php echo $record['operateTime'] ?></td>
                  <td class="<?php echo ( $record['action'] == 'A' ? '' : 'text-danger text-decoration-line-through') ?>"><?php echo $record['department'] ?></td>
                  <td class="text-center align-middle"><?php echo $record['fromIP'] ?></td>
                </tr>  
              <?php
                  $lastClassTitle = $record['classTitle'];
                  $lastId = $record['studentId'];
                }
              ?>  
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <?php } ?>				
    </div>
	</div>
</body> 
</html>
<?php } ?>