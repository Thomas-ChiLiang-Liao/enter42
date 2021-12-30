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
    . "  UnionQuota.quotaA AS quota "
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
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <title>預選系統</title>
  <link rel="icon" href="../images/NA156516864930117.gif" type="image/x-icon">
  <script>
    $(document).ready(function(){
      $(".form-select").change(function(){
        $("#thisForm").submit();
      });
    });
  </script>
</head>

<body>
  <?php menu('quota'); ?>
  <div class="container-fluid">
    <div class="row mt-0">
      <div class="col-12 text-center">
        <form action="index.php" method="post" id="thisForm">
          <div class="row">
            <div class="col">
              <div class="input-group mt-1">
                <select name="schName" id="schName" class="form-select mt-1">
                  <option value="00">請選擇學校</option>
                  <?php foreach ( $pdo->query('SELECT * FROM TVERESchool WHERE 1 ORDER BY isPublic DESC, id ASC;') AS $school ) echo  "<option value=\"$school[title]\"".( $_POST['schName'] == $school['title'] ? "selected = \"selected\"" : '').">$school[title]</option>"; ?>
                </select>
                <!--
                <span class="input-group-text">學校</span>  
                <input type="text" class="form-control form-control" placeholder="請輸入學校名稱" list="schools" name="schName" id="schName" value="<?php echo $_POST['schName']; ?>">
                <datalist id="schools">
                  <?php //foreach ( $pdo->query('SELECT * FROM TVERESchool WHERE 1 ORDER BY is_public DESC, id ASC;') AS $school ) echo  "<option value=\"$school[title]\"></option>"; ?>
                </datalist>
                -->
              </div>
            </div>
            <div class="col">
              <div class="input-group mt-1">
                <select name="examSort" id="examSort" class="form-select mt-1">
                  <option value="00">請選擇校系類別</option>
                  <?php foreach ($pdo->query('SELECT * FROM TVETExamSort WHERE id <= 21;') AS $examSorts ) echo "<option value=\"$examSorts[id]$examSorts[sort]\"".( $_POST['examSort'] == $examSorts['id'].$examSorts['sort'] ? "selected = \"selected\"" : '').">$examSorts[id]$examSorts[sort]</option>"; ?>
                </select>
                <!--
                <span class="input-group-text">招生類別</span>
                <input type="text" class="form-control form-control" placeholder="請輸入招生類別代碼" list="examSorts" name="examSort" id="examSort" value="<?php echo $_POST['examSort']; ?>">
                <datalist id="examSorts">
                  <?php //foreach ($pdo->query('SELECT * FROM TVETExamSort WHERE id <= 21;') AS $examSorts ) echo "<option value=\"$examSorts[id]$examSorts[sort]\"></option>"; ?>
                </datalist>
                -->
              </div>
            </div>
          </div>

        </form>
      </div>
    </div>
    <div class="row mt-1">
      <!-- 甄選入學資料 -->
      <div class="col-6">
        <?php
        if ( !isset($_POST['schName']) OR $_POST['schName'] == '00'  ) {
          // 空白網頁
        } else {
        ?>
        <div class="card">
          <div class="card-header bg-primary text-center text-white">
            <?php echo "$_POST[schName]".($_POST['examSort'] == '00' ? '' : "【$_POST[examSort]】"); ?>甄選入學名額
          </div>
          <div class="card-body">
            <table class="table table-bordered table-hover table-sm">
              <thead>
                <tr class="table-primary">
                  <th class="text-center align-middle">系科名稱</th>
                  <th class="text-center align-middle">招生類別</th>
                  <th class="text-center align-middle">一般生名額</th>
                </tr>
              </thead>
              <tbody>
              <?php while ($result = $statementLeft->fetch(PDO::FETCH_ASSOC)) { ?>
                <tr>
                  <td class="text-center align-middle"><?php echo $result['depName']; ?></td>
                  <td class="text-center align-middle"><?php echo $result['examSort']; ?></td>
                  <td class="text-center align-middle"><?php echo $result['quota']; ?></td>
                </tr>
              <?php } ?>
              </tbody>
            </table>            
          </div>
        </div>
        <?php } ?>
      </div>

      <!-- 登記分發資料 -->
      <div class="col-6">
        <?php
        if ( !isset($_POST['schName']) OR $_POST['schName'] == '00' ) {
          //空白網頁
        } else {
        ?>
        <div class="card">
          <div class="card-header bg-success text-center text-white">
            <?php echo "$_POST[schName]".($_POST['examSort'] == '00' ? '' : "【$_POST[examSort]】"); ?>登記分發名額
          </div>
          <div class="card-body">
            <table class="table table-bordered table-hover table-sm">
              <thead>
                <tr class="table-success">
                  <th class="text-center align-middle">系科名稱</th>
                  <th class="text-center align-middle">招生類別</th>
                  <th class="text-center align-middle">一般生名額</th>
                </tr>
              </thead>
              <tbody>
              <?php while ($result = $statementRight->fetch(PDO::FETCH_ASSOC)) { ?>
                <tr>
                  <td class="text-center align-middle"><?php echo $result['depName']; ?></td>
                  <td class="text-center align-middle"><?php echo $result['examSort']; ?></td>
                  <td class="text-center align-middle"><?php echo $result['quota']; ?></td>
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