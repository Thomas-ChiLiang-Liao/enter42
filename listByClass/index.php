<!DOCTYPE html>
<?php
  if ( !isset( $_SERVER['HTTP_X_HTTPS'] ) OR ( $_SERVER['HTTP_X_HTTPS'] != 'on' ) ) header( "Location: https://$_SERVER[SERVER_NAME]$_SERVER[REQUEST_URI]" );
  else {
    session_start();
    include '../menu.php';
    include '../config.ini.php';
    
    foreach ($_POST as $i => $data) {
      $data 			= str_replace('"','',$data);
      $_POST[$i] 	= str_replace("'",'',$data);
    }    

    // 資料庫連線
    $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $guestId, $guestPw);

    // 取出所有的班級資料
    $sql = "SELECT * FROM class WHERE 1;";
    $statement = $pdo->query($sql);

?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <title>預選系統</title>
  <link rel="icon" href="../images/logo.icon.jpg" type="image/x-icon">
  <link rel="stylesheet" href="../styles.css">
</head>
<body>
	<?php menu('listByClass'); ?>
  <div class="container-fluid">
    <div class="row mt-1">
      <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
        <form action="index.php" method="post">
            <select class="form-select form-select-sm mt-1" name="selector" onchange="this.parentNode.submit()">
              <option value='000'>請選擇班級</option>
              <?php while ( $row = $statement->fetch(PDO::FETCH_ASSOC) ) 
              echo "<option".( substr($_POST['selector'],0,3) == $row['id'] ? ' selected' : '')." value='$row[id]$row[title]'>$row[title]</option>"; ?>
            </select>
        </form>
      </div>
    </div>
    <?php
    if (isset($_POST['selector']) && $_POST['selector'] != '000') {
    $sql = 'SELECT'
         . '  student.id AS studentId,'
         . '  RIGHT(student.id, 2) AS seatNo,'
         . '  CONCAT(student.examSort, TVETExamSort.sort) AS examSort,'
         . '  COUNT(CASE WHEN RIGHT(TVERETarget.id, 6) IS NOT NULL THEN 1 END) AS targets'
         . ' FROM student'
         . ' LEFT JOIN TVERETarget ON student.id = LEFT(TVERETarget.id, 6)'
         . ' LEFT JOIN TVETExamSort ON student.examSort = TVETExamSort.id'
         . ' WHERE LEFT(student.id, 3) = :classId'
         . ' GROUP BY student.id'
         . ' ORDER BY student.id;';
    $statement = $pdo->prepare($sql);
    $classId = substr($_POST['selector'],0,3);
    $statement->bindParam(':classId', $classId, PDO::PARAM_STR, 3);
    if (!$statement->execute()) {
      $errorInfo = $statement->errorInfo();
      echo "讀取資料發錯錯誤，代碼：$errorInfo[0]/$errorInfo[1]<br>訊息：$errorInfo[2]";
      exit();
    }
    ?>
    <div class="row mt-2">
      <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xxl-4 offset-xxl-4">
        <div class="card">
          <div class="card-header bg-primary text-white text-center"><?php echo substr($_POST['selector'],3).'預選統計'; ?></div>
          <div class="card-body">
            <table class="table table-sm table-bordered table-hover">
              <thead>
                <tr class="bg-secondary text-white">
                  <th class="text-center">座號</th>
                  <th class="text-center">考試類別</th>
                  <th class="text-center">預選志願數</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($field = $statement->fetch(PDO::FETCH_ASSOC)) { ?>
                <tr<?php echo ( $field['targets'] ==  0 ? ' class="text-danger"' : ''); ?>>
                  <td class="text-center align-middle"><?php echo $field['seatNo']; ?></td>
                  <td class="text-center align-middle"><?php echo $field['examSort']; ?></td>
                  <td class="text-center">
                    <?php if ($field['targets'] != 0) { ?>
                    <a href="targetsListByStudent.php?stuid=<?php echo $field['studentId']; ?>" class="btn btn-info py-0" target="_blank"><?php echo $field['targets']; ?></a>
                    <?php } else echo $field['targets']; ?>
                  </td>    
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <?php } ?>
  </div>
</body>
</html>
<?php } ?>