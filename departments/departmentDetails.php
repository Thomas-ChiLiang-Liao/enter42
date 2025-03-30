<!DOCTYPE html>

<?php 
session_start();
include '../menu.php';
include '../config.ini.php';
$errorPage = "Location: https://$_SERVER[SERVER_NAME]".dirname($_SERVER['SCRIPT_NAME']).'/../error.php?msg';

function sequenceContent($sequence) {
  GLOBAL $result;
  if ( mb_substr($sequence,0,4,'UTF-8') == '指定項目' ) {
    $index = mb_substr($sequence,-1,1,'UTF-8');
    return $result["assignItem$index"];
  } else return $sequence;
}

function dateFormat($date, $type) {
  $timeStamp = strtotime($date);
  $rString = strval((int) substr($date,0,4) - 1911) . '年' . substr($date,5,2) . '月' . substr($date,8,2) . '日';
  switch ( date('w',$timeStamp) ) {
    case 0: $rString .= '(日)'; break;
    case 1: $rString .= '(一)'; break;
    case 2: $rString .= '(二)'; break;
    case 3: $rString .= '(三)'; break;
    case 4: $rString .= '(四)'; break;
    case 5: $rString .= '(五)'; break;
    case 6: $rString .= '(六)'; 
  }
  if ($type == 1) return $rString;
  $rString .= '<br>' . substr($date,11,2) . ':' . substr($date,14,2);
  return $rString;
}

function memoContent($content) {
   if (mb_substr($content,0,2,'UTF-8') == '1.') {
    /*
    $content = str_replace('\r','',$content);
    $rContent = '';
    $list = explode('。', $content);
    for ($i = 0; $i < count($list)-1; $i++) $rContent .= $list[$i] . '。<br>';
    */
    return "<pre>$content</pre>";
  } else return $content;
}

// 避免 SQL Injection
$_GET['depid'] = str_replace('"','',$_GET['depid']);
$_GET['depid'] = str_replace("'","",$_GET['depid']);
// 連線資料庫
$pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8",$guestId,$guestPw);
// 查詢
$sql = "SELECT "
     . "  TVEREDepartment.id AS id, "
     . "  TVERESchool.title AS schTitle, TVEREDepartment.title AS depTitle, "
     . "  TVERESchool.maxTargets AS maxTargets, "
     . "  CONCAT(TVETExamSort.id, TVETExamSort.sort) AS examSort, "
     . "  TVEREDepartment.quotaA, TVEREDepartment.stage2QuotaA, TVEREDepartment.quotaB, TVEREDepartment.stage2QuotaB, "
     . "  TVEREDepartment.chineseMagnification, TVEREDepartment.englishMagnification, TVEREDepartment.mathMagnification, "
     . "  TVEREDepartment.pro1Magnification, TVEREDepartment.pro2Magnification, "
     . "  TVEREDepartment.chineseFilterFlag, TVEREDepartment.englishFilterFlag, TVEREDepartment.mathFilterFlag, TVEREDepartment.pro1FilterFlag, TVEREDepartment.pro2FilterFlag, "
     . "  TVEREDepartment.chineseWeight, TVEREDepartment.englishWeight, TVEREDepartment.mathWeight, "
     . "  TVEREDepartment.pro1Weight, TVEREDepartment.pro2Weight, TVEREDepartment.examScoreRate, "
     . "  TVEREDepartment.assignItem1, TVEREDepartment.assignItem1Threshold, TVEREDepartment.assignItem1Rate, "
     . "  TVEREDepartment.assignItem2, TVEREDepartment.assignItem2Threshold, TVEREDepartment.assignItem2Rate, "
     . "  TVEREDepartment.assignItem3, TVEREDepartment.assignItem3Threshold, TVEREDepartment.assignItem3Rate, "
     . "  TVEREDepartment.assignItem4, TVEREDepartment.assignItem4Threshold, TVEREDepartment.assignItem4Rate, "
     . "  TVEREDepartment.assignItem5, TVEREDepartment.assignItem5Threshold, TVEREDepartment.assignItem5Rate, "
     . "  TVEREDepartment.assignItemExamFee, TVEREDepartment.assignItemCount, TVEREDepartment.certificateExtra, "
     . "  TVEREDepartment.sequence1, TVEREDepartment.sequence2, TVEREDepartment.sequence3, "
     . "  TVEREDepartment.sequence4, TVEREDepartment.sequence5, TVEREDepartment.sequence6, TVEREDepartment.sequenceCount, "
     . "  TVEREDepartment.date1, TVEREDepartment.date2, TVEREDepartment.examDate, TVEREDepartment.date3, "
     . "  TVEREDepartment.date4, TVEREDepartment.date5, TVEREDepartment.date6, TVEREDepartment.checkInDate, "
     . "  TVEREDepartment.B1, TVEREDepartment.B2, "
     . "  TVEREDepartment.C1, TVEREDepartment.C2, TVEREDepartment.C3, TVEREDepartment.C4, TVEREDepartment.C5, TVEREDepartment.C6, "
     . "  TVEREDepartment.C7, TVEREDepartment.C8, TVEREDepartment.C_counts, "
     . "  TVEREDepartment.uploadMemo, TVEREDepartment.assignExamMemo, TVEREDepartment.memo "
     . "FROM TVEREDepartment "
     . "LEFT JOIN TVERESchool ON TVEREDepartment.schid = TVERESchool.id "
     . "LEFT JOIN TVETExamSort ON TVEREDepartment.examSort = TVETExamSort.id "
     . "WHERE TVEREDepartment.id = :depid";
