<!DOCTYPE html>
<?php
/*
 $_SESSION[projectRoot] => https://photo.taivs.tp.edu.tw/enter42/admin 
 $_SESSION[name] 
 $_SESSION[optype] 
 $_SESSION[secondsBrowserTimezoneOffset]
 $_SESSION[secondsServerTimezoneOffset]
*/
if ( !isset( $_SERVER['HTTPS'] ) OR ( $_SERVER['HTTPS'] != 'on' ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
  else {
    session_start();
    if ( !isset( $_SESSION['name'] ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
    else { 
      include '../menu.php';
      include '../../config.ini.php';
      // 資料庫連線，刪除人員介面選單用
		  $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $opId, $opPw);
		  $statement = $pdo->query('SELECT id, name FROM operator WHERE 1;');
		  $statement->execute();
  ?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <title>預選系統-管理</title>
  <link rel="icon" href="../../images/logo.icon.png" type="image/x-icon">
  <link rel="stylesheet" href="../../styles.css">
  <script src="../../autoLogout.js"></script>
  <script src="../../sha1.js"></script>
  <script src="../../pw.js"></script>
  <script src="scripts.js"></script>
</head>
<body>
  <?php menu('operatorMaintain'); ?>
  <div class="container-fluid">
    <div class="row mt-5">
      <div class="col-sm-6">
        <!-- 新增人員介面 -->
        <div class="row">
          <div class="col-12 col-md-8 offset-md-2">
            <div class="card">
              <div class="card-header bg-primary">
                <h3 class="text-white">新增人員作業</h3>
              </div>
              <div class="card-body">
                <form action="insertIntoTable.php" id="addOperatorForm" method="post">
                  <input type="text" class="form-control" id="operatorName" name="operatorName" placeholder="操作人員姓名">
                  <input type="password" class="form-control mt-2" id="operatorPassword" name="operatorPassword" placeholder="請輸入密碼" onkeyup="checkPasswordMatch(this.id,'confirmPassword')">
                  <input type="password" class="form-control mt-2" id="confirmPassword" name="confirmPassword" placeholder="請再次輸入密碼" onkeyup="checkPasswordMatch('operatorPassword', this.id)">
                  <div class="mt-2">
                    <div class="form-check form-check-inline">
                      <input type="radio" class="form-check-input" id="radio1" name="operatorSex" value="1">
                      <label for="radio1" class="form-check-label">男</label>
                    </div>
                    <div class="form-check form-check-inline ms-3">
                      <input type="radio" class="form-check-input" id="radio2" name="operatorSex" value="2">
                      <label for="radio2" class="form-check-label">女</label>
                    </div>
                  </div>
                  <div class="mt-2">
                    <div class="form-check form-check-inline">
                      <input type="radio" class="form-check-input" id="radio3" name="operatorType" value="1">
                      <label for="radio3" class="form-check-label">組長</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input type="radio" class="form-check-input" id="radio4" name="operatorType" value="2">
                      <label for="radio4" class="form-check-label">組員</label>
                    </div>
                  </div>
                  <button class="btn btn-primary mt-2" id="addOperatorButton" type="button" onclick="checkFormA()">新增</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6">
        <!-- 刪除人員介啊 -->
        <div class="row">
          <div class="col-12 col-md-8 offset-md-2">
            <div class="card">
              <div class="card-header bg-warning">
                <h3 class="text-white">刪除人員作業</h3>
              </div>
              <div class="card-body">
                <form action="deleteFromTable.php" id="deleteOperatorForm" method="post">
                  <select name="operatorId" id="operatorSelector" class="form-select" onchange="checkSelected()">
                    <option value="0">請選擇……</option>
                    <?php
                    while ($record = $statement->fetch(PDO::FETCH_ASSOC)) echo "<option value=\"$record[id]\">$record[name]</option>";
                    ?>
                  </select>
                  <button id="deleteOperatorButton" class="btn btn-warning mt-2" type="submit">刪除</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
<script>
  document.getElementById("addOperatorButton").disabled = true;
  document.getElementById("operatorPassword").style.background = "#FF8080";
  document.getElementById("confirmPassword").style.background = "#FF8080";
  document.getElementById("deleteOperatorButton").disabled = true;
</script>
</html>
<?php } } ?>