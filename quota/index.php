<!DOCTYPE html>

<?php 
session_start();
include '../menu.php';
include '../config.ini.php';
$pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8",$guestId,$guestPw);
if ( isset($_POST['schName']) ) {
  $_POST['schName'] = str_replace('"','',$_POST['schName']);
  $_POST['schName'] = str_replace("'",'',$_POST['schName']);
  $_POST['examSort'] = str_replace('"','',$_POST['examSort']);
  $_POST['examSort'] = str_replace("'",'',$_POST['examSort']);
  $examSort = substr($_POST['examSort'],0,2);
  $sql = "SELECT "
    . "  TVEREDepartment.id AS depid, "
    . "  TVERESchool.title AS schName, "
    . "  TVEREDepartment.title AS depName, "
    . "  CONCAT(TVETExamSort.id, TVETExamSort.sort) AS examSort, "
    . "  TVEREDepartment.quotaA AS quota "
    . "FROM TVEREDepartment "
    . "LEFT JOIN TVERESchool ON TVEREDepartment.schid = TVERESchool.id "
    . "LEFT JOIN TVETExamSort ON TVEREDepartment.examSort = TVETExamSort.id "
    . "WHERE TVERESchool.title = :schName AND TVEREDepartment.quotaA <> 0 " . ( $_POST['examSort'] <> '00' ? "AND TVETExamSort.id = :examSort " : ' ' )
    . "ORDER BY TVEREDepartment.title, TVETExamSort.id;";
  $statementLeft = $pdo->prepare($sql);
  $statementLeft->bindParam(':schName', $_POST['schName'], PDO::PARAM_STR, 25);
  if ( $_POST['examSort'] <> '00' ) $statementLeft->bindParam(':examSort', $examSort, PDO::PARAM_STR, 2);
  $statementLeft->execute();
  
  $sql = "SELECT "
    . "  UnionQuota.schName AS schName, "
    . "  UnionQuota.depTitle AS depName, "
    . "  CONCAT(TVETExamSort.id, TVETExamSort.sort) AS examSort, "
    . "  UnionQuota.quotaA AS quota, "
    . "  UnionQuota.chinese AS chinese, "
    . "  UnionQuota.english AS english, "
    . "  UnionQuota.math AS math, "
    . "  UnionQuota.pro1 AS pro1, "
    . "  UnionQuota.pro2 AS pro2 "
    . "FROM UnionQuota "
    . "LEFT JOIN TVETExamSort ON LEFT(UnionQuota.id, 2) = TVETExamSort.id "
    . "WHERE UnionQuota.schName = :schName " . ( $_POST['examSort'] <> '00' ? "AND TVETExamSort.id = :examSort " : ' ' )
    . "ORDER BY UnionQuota.depTitle, TVETExamSort.id;";
  $statementRight = $pdo->prepare($sql);
  $statementRight->bindParam(':schName', $_POST['schName'], PDO::PARAM_STR, 25);
  if ( $_POST['examSort'] <> '00' ) $statementRight->bindParam(':examSort', $examSort, PDO::PARAM_STR, 2);
  $statementRight->execute();
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
  <link rel="icon" href="../images/logo.icon.png" type="image/x-icon">
  <link rel="stylesheet" href="../styles.css">
</head>

<body>
  <?php menu('quota'); ?>
  <div class="container-fluid">
    <div class="row mt-0">
      <div class="col-12 text-center">
        <form action="index.php" method="post" id="thisForm">
          <div class="row">
            <div class="col-5">
              <div class="input-group mt-1">
                <select name="schName" id="schName" class="form-select mt-1" onchange="document.getElementById('thisForm').submit()">
                  <option value="00">請選擇學校</option>
                  <?php foreach ( $pdo->query('SELECT * FROM TVERESchool WHERE 1 ORDER BY isPublic DESC, id ASC;') AS $school ) echo  "<option value=\"$school[title]\"".( $_POST['schName'] == $school['title'] ? "selected = \"selected\"" : '').">$school[title]</option>"; ?>
                </select>
              </div>
            </div>
            <div class="col-7">
              <div class="input-group mt-1">
                <select name="examSort" id="examSort" class="form-select mt-1" onchange="document.getElementById('thisForm').submit()">
                  <option value="00">請選擇校系類別</option>
                  <?php foreach ($pdo->query('SELECT * FROM TVETExamSort WHERE id <= 21;') AS $examSorts ) echo "<option value=\"$examSorts[id]$examSorts[sort]\"".( $_POST['examSort'] == $examSorts['id'].$examSorts['sort'] ? "selected = \"selected\"" : '').">$examSorts[id]$examSorts[sort]</option>"; ?>
                </select>
              </div>
            </div>
          </div>

        </form>
      </div>
    </div>
    <div class="row mt-1">
      <!-- 甄選入學資料 -->
      <div class="col-5">
        <?php
        if ( !isset($_POST['schName']) OR $_POST['schName'] == '00'  ) {
          // 空白網頁
        } else {
        ?>
        <div class="card">
          <div class="card-header bg-primary text-center text-white">
            <?php echo "$_POST[schName]".($_POST['examSort'] == '00' ? '【各系科】' : "【$_POST[examSort]】"); ?><span class="fw-bolder text-warning">甄選入學</span>名額
          </div>
          <div class="card-body">
            <table class="table table-bordered table-hover table-sm">
              <thead>
                <tr class="table-primary">
                  <th class="align-middle">校系代碼及名稱</th>
                  <?php if ( $_POST['examSort'] == '00' ) { ?>
                  <th class="text-center align-middle">招生類別</th>
                  <?php } ?>
                  <th class="text-center align-middle">一般生名額</th>
                </tr>
              </thead>
              <tbody>
              <?php $totalQuota = 0; while ($result = $statementLeft->fetch(PDO::FETCH_ASSOC)) { $totalQuota += $result['quota']; ?>
                <tr>
                  <td class="align-middle">
                    <a href="<?php echo $_SESSION['projectRoot']; ?>/departments/departmentDetails.php?depid=<?php echo $result['depid']; ?>" class="text-primary text-decoration-none fw-bold" title="按下可查詢此系條件" target="_blank">
                      <?php echo "$result[depid]$result[schName]$result[depName]"; ?>
                    </a>
                  </td>
                  <?php if ( $_POST['examSort'] == '00' ) { ?>
                    <td class="align-middle"><?php echo $result['examSort']; ?></td>
                  <?php } ?>
                  <td class="text-center align-middle text-primary fw-bold"><?php echo $result['quota']; ?></td>
                </tr>
              <?php } ?>
                <tr>
                  <td class="text-center fs-4 fw-bold" colspan="<?php echo ( $_POST['examSort'] == '00' ? 2 : 1 ); ?>">合&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;計</td>
                  <td class="text-center fs-4 fw-bold text-primary"><?php echo $totalQuota; ?></td>
                </tr>
              </tbody>
            </table>            
          </div>
        </div>
        <?php } ?>
      </div>

      <!-- 登記分發資料 -->
      <div class="col-7">
        <?php
        if ( !isset($_POST['schName']) OR $_POST['schName'] == '00' ) {
          //空白網頁
        } else {
        ?>
        <div class="card">
          <div class="card-header bg-success text-center text-white">
            <?php echo "$_POST[schName]".($_POST['examSort'] == '00' ? '【各系科】' : "【$_POST[examSort]】"); ?><span class="fw-bolder text-warning">登記分發</span>名額
          </div>
          <div class="card-body">
            <table class="table table-bordered table-hover table-sm">
              <thead>
                <tr class="table-success">
                  <th class="align-middle">系科名稱</th>
                  <?php if ( $_POST['examSort'] == '00' ) { ?>
                  <th class="text-center align-middle">招生類別</th>
                  <?php } ?>
                  <th class="text-center align-middle">一般生名額</th>
                  <th class="text-center align-middle">國文加權</th>
                  <th class="text-center align-middle">英文加權</th>
                  <th class="text-center align-middle">數學加權</th>
                  <th class="text-center align-middle">專一加權</th>
                  <th class="text-center align-middle">專二加權</th>                  
                </tr>
              </thead>
              <tbody>
              <?php $totalQuota = 0; while ($result = $statementRight->fetch(PDO::FETCH_ASSOC)) { $totalQuota += $result['quota'];?>
                <tr>
                  <td class="align-middle"><?php echo $result['depName']; ?></td>
                  <?php if ( $_POST['examSort'] == '00' ) { ?>
                  <td class="align-middle"><?php echo $result['examSort']; ?></td>
                  <?php } ?>
                  <td class="text-center align-middle text-success fw-bold"><?php echo $result['quota']; ?></td>
                  <td class="text-center align-middle"><?php echo $result['chinese']; ?></td>
                  <td class="text-center align-middle"><?php echo $result['english']; ?></td>
                  <td class="text-center align-middle"><?php echo $result['math']; ?></td>
                  <td class="text-center align-middle"><?php echo $result['pro1']; ?></td>
                  <td class="text-center align-middle"><?php echo $result['pro2']; ?></td>                  
                </tr>            
              <?php } ?>
                <tr>
                  <td class="text-center fs-4 fw-bold" colspan="<?php echo ( $_POST['examSort'] == '00' ? 2 : 1 ); ?>">合&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;計</td>
                  <td class="text-center fs-4 fw-bold text-success"><?php echo $totalQuota; ?></td>
                  <td class="text-center fs-4 fw-bold" colspan="5">--</td>
                </tr>
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