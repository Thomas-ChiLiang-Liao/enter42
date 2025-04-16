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
  
    /************************************************
    * 查詢資料庫中每生的查詢紀錄，依每人總次數由大到小排列。 *
    ************************************************/
    $sql = 'SELECT'
      . ' student.name AS studentName,'
      . ' class.title AS classTitle,'
      . ' student.id AS studentId,'
      . ' RIGHT(student.id,2) AS seatNo,'
      . ' COUNT(*) AS times'
      . ' FROM TVEREOperateRecord'
      . ' LEFT JOIN student ON TVEREOperateRecord.studentId = student.id'
      . ' LEFT JOIN class ON LEFT(student.id,3) = class.id'
      . ' WHERE 1'
      . ' GROUP BY TVEREOperateRecord.studentId'
      . ' ORDER BY times DESC, TVEREOperateRecord.studentId;';
    $result = $pdo->query($sql);
    $lastClassTitle = "";
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
  <?php menu('operateTimesListByTimes'); ?>
  <div class="container-fluid">  
    <div class="row">
      <div class="col-12 col-lg-8 offset-lg-2 col-xxl-6 offset-xxl-3 mt-3">
        <div class="card">
          <div class="card-header bg-secondary text-center text-white">
            <h4>每人操作次數由多到少排列</h4>
          </div>
          <div class="card-body">
            <table class="table table-sm table-bordered table-hover table-striped">
              <thead class="table-primary">
                <tr>
                  <th class="text-center align-middle">班級</th>
                  <th class="text-center align-middle">座號</th>
                  <th class="text-center align-middle">姓名</th>
                  <th class="text-center align-middle">操作次數</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($record = $result->fetch(PDO::FETCH_ASSOC)) { ?>
                <tr>
                  <td class="text-center align-middle"><?php echo ($record['classTitle'] == $lastClassTitle ? '' : $record['classTitle']); ?></td>
                  <td class="text-center align-middle"><?php echo $record['seatNo']; ?></td>
                  <td class="text-center align-middle"><?php echo $record['studentName']; ?></td>
                  <td class="text-center align-middle">
                    <a href="studentOperateRecordList.php?studentId=<?php echo $record['studentId']; ?>" class="text-decoration-none fw-bold">
                      <?php echo $record['times']; ?>
                    </a>
                  </td>
                </tr>
                <?php
                  $lastClassTitle = $field['classTitle'];
                  };
                ?>
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