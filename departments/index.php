<!DOCTYPE html>

<?php 
session_start();
include '../menu.php';
include '../config.ini.php';
$errorPage = "Location: https://$_SERVER[SERVER_NAME]".dirname($_SERVER['SCRIPT_NAME']).'/../error.php?msg';
$pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8",$guestId,$guestPw);
$statement = $pdo->query('SELECT id, sort FROM TVETExamSort WHERE id <= 21;');
$errMessage = $pdo->errorInfo();
if ($errMessage[0] != '00000') {
  header("$errorPage=danger:讀取 TVETExamSort 發生錯誤！代碼：$errMessage[0]/$errMessage[1]<br>訊息：$errMessage[2]");
  exit();
}
?>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/js/bootstrap.bundle.min.js"></script>
  <title>預選系統</title>
  <link rel="icon" href="../images/<?php echo ( $_SERVER["SERVER_NAME"] == "yy33.us" ? "website-design.png" : "logo.icon.png"); ?>" type="image/x-icon">
  <link rel="stylesheet" href="../styles.css">
</head>
<body>
  <?php menu('departments'); ?>
  <div class="container-fluid">
    <div class="row mt-0">
      <div class="col-12 offset-md-4 col-md-4 text-center">
        <form action="index.php" method="post">
          <select class="form-select form-select-sm mt-1" name="selector" onchange="this.parentNode.submit()">
            <option value='00'>請選擇招生群(類)別</option>
            <?php while ( $row = $statement->fetch(PDO::FETCH_ASSOC) ) 
            echo "<option".($_POST['selector'] == $row['id'] ? ' selected' : '')." value='$row[id]'>$row[id]$row[sort]</option>"; ?>
          </select>
        </form>
      </div>
    </div>
    <?php 
    if ( isset( $_POST['selector'] ) && ($_POST['selector'] <> '00') ) { 
      $sql = "SELECT "
        . "  TVEREDepartment.id AS depid, "
        . "  CONCAT(TVERESchool.title, TVEREDepartment.title) AS title, "
        . "  TVERESchool.maxTargets AS maxTargets, "
        . "  TVEREDepartment.quotaA AS quotaA, TVEREDepartment.stage2QuotaA AS stage2QuotaA, "
        . "  TVEREDepartment.examDate AS examDate "
        . "FROM TVEREDepartment "
        . "LEFT JOIN TVERESchool ON TVEREDepartment.schid = TVERESchool.id "
        . "WHERE TVEREDepartment.examSort = :examSort AND TVEREDepartment.quotaA <> 0 "
        . "ORDER BY TVERESchool.isPublic DESC, TVEREDepartment.id ASC;";
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':examSort', $_POST['selector'], PDO::PARAM_STR, 2);
      $statement->execute();
      $errMessage = $statement->errorInfo();
      if ($errMessage[0] != '00000') {
        header("$errorPage=danger:讀取 TVETExamSort 發生錯誤(行號: 59)！代碼：$errMessage[0]/$errMessage[1]<br>訊息：$errMessage[2]");
        exit();
      }
    ?>
    <div class="row mt-1">
      <div class="col-12">
        <table class="table table-sm table-bordered table-hover">
          <thead class="table-primary">
            <tr>
              <th class="text-center align-middle">校系</th>
              <th class="text-center align-middle">可報名校系科組數</th>
              <th class="text-center align-middle">一般生名額</th>
              <th class="text-center align-middle">第二階段名額</th>
              <th class="text-center align-middle">甄試日期</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $rows = 0;
            while ( $record = $statement->fetch(PDO::FETCH_ASSOC) ) {
              $examDate = strtotime($record['examDate']);
              switch ( date('w',$examDate) ) {
                case 0: $weekDay = '日'; break;
                case 1: $weekDay = '一'; break;
                case 2: $weekDay = '二'; break;
                case 3: $weekDay = '三'; break;
                case 4: $weekDay = '四'; break;
                case 5: $weekDay = '五'; break;
                case 6: $weekDay = '六'; 
              }

              if ($rows++ > 18) { $rows = 0; ?>
            <tr class="table-primary">
              <th class="text-center align-middle">校系</th>
              <th class="text-center align-middle">可報名校系科組數</th>
              <th class="text-center align-middle">一般生名額</th>
              <th class="text-center align-middle">第二階段名額</th>
              <th class="text-center align-middle">甄試日期</th>
            </tr>
            <?php } ?>
            <tr>
              <td class="align-middle bg-light">
                <a href="https:departmentDetails.php?depid=<?php echo $record['depid']; ?>" class="text-primary fw-bold text-decoration-none" title="按下可查詢此系條件" target="_blank">
                  <?php echo "<strong>$record[depid]</strong>$record[title]"; ?>
                </a>
              </td>
              <td class="text-center align-middle bg-light"><?php echo $record['maxTargets']; ?></td>
              <td class="text-center align-middle bg-light"><?php echo "$record[quotaA]"; ?></td>
              <td class="text-center align-middle bg-light"><?php echo "$record[stage2QuotaA]"; ?></td>
              <td class="text-center align-middle bg-light"><?php echo ( $record['examDate'] == null ? '--' : date('Y-m-d', $examDate) . ' (' . $weekDay . ')' ); ?></td>
            </tr>
            <? } ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php } ?>
  </div>
</body>
</html>