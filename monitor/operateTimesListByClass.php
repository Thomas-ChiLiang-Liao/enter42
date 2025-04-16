<!DOCTYPE html>
<?php
  session_start();
  if ( !isset( $_SERVER['HTTP_X_HTTPS'] ) OR ( $_SERVER['HTTP_X_HTTPS'] != 'on' ) ) header( "Location: $_SESSION[serverRoot]" );
  else {
    include 'menu.php';
    // 資料庫連線
    $pdo = new PDO("mysql:host=$_SESSION[server]:3307;dbname=enter42;charset=utf8mb4", $_SESSION['account'], $_SESSION['password']);    
 
    // 查詢班級名稱
    $sql = 'SELECT class.id AS classId, class.title AS classTitle FROM class WHERE 1;';
    $result = $pdo->query($sql);
 ?>
<html lang="en">
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
  <?php menu('operateTimesListByClass'); ?>
    <div class="row">
      <div class="col-12 col-lg-8 offset-lg-2 col-xxl-6 offset-xxl-3 mt-3">
      <?php if (!isset($_GET['classIdTitle'])) { ?>
        <!-- 不存在 $_GET['classId'] 變數，顯示班級連結 -->
        <?php while ($record = $result->fetch(PDO::FETCH_ASSOC)) { ?>
          <a href="operateTimesListByClass.php?classIdTitle=<?php echo $record['classId'].$record['classTitle']; ?>" class="btn btn-primary mb-5 me-3">
            <?php echo $record['classTitle']; ?>
          </a>
        <?php } ?>
      <?php } else { 
        $sql = 'SELECT'
          . ' student.id AS studentId,'
          . ' RIGHT(student.id, 2) AS seatNo,'
          . ' student.name AS studentName,'
          . ' COUNT(CASE WHEN TVEREOperateRecord.studentId IS NOT NULL THEN 1 END) AS times'
          . ' FROM student'
          . ' LEFT JOIN TVEREOperateRecord ON student.id = TVEREOperateRecord.studentId'
          . ' WHERE LEFT(student.id, 3) = "'.substr($_GET['classIdTitle'],0,3).'"'
          . ' GROUP BY student.id'
          . ' ORDER BY student.id;';
        $result = $pdo->query($sql);
      ?>
        <div class="card">
          <div class="card-header bg-secondary text-center text-white">
            <h4><?php echo substr($_GET['classIdTitle'],3); ?>&nbsp;學生預選次數列表</h4>
          </div>
          <div class="card-body">
            <table class="table table-sm table-bordered table-hover table-striped">
             <thead class="table-primary">
               <tr>
                 <th class="text-center align-middle">座號</th>
                 <th class="text-center align-middle">姓名</th>
                 <th class="text-center align-middle">操作次數</th>
               </tr>
             </thead>
             <tbody>
               <?php while ($record = $result->fetch(PDO::FETCH_ASSOC)) { ?>
               <tr>
                 <td class="text-center align-middle <?php echo ( $record['times'] == 0 ? 'text-danger' : ''); ?>"><?php echo $record['seatNo']; ?></td>
                 <td class="text-center align-middle <?php echo ( $record['times'] == 0 ? 'text-danger' : ''); ?>"><?php echo $record['studentName']; ?></td>
                 <td class="text-center align-middle <?php echo ( $record['times'] == 0 ? 'text-danger' : ''); ?>">
                  <?php if ($record['times'] == 0) { echo $record['times']; } else { ?>
                  <a href="studentOperateRecordList.php?studentId=<?php echo $record['studentId']; ?>" class="text-primary text-decoration-none fw-bold">
                    <?php echo $record['times']; ?>
                  </a>
                  <?php } ?>
                 </td>
               </tr>
               <?php } ?>
             </tbody>
            </table>
          </div>
        </div>
      <?php } ?>
      </div>
    </div>
	</div>
</body> 
</html>
<?php } ?>