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

      foreach ($_POST as $i => $data) {
        $data = str_replace('"','',$data);
        $data = str_replace("'","",$data);
        $_POST[$i] = $data;
      }		
      
      // 資料庫連線
      $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $opId, $opPw);
      
      // 讀取學生人數及班級數
      $studentTableQuery = $pdo->query("SELECT * FROM student WHERE 1;");
      $studentTableQuery->execute();
      $numOfStudents = $studentTableQuery->rowCount();
  
      $classTableQuery = $pdo->prepare("SELECT * FROM class WHERE 1;");
      $classTableQuery->execute();
      $numOfClasses = $classTableQuery->rowCount();

      if (isset($_POST['classSelector'])) {
        $sql = 'SELECT RIGHT(student.id,2) AS seatNo,'
            . ' student.name AS studentName,'
            . ' class.title AS classTitle,'
            . ' CONCAT(student.examSort, TVETExamSort.sort) AS examSort,'
            . ' TVETExamSort.admissionIds AS admissionIds,'
            . ' possibilityDepartments.id AS flag, '
            . ' student.scoreG AS scoreG '
            . ' FROM student'
            . ' LEFT JOIN class ON class.id = :classId'
            . ' LEFT JOIN TVETExamSort ON student.examSort = TVETExamSort.id'
            . ' LEFT JOIN possibilityDepartments ON student.id = possibilityDepartments.id'
            . ' WHERE LEFT(student.id, 3) = :classId'
            . ' ORDER BY student.id;';
        $students = $pdo->prepare($sql);
        $students->bindParam(':classId', $_POST['classSelector'], PDO::PARAM_STR, 3);
        $students->execute();
        $errorInfo = $students->errorInfo();
        if ($errorInfo[0] != '00000') {
          $_SESSION['msg'] = "danger:錯誤發生，代碼：$errorInfo[0]<br>錯誤碼：$errorInfo[1]，訊息：$errorInfo[2]";
          header("Location: $_SESSION[projectRoot]/main/");
        }
      }
  ?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <title>預選系統-管理</title>
  <link rel="icon" href="../../images/<?php echo ( $_SERVER["SERVER_NAME"] == "yy33.us" ? "website-design.png" : "logo.icon.png"); ?>" type="image/x-icon">
  <link rel="stylesheet" href="../../styles.css">
  <script src="../../autoLogout.js"></script>
