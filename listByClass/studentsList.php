<!DOCTYPE html>
<?php
if ( !isset( $_SERVER['HTTPS'] ) OR ( $_SERVER['HTTPS'] != 'on' ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
else {
  session_start();
  include '../menu2.php';
  include '../config.ini.php';

  foreach ($_GET as $i => $data) {
    $data 			= str_replace('"','',$data);
    $_GET[$i] 	= str_replace("'","",$data);
  }

  $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $guestId, $guestPw);
  // 取得該校系的招生類別及專業二的索引
  $sql = "SELECT DISTINCT"
      ."  CONCAT(TVEREDepartment.id, TVERESchool.title) AS depIdTitle, "
      ."  TVEREDepartment.examSort AS examSort "
      ."FROM TVEREDepartment "
      ."LEFT JOIN TVERESchool ON TVEREDepartment.schid = TVERESchool.id "
      ."WHERE TVEREDepartment.id = '$_GET[targetId]';";
  $target = $pdo->prepare($sql);
  if (!$target->execute()) {
    echo "讀取校系資料發生錯誤！訊息：" . print_r ($target->errorInfo());
    exit();
  }
  if ($target->rowCount() != 1) {
    echo "$_GET[targetId]的校系資料不止一筆(有".$target->rowCount()."筆)，請檢查資料庫。";
    exit();
  } else $targetFields = $target->fetch(PDO::FETCH_ASSOC);
  // 取得選擇該校系的學生資料
  $sql = "SELECT "
      ."  class.title AS classTitle, "
      ."  RIGHT(student.id,2) AS seatNo, "
      ."  student.name AS stuName, "
      ."  student.examId AS examId, "
      ."  JSON_EXTRACT(student.scoreG, '$.chinese') AS chinese, "
      ."  JSON_EXTRACT(student.scoreG, '$.english') AS english, "        
      ."  JSON_EXTRACT(student.scoreG, '$.math') AS math, "
      ."  JSON_EXTRACT(student.scoreG, '$.pro1') AS pro1, "
      ."  JSON_EXTRACT(student.scoreG, '$.pro2.B". ( $targetFields['examSort'] == '21' ? '09' : $targetFields['examSort'] )."') AS pro2, "
      ."  (JSON_EXTRACT(student.scoreG, '$.chinese') + JSON_EXTRACT(student.scoreG, '$.english') + JSON_EXTRACT(student.scoreG, '$.math') + JSON_EXTRACT(student.scoreG, '$.pro1') + JSON_EXTRACT(student.scoreG, '$.pro2.B".( $targetFields['examSort'] == '21' ? '09' : $targetFields['examSort'])."')) AS total "
      ."FROM student "
      ."LEFT JOIN class ON class.id = LEFT(student.id,3) "
      ."LEFT JOIN TVERETarget ON student.id = LEFT(TVERETarget.id,6) "
      ."WHERE RIGHT(TVERETarget.id,6) = :depid "
      ."ORDER BY total DESC;";
  $statement = $pdo->prepare($sql);
  $statement->bindParam(':depid', $_GET['targetId'], PDO::PARAM_STR, 6);
  if (!$statement->execute()) {
    echo '讀取資料庫發生錯誤！訊息：' . print_r($statement->errorInfo());
    exit();
  } else {
    $json = array();
    while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
      $json[] = json_encode($result);
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
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <title><?php echo substr($targetFields['depIdTitle'],0,6); ?>預選學生列表</title>
  <link rel="icon" href="../images/<?php echo ( $_SERVER["SERVER_NAME"] == "yy33.us" ? "website-design.png" : "logo.icon.png"); ?>" type="image/x-icon">
  <link rel="stylesheet" href="../../styles.css">
  <script src="../../autoLogout.js"></script>
  <script>
    let depIdTitle = "<?php echo $targetFields['depIdTitle']; ?>";
    // 把從資料庫讀出來的資料以 json 格式傳給 JavaScript Object
    let jsonObject = <?php echo json_encode($json)."; "; ?>;
    // 把 JavaScript Object 的資料 - JSON型態-，轉換成 Array 型態，並一列一列放到 studentArray 中
    let studentArray = [];
    for(i=0; i<jsonObject.length; i++) studentArray[i] = JSON.parse(jsonObject[i]);
    let optionState = [[false,false,false,false,false,false],
                       [false,false,false,false,false,false],
                       [false,false,false,false,false,false],
                       [false,false,false,false,false,false]];

    let keyString = [
      { left:"", right:"" },
      { left:"", right:"" },
      { left:"", right:"" },
      { left:"", right:"" }
    ]; 
  </script>
  <script src="scripts.js"></script>
</head>
<body>
  <?php menu2(); ?>
  <div class="container-fluid">
    <div class="row">
      <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xxl-4 offset-xxl-4">
        <div class="card" id="sortOptionPanel" style="display: none;">
          <div class="card-header">
            <h4 class="bg-info text-center text-white" id="sortBy">同一比序有兩科以上者取其和為排序依據。</h4>
          </div>
          <div class="card-body">
            <p>第一比序</p>
            <div class="row">
              <div class="col-2">
                <div class="form-check p-0 text-center">
                  <input type="checkbox" class="form-check-input-sm key1" id="chineseKey1" onclick="setOptionState(this.checked,0,0)">
                  <label class="form-check-label" for="chineseKey1">國文</label>
                </div>                                                              
              </div>
              <div class="col-2">
                <div class="form-check p-0 text-center">
                  <input type="checkbox" class="form-check-input-sm key1" id="englishKey1" onclick="setOptionState(this.checked,0,1)">
                  <label class="form-check-label" for="englishKey1">英文</label>
                </div>                                                              
              </div>
              <div class="col-2">
                <div class="form-check p-0 text-center">
                  <input type="checkbox" class="form-check-input-sm key1" id="mathKey1" onclick="setOptionState(this.checked,0,2)">
                  <label class="form-check-label" for="mathKey1">數學</label>
                </div>                                                              
              </div>
              <div class="col-2">
                <div class="form-check p-0 text-center">
                  <input type="checkbox" class="form-check-input-sm key1" id="pro1Key1" onclick="setOptionState(this.checked,0,3)">
                  <label class="form-check-label" for="pro1Key1">專一</label>
                </div>                                                              
              </div>
              <div class="col-2">
                <div class="form-check p-0 text-center">
                  <input type="checkbox" class="form-check-input-sm key1" id="pro2Key1" onclick="setOptionState(this.checked,0,4)">
                  <label class="form-check-label" for="pro2Key1">專二</label>
                </div>                                                              
              </div>
              <div class="col-2">
                <div class="form-check p-0 text-center">
                  <input type="checkbox" class="form-check-input-sm key1" id="totalKey1" onclick="setOptionState(this.checked,0,5)">
                  <label class="form-check-label" for="totalKey1">總分</label>
                </div>                                                              
              </div>                                                                      
            </div> 
            <hr>   
            <p>第二比序</p>
            <div class="row">
              <div class="col-2">
                <div class="form-check p-0 text-center">
                  <input type="checkbox" class="form-check-input-sm key2" id="chineseKey2" onclick="setOptionState(this.checked,1,0)" disabled>
                  <label class="form-check-label" for="chineseKey2">國文</label>
                </div>                                                              
              </div>
              <div class="col-2">
                <div class="form-check p-0 text-center">
                  <input type="checkbox" class="form-check-input-sm key2" id="englishKey2" onclick="setOptionState(this.checked,1,1)" disabled>
                  <label class="form-check-label" for="englishKey2">英文</label>
                </div>                                                              
              </div>
              <div class="col-2">
                <div class="form-check p-0 text-center">
                  <input type="checkbox" class="form-check-input-sm key2" id="mathKey2" onclick="setOptionState(this.checked,1,2)" disabled>
                  <label class="form-check-label" for="mathKey2">數學</label>
                </div>                                                              
              </div>
              <div class="col-2">
                <div class="form-check p-0 text-center">
                  <input type="checkbox" class="form-check-input-sm key2" id="pro1Key2" onclick="setOptionState(this.checked,1,3)" disabled>
                  <label class="form-check-label" for="pro1Key2">專一</label>
                </div>                                                              
              </div>
              <div class="col-2">
                <div class="form-check p-0 text-center">
                  <input type="checkbox" class="form-check-input-sm key2" id="pro2Key2" onclick="setOptionState(this.checked,1,4)" disabled>
                  <label class="form-check-label" for="pro2Key2">專二</label>
                </div>                                                              
              </div>
              <div class="col-2">
                <div class="form-check p-0 text-center">
                  <input type="checkbox" class="form-check-input-sm key2" id="totalKey2" onclick="setOptionState(this.checked,1,5)" disabled>
                  <label class="form-check-label" for="totalKey2">總分</label>
                </div>                                                              
              </div>                                                                      
            </div>
            <hr>
            <p>第三比序</p>
            <div class="row">
              <div class="col-2">
                <div class="form-check p-0 text-center">
                  <input type="checkbox" class="form-check-input-sm key3" id="chineseKey3" onclick="setOptionState(this.checked,2,0)" disabled>
                  <label class="form-check-label" for="chineseKey3">國文</label>
                </div>                                                              
              </div>
              <div class="col-2">
                <div class="form-check p-0 text-center">
                  <input type="checkbox" class="form-check-input-sm key3" id="englishKey3" onclick="setOptionState(this.checked,2,1)" disabled>
                  <label class="form-check-label" for="englishKey3">英文</label>
                </div>                                                              
              </div>
              <div class="col-2">
                <div class="form-check p-0 text-center">
                  <input type="checkbox" class="form-check-input-sm key3" id="mathKey3" onclick="setOptionState(this.checked,2,2)" disabled>
                  <label class="form-check-label" for="mathKey3">數學</label>
                </div>                                                              
              </div>
              <div class="col-2">
                <div class="form-check p-0 text-center">
                  <input type="checkbox" class="form-check-input-sm key3" id="pro1Key3" onclick="setOptionState(this.checked,2,3)" disabled>
                  <label class="form-check-label" for="pro1Key3">專一</label>
                </div>                                                              
              </div>
              <div class="col-2">
                <div class="form-check p-0 text-center">
                  <input type="checkbox" class="form-check-input-sm key3" id="pro2Key3" onclick="setOptionState(this.checked,2,4)" disabled>
                  <label class="form-check-label" for="pro2Key3">專二</label>
                </div>                                                              
              </div>
              <div class="col-2">
                <div class="form-check p-0 text-center">
                  <input type="checkbox" class="form-check-input-sm key3" id="totalKey3" onclick="setOptionState(this.checked,2,5)" disabled>
                  <label class="form-check-label" for="totalKey3">總分</label>
                </div>                                                              
              </div>                                                                      
            </div>
            <hr>
            <p>第四比序</p>
            <div class="row">
              <div class="col-2">
                <div class="form-check p-0 text-center">
                  <input type="checkbox" class="form-check-input-sm key4" id="chineseKey4" onclick="setOptionState(this.checked,3,0)" disabled>
                  <label class="form-check-label" for="chineseKey4">國文</label>
                </div>                                                              
              </div>
              <div class="col-2">
                <div class="form-check p-0 text-center">
                  <input type="checkbox" class="form-check-input-sm key4" id="englishKey4" onclick="setOptionState(this.checked,3,1)" disabled>
                  <label class="form-check-label" for="englishKey4">英文</label>
                </div>                                                              
              </div>
              <div class="col-2">
                <div class="form-check p-0 text-center">
                  <input type="checkbox" class="form-check-input-sm key4" id="mathKey4" onclick="setOptionState(this.checked,3,2)" disabled>
                  <label class="form-check-label" for="mathKey4">數學</label>
                </div>                                                              
              </div>
              <div class="col-2">
                <div class="form-check p-0 text-center">
                  <input type="checkbox" class="form-check-input-sm key4" id="pro1Key4" onclick="setOptionState(this.checked,3,3)" disabled>
                  <label class="form-check-label" for="pro1Key4">專一</label>
                </div>                                                              
              </div>
              <div class="col-2">
                <div class="form-check p-0 text-center">
                  <input type="checkbox" class="form-check-input-sm key4" id="pro2Key4" onclick="setOptionState(this.checked,3,4)" disabled>
                  <label class="form-check-label" for="pro2Key4">專二</label>
                </div>                                                              
              </div>
              <div class="col-2">
                <div class="form-check p-0 text-center">
                  <input type="checkbox" class="form-check-input-sm key4" id="totalKey4" onclick="setOptionState(this.checked,3,5)" disabled>
                  <label class="form-check-label" for="totalKey4">總分</label>
                </div>                                                              
              </div>                                                                      
            </div>
            <hr>
            <button class="btn btn-primary" type="button" onclick="reSort()" disabled>重排</button>                                                        
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-md-10 offset-md-1 col-xl-8 offset-xl-2 col-xxl-6 offset-xxl-3">
        <table class="table px-3 mt-2">
          <thead id="resortPanel" title="按此可重新排序">
            <tr>
              <th class="text-center align-middle bg-primary text-white">序號</th>
              <!--<th class="text-center align-middle bg-primary text-white">班級</th>-->
              <th class="text-center align-middle bg-primary text-white">准考證號</th>
              <th class="text-center align-middle bg-primary text-white">國文</th>
              <th class="text-center align-middle bg-primary text-white">英文</th>
              <th class="text-center align-middle bg-primary text-white">數學</th>
              <th class="text-center align-middle bg-primary text-white">專一</th>
              <th class="text-center align-middle bg-primary text-white">專二</th>
              <th class="text-center align-middle bg-primary text-white">總分</th>
            </tr>
          </thead>
          <tbody id="scoreTable"><script>setTable();</script></tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
<?php } ?>