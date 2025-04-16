<!DOCTYPE html>
<?php
  session_start();
  if ( !isset( $_SERVER['HTTP_X_HTTPS'] ) OR ( $_SERVER['HTTP_X_HTTPS'] != 'on' ) ) header( "Location: $_SESSION[serverRoot]" );
  else {
    include 'menu.php';
    // 資料庫連線
    $pdo = new PDO("mysql:host=$_SESSION[server]:3307;dbname=enter42;charset=utf8mb4", $_SESSION['account'], $_SESSION['password']);  

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
</head>
<body>
  <?php menu(''); ?>
  <div class="container-fluid">    
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