</head>
<body>
  <?php menu('viewStudentData'); ?>
  <div class="container-fluid">
    <?php if ($numOfStudents == 0 || $numOfClasses == 0) { ?> 
    <div class="alert alert-warning text-center mt-5">
      <?php if ($numOfStudents == 0 && $numOfClasses == 0) { ?> 
        <strong>請注意：</strong>學生資料及班級資料均未上傳，請先上傳資料！
      <?php } else if ($numOfStudents == 0 ) {?>
        <strong>請注意：</strong>學生資料未上傳，請先上傳再進行操作。
      <?php } else { ?>
        <strong>請注意：</strong>班級資料未上傳，請先上傳再進行操作。
      <?php } ?>
    </div>
    <?php } else { ?>
    <div class="row">
      <div class="col-12 col-md-10 offset-md-1">
        <form action="index.php" method="post">
          <select name="classSelector" id="classSelector" class="form-select mt-2" onchange=this.parentNode.submit()>
            <option value="0">請選擇班級…</option>
            <?php while ( $record = $classTableQuery->fetch(PDO::FETCH_ASSOC) ) echo "<option value=\"$record[id]\" " . ($_POST['classSelector'] == $record['id'] ? 'selected' : '' ) . ">$record[title]</option>"; ?>
          </select>
        </form>
        <?php if (isset($_POST['classSelector'])) { ?>
					<table class="table table-hover table-bordered mt-3 table-sm">
            <thead>
							<tr class="bg-secondary text-white">
								<th class="text-center align-middle bg-secondary text-white">重設密碼</th>
								<th class="text-center align-middle bg-secondary text-white">座號</th>
								<th class="text-center align-middle bg-secondary text-white">姓名</th>
								<th class="text-center align-middle bg-secondary text-white">考試類別</th>
								<th class="text-center align-middle bg-secondary text-white">國文<br>(含作文)</th>
								<th class="text-center align-middle bg-secondary text-white">英文</th>
								<th class="text-center align-middle bg-secondary text-white">數學</th>
								<th class="text-center align-middle bg-secondary text-white">專一</th>
								<th class="text-center align-middle bg-secondary text-white">專二</th>
                <th class="text-center align-middle bg-secondary text-white">檢視落點分析</th>
							</tr>
						</thead>
            <tbody>
            <?php $lineCounter = 0; while ( $field = $students->fetch(PDO::FETCH_ASSOC) ) {
              if ( $lastSeatNo == $field['seatNo'] ) continue;
              if ( $field['flag'] == null ) $disable = true; else $disable = false;
              if ( $lineCounter % 15 == 0 && $lineCounter <> 0 ) { ?>
            	<tr class="bg-secondary text-white">
								<th class="text-center align-middle bg-secondary text-white">重設密碼</th>
								<th class="text-center align-middle bg-secondary text-white">座號</th>
								<th class="text-center align-middle bg-secondary text-white">姓名</th>
								<th class="text-center align-middle bg-secondary text-white">考試類別</th>
								<th class="text-center align-middle bg-secondary text-white">國文<br>(含作文)</th>
								<th class="text-center align-middle bg-secondary text-white">英文</th>
								<th class="text-center align-middle bg-secondary text-white">數學</th>
								<th class="text-center align-middle bg-secondary text-white">專一</th>
								<th class="text-center align-middle bg-secondary text-white">專二</th>
                <th class="text-center align-middle bg-secondary text-white">檢視落點分析</th>
							</tr>
            <?php } ?>
							<tr>
								<td class="text-center align-middle">
									<form action="resetPassword.php" method="post" onsubmit="return confirm('<?php echo "要將【$field[classTitle]&nbsp;$field[seatNo]號&nbsp;$field[studentName]】同學的密碼回復成原始設定？";?>')">
										<input type="hidden" name="studentId" value="<?php echo $_POST['classSelector'].'0'.$field['seatNo']; ?>">
										<input type="hidden" name="classTitle" value="<?php echo $field['classTitle']; ?>">
										<input type="hidden" name="seatNo" value="<?php echo $field['seatNo']; ?>">
										<input type="hidden" name="studentName" value="<?php echo $field['studentName']; ?>">
										<button class="btn btn-info py-1" type="submit">重設密碼</button>
									</form>
								</td>
								<td class="text-center align-middle"><?php echo $field['seatNo']; ?></td>
								<td class="text-center align-middle"><?php echo $field['studentName']; ?></td>
								<td class="text-center align-middle"><?php echo $field['examSort']; ?></td>
                <!-- 成績，判斷 scoreG 是否為 null，如果是就全部顯示為  -->
                <?php if ( $field['scoreG'] == null ) { ?>
                <td class="text-center align-middle">--</td>
                <td class="text-center align-middle">--</td>
                <td class="text-center align-middle">--</td>
                <td class="text-center align-middle">--</td>
                <td class="text-center align-middle">--</td>
                <?php } else { 
                  $scoreG = json_decode($field['scoreG'],true);
                ?>
                <td class="text-center align-middle"><?php echo $scoreG['chinese']; ?></td>
                <td class="text-center align-middle"><?php echo $scoreG['english']; ?></td>
                <td class="text-center align-middle"><?php echo $scoreG['math']; ?></td>
                <td class="text-center align-middle"><?php echo $scoreG['pro1']; ?></td>
                <td class="text-center align-middle">
                  <?php
                    if (count($scoreG['pro2']) == 1) echo $scoreG['pro2']['B'.substr($field['examSort'],0,2)];
                    else {
                      $echoString = "";
                      foreach ($scoreG['pro2'] AS $i => $data) {
                        if ($echoString != "") $echoString .= '<br>';
                        $echoString .= substr($i,-2) . ': ' . $data;
                      }
                      echo $echoString;
                    }
                  ?>
                </td>
                <td class="text-center align-middle">
									<form action="possibilityDepartments.php" method="post" target="_blank">
										<input type="hidden" name="studentId" value="<?php echo $_POST['classSelector'].'0'.$field['seatNo']; ?>">
										<input type="hidden" name="classTitle" value="<?php echo $field['classTitle']; ?>">
										<input type="hidden" name="seatNo" value="<?php echo $field['seatNo']; ?>">
										<input type="hidden" name="studentName" value="<?php echo $field['studentName']; ?>">
										<button class="btn btn-success py-1" type="submit" <?php echo ( $disable ? "disabled" : ""  ) ?>>落點分析</button>
									</form>
								</td>
                <?php $lastSeatNo = $field['seatNo']; } ?>
							</tr>
						<?php $lineCounter++; } ?>
						</tbody>
					</table>
				<?php } ?>
      </div>
    </div>
    <?php } ?>
  </div>
</body>
</html>
<?php } } ?>