$statement = $pdo->prepare($sql);
$statement->bindParam(':depid', $_GET['depid'], PDO::PARAM_STR, 6);
$statement->execute();
$errMessage = $statement->errorInfo();
if ($errMessage[0] != '00000') {
  header("$errorPage=danger:讀取 TVEREDepartments 發生錯誤(行號: 83)！代碼：$errMessage[0]/$errMessage[1]<br>訊息：$errMessage[2]");
  exit();
}
$result = $statement->fetch(PDO::FETCH_ASSOC);
?>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <title><?php echo $_GET['depid']; ?>簡章內容</title>
  <link rel="icon" href="../images/<?php echo ( $_SERVER["SERVER_NAME"] == "yy33.us" ? "website-design.png" : "logo.icon.png"); ?>" type="image/x-icon">
  <link rel="stylesheet" href="../styles.css">
</head>
<body>
  <div class="container-fluid">
    <div class="row mt-2">
      <div class="col-12">
        <div class="alert alert-danger display-6 text-center align-middle">
          <strong>警告！</strong>下表資料可能有誤，請以紙本簡章或<a href="https://www.jctv.ntut.edu.tw/downloads/114/apply/ugcdrom/index.html" target="_blank">官網</a>資料為主。
          <a href="https://www.jctv.ntut.edu.tw/downloads/114/apply/ugcdrom/printDept.html?dCode=<?php echo $_GET['depid']; ?>" class="btn btn-secondary btn-lg">官網簡章列印</a>
        </div>
        <table class="table table-bordered table-sm">
          <tbody>
            <tr>
              <th class="table-warning text-center align-middle" rowspan="3" style="width: 6.5%">校系科組<br>學程名稱</th>
              <td class="table-light align-middle" rowspan="3" colspan="2" style="width: 9%"><?php echo "$result[schTitle]<br>$result[depTitle]"; ?></td>
              <th class="table-warning text-center align-middle" colspan="4">第一階段</th>
              <th class="table-warning text-center align-middle" colspan="6">第二階段指定項目甄試</th>
              <th class="table-warning text-center align-middle" colspan="2">可選填報名之系<br>科(組)、學程數</th>
              <td class="bg-danger text-white text-center align-middle">
                <?php echo $result['maxTargets']; ?>
              </td>
            </tr>
            <tr>
              <th class="table-warning text-center align-middle" colspan="4">統一入學測驗成績</th>
              <th class="table-warning text-center align-middle" colspan="7">甄選總成績採計方式</th>
              <th class="table-warning text-center align-middle" colspan="2">總成績同分參酌方法</th>
            </tr>
            <tr>
              <th class="table-warning text-center align-middle" rowspan="7">成<br>績<br>處<br>理<br>方<br>式</th>
              <th class="table-warning text-center align-middle" style="width: 3.8%">科目</th>
              <th class="table-warning text-center align-middle" style="width: 4.3%">篩選倍率</th>
              <th class="table-warning text-center align-middle">同級分超額<br>篩選科目</th>
              <th class="table-warning text-center align-middle" colspan="2">統一入學測驗<br>成績加權</th>
              <th class="table-warning text-center align-middle">指定項目</th>
              <th class="table-warning text-center align-middle" style="width: 3%">最低<br>得分</th>
              <th class="table-warning text-center align-middle" style="width: 3%">滿分</th>
              <th class="table-warning text-center align-middle">占總成績<br>比例</th>
              <th class="table-warning text-center align-middle">證照或<br>得獎加分</th>
              <th class="table-warning text-center align-middle" style="width: 1.5%">順序</th>
              <th class="table-warning text-center align-middle">項目</th>
            </tr>
            <tr>
              <th class="table-warning text-center align-middle">校系科組<br>學程代碼</th>
              <td class="table-light text-center align-middle" colspan="2"><?php echo $result['id']; ?></td>
              <td class="table-light text-center align-middle">國文</td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['chineseMagnification'] == null ? '--' : $result['chineseMagnification'] ); ?>
              </td>
              <td class="table-light text-center align-middle"><?php echo $result['chineseFilterFlag']; ?></td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['chineseWeight'] == null ? '--' : "✕$result[chineseWeight] 倍" ); ?>
              </td>
              <td class="table-light text-center align-middle" rowspan="6">合佔<br>總成績<br>比例<br><?php echo $result['examScoreRate']; ?>％</td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['assignItem1'] == null ? '--' : $result['assignItem1'] ); ?>
              </td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['assignItem1Threshold'] == null ? '--' : $result['assignItem1Threshold'] ); ?>
              </td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['assignItem1'] == null ? '--' : '100' ); ?>
              </td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['assignItem1Rate'] == null ? '--' : "$result[assignItem1Rate]％" ); ?>
              </td>
              <td class="table-light text-center align-middle" rowspan="6">
                <?php echo ( $result['certificateExtra'] == 0 ? '不予<br>加分' : '依加分<br>標準' ); ?>
              </td>
              <th class="table-warning text-center align-middle">1</th>
              <td class="table-light text-center align-middle">
                <?php echo sequenceContent($result['sequence1']); ?>
              </td>
            </tr>
            <tr>
              <th class="table-warning text-center align-middle">招生群(類)別</th>
              <td class="table-light text-center align-middle" colspan="2"><?php echo $result['examSort']; ?></td>
              <td class="table-light text-center align-middle">英文</td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['englishMagnification'] == null ? '--' : $result['englishMagnification'] ); ?>
              </td>
              <td class="table-light text-center align-middle"><?php echo $result['englishFilterFlag']; ?></td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['englishWeight'] == null ? '--' : "✕$result[englishWeight] 倍" ); ?>
              </td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['assignItem2'] == null ? '--' : $result['assignItem2'] ); ?>
              </td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['assignItem2Threshold'] == null ? '--' : $result['assignItem2Threshold'] ); ?>
              </td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['assignItem2'] == null ? '--' : '100' ); ?>
              </td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['assignItem2Rate'] == null ? '--' : "$result[assignItem2Rate]％" ); ?>
              </td>
              <th class="table-warning text-center align-middle">2</th>
              <td class="table-light text-center align-middle">
                <?php echo sequenceContent($result['sequence2']); ?>
              </td>              
            </tr>
            <tr>
              <th class="table-warning text-center align-middle">考生身分</th>
              <th class="table-warning text-center align-middle">招生名額</th>
              <th class="table-warning text-center align-middle">預計甄試人數</th>
              <td class="table-light text-center align-middle">數學</td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['mathMagnification'] == null ? '--' : $result['mathMagnification'] ); ?>
              </td>
              <td class="table-light text-center align-middle"><?php echo $result['mathFilterFlag']; ?></td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['mathWeight'] == null ? '--' : "✕$result[mathWeight] 倍" ); ?>
              </td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['assignItem3'] == null ? '--' : $result['assignItem3'] ); ?>
              </td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['assignItem3Threshold'] == null ? '--' : $result['assignItem3Threshold'] ); ?>
              </td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['assignItem3'] == null ? '--' : '100' ); ?>
              </td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['assignItem3Rate'] == null ? '--' : "$result[assignItem3Rate]％" ); ?>
              </td>
              <th class="table-warning text-center align-middle">3</th>
              <td class="table-light text-center align-middle">
                <?php echo sequenceContent($result['sequence3']); ?>
              </td> 
            </tr>  
            <tr>
              <th class="table-warning text-center align-middle" rowspan="2">一般考生</th>
              <td class="table-light text-center align-middle" rowspan="2"><?php echo $result['quotaA']; ?></td>
              <td class="table-light text-center align-middle" rowspan="2"><?php echo $result['stage2QuotaA']; ?></td>  
              <td class="table-light text-center align-middle">專業一</td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['pro1Magnification'] == null ? '--' : $result['pro1Magnification'] ); ?>
              </td>
              <td class="table-light text-center align-middle"><?php echo $result['pro1FilterFlag']; ?></td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['pro1Weight'] == null ? '--' : "✕$result[pro1Weight] 倍" ); ?>
              </td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['assignItem4'] == null ? '--' : $result['assignItem4'] ); ?>
              </td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['assignItem4Threshold'] == null ? '--' : $result['assignItem4Threshold'] ); ?>
              </td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['assignItem4'] == null ? '--' : '100' ); ?>
              </td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['assignItem4Rate'] == null ? '--' : "$result[assignItem4Rate]％" ); ?>
              </td>
              <th class="table-warning text-center align-middle">4</th>
              <td class="table-light text-center align-middle">
                <?php echo sequenceContent($result['sequence4']); ?>
              </td>              
            </tr> 
            <tr>
              <td class="table-light text-center align-middle">專業二</td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['pro2Magnification'] == null ? '--' : $result['pro2Magnification'] ); ?>
              </td>
              <td class="table-light text-center align-middle"><?php echo $result['pro2FilterFlag']; ?></td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['pro2Weight'] == null ? '--' : "✕$result[pro2Weight] 倍" ); ?>
              </td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['assignItem5'] == null ? '--' : $result['assignItem5'] ); ?>
              </td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['assignItem5Threshold'] == null ? '--' : $result['assignItem5Threshold'] ); ?>
              </td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['assignItem5'] == null ? '--' : '100' ); ?>
              </td>
              <td class="table-light text-center align-middle">
                <?php echo ( $result['assignItem5Rate'] == null ? '--' : "$result[assignItem5Rate]％" ); ?>
              </td>
              <th class="table-warning text-center align-middle">5</th>
              <td class="table-light text-center align-middle">
                <?php echo sequenceContent($result['sequence5']); ?>
              </td>              
            </tr> 
            <tr>
              <th class="table-warning text-center align-middle">原住民考生</th>
              <td class="table-light text-center align-middle"><?php echo $result['quotaB']; ?></td>
              <td class="table-light text-center align-middle"><?php echo $result['stage2QuotaB']; ?></td>
              <td class="table-light text-center align-middle" colspan="3">同級分超額篩選為同級分超<br>額篩選勾選科目級分之總和</td>
              <td class="table-light text-center align-middle">--</td>
              <td class="table-light text-center align-middle">--</td>
              <td class="table-light text-center align-middle">--</td>
              <td class="table-light text-center align-middle">--</td>
              <td class="table-light text-center align-middle">--</td>
              <th class="table-warning text-center align-middle">6</th>
              <td class="table-light text-center align-middle"><?php echo sequenceContent($result['sequence6']); ?></td>
            </tr>
            <tr>
              <th class="table-warning text-center align-middle" rowspan="4">指定項目<br>甄試費</th>
              <td class="table-light text-center align-middle" colspan="2" rowspan="4"><?php echo $result['assignItemExamFee'] ?> 元</td>
              <th class="table-warning text-center align-middle" colspan="3" rowspan="8">學習歷程<br>備審資料</th>
              <th class="table-warning text-center align-middle" colspan="9">項目</th>
              <th class="table-warning text-center align-middle">上傳檔案件數上限</th>
            </tr>
            <tr>
              <th class="table-warning text-left align-middle" colspan="9">A.修課紀錄&nbsp;&nbsp;※應屆畢業生一律由就讀學校上傳；110學年度以後畢業生，一律由學習歷程中央資料庫提供；其餘畢業生或同等學力者，一律自行上傳歷年成績單(PDF檔)</th>
              <td class="table-light text-center align-middle">1件</td>
            </tr>
            <tr>
              <th class="table-warning text-left align-middle" colspan="2" rowspan="2">B.課程學習成果</th>
              <th class="table-warning text-left align-middle" colspan="7">B-1.專題實作、實習科目學習成果(含技能領域)(*須至少上傳1件)在符合上傳件數上限下，可上傳專題實作、亦可上傳實習科目學習成果(含技能領域)、也可二者皆上傳</th>
              <td class="table-light text-center align-middle"><?php echo $result['B1']; ?>件</td>
            </tr>
            <tr>
              <th class="table-warning text-left align-middle" colspan="7">B-2.其他課程學習(作品)成果</th>
              <td class="table-light text-center align-middle"><?php echo $result['B2']; ?>件</td>
            </tr>
            <tr>
              <th class="table-warning text-center align-middle" rowspan="3">學習歷程<br>備審資料<br>上傳暨繳費<br>截止時間</th>
              <td class="table-light text-center align-middle" colspan="2" rowspan="3"><?php echo dateFormat($result['date1'],0); ?>止</td>
              <th class="table-warning text-left align-middle" colspan="9">
                C.多元表現：
                <?php
                $cString = '';
                for ($i = 1; $i <= 8; $i++) $cString .= ( $result["C$i"] == '採計' ? ( strlen($cString) == 0 ? "C-$i" : "、C-$i" ) : '' );
                echo $cString;
                ?>
              </th>
              <td class="table-light text-center align-middle"><?php echo $result['C_counts']; ?>件</td>
            </tr>
            <tr>
              <th class="table-warning text-left align-middle" colspan="9">D-1.多元表現綜整心得</th>
              <td class="table-light text-center align-middle">1件</td>
            </tr>
            <tr>
              <th class="table-warning text-left align-middle" colspan="9">D-2.學習歷程自述(含學習歷程反思、就讀動機、未來學習計畫與生涯規劃)</th>
              <td class="table-light text-center align-middle">1件</td>              
            </tr>
            <tr>
              <th class="table-warning text-center align-middle" rowspan="2">公告第二階段<br>甄試名單<br>及注意事項</th>
              <td class="table-light text-center align-middle" colspan="2" rowspan="2"><?php echo ( $result['date2'] == null ? '--' : dateFormat($result['date2'],0).'起' ); ?></td>
              <th class="table-warning text-left align-middle" colspan="9">D-3.其他有利審查資料</th>
              <td class="table-light text-center align-middle">1件</td>
            </tr>
            <tr>
              <th class="table-warning text-center align-middle" colspan="3" rowspan="3">學習歷程<br>備審資料<br>上傳說明</th>
              <td class="table-light text-left align-middle" colspan="10" rowspan="3"><?php echo memoContent($result['uploadMemo']); ?></td>
            </tr>
            <tr>
              <th class="table-warning text-center align-middle">甄試日期</th>
              <td class="table-light text-center align-middle" colspan="2"><?php echo ( $result['examDate'] == null ? '--' : dateFormat($result['examDate'],1) ); ?></td>
            </tr>
            <tr>
              <th class="table-warning text-center align-middle">公告甄選<br>總成績日期</th>
              <td class="table-light text-center align-middle" colspan="2"><?php echo dateFormat($result['date3'],0); ?>起</td>
            </tr> 
            <tr>
              <th class="table-warning text-center align-middle">甄選總成績<br>複查截止日期</th>
              <td class="table-light text-center align-middle" colspan="2"><?php echo dateFormat($result['date4'],0); ?>止</td>
              <th class="table-warning text-center align-middle" colspan="3" rowspan="4">指定項目甄試說明</th> 
              <td class="table-light text-left align-middle" colspan="10" rowspan="4"><?php echo memoContent($result['assignExamMemo']); ?></td>             
            </tr>  
            <tr>
              <th class="table-warning text-center align-middle">公告正(備)取生<br>名單日期</th>
              <td class="table-light text-center align-middle" colspan="2"><?php echo dateFormat($result['date5'],0); ?>起</td>              
            </tr>  
            <tr>
              <th class="table-warning text-center align-middle">正(備)取生名單<br>複查截止日期</th>
              <td class="table-light text-center align-middle" colspan="2"><?php echo dateFormat($result['date6'],0); ?>止</td>              
            </tr>     
            <tr>
              <th class="table-warning text-center align-middle">分發錄取生<br>報到截止日</th>
              <td class="table-light text-center align-middle" colspan="2"><?php echo dateFormat($result['checkInDate'],0); ?>止</td>              
            </tr>     
            <tr>
              <th class="table-warning text-center align-middle" colspan="3">備註</th>
              <td class="table-light text-left align-middle" colspan="14"><?php echo memoContent($result['memo']); ?></td>
            </tr>                         
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>