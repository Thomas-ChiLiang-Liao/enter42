<!DOCTYPE html>

<?php 
session_start();
include '../menu.php';
include '../config.ini.php';
$pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8",$guestId,$guestPw);
$statement = $pdo->query('SELECT id, sort FROM TVETExamSort WHERE id <= 21;');
$statement->execute();
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
  <style>
    tr.link { cursor: pointer; }
  </style>
  <!-- 本頁所用之 jQuery / javascript -->
  <script>
    $(document).ready(function(){
      $(".link").click(function(){
        window.open($(this).data("href"), "_blank");
      });
      $("select").change(function(){
        $(this).parent().submit();
      });
    });
  </script>

</head>
<body>
  <?php menu('departments'); ?>
  <div class="container-fluid">
    <div class="row mt-0">
      <div class="col-12 offset-md-4 col-md-4 text-center">
        <form action="index.php" method="post">
          <select class="form-select form-select-sm mt-1" name="selector">
            <option value='00'>請選擇校系類別</option>
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
        . "  TVERESchool.isRestricted AS isRestricted, "
        . "  TVEREDepartment.quotaA AS quotaA, TVEREDepartment.stage2QuotaA AS stage2QuotaA, "
        . "  TVEREDepartment.examDate AS examDate "
        . "FROM TVEREDepartment "
        . "LEFT JOIN TVERESchool ON TVEREDepartment.schid = TVERESchool.id "
        . "WHERE TVEREDepartment.examSort = :examSort AND TVEREDepartment.quotaA <> 0 "
        . "ORDER BY TVERESchool.isPublic DESC, TVEREDepartment.id ASC;";
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':examSort', $_POST['selector'], PDO::PARAM_STR, 2);
      $statement->execute();
      if ( !$statement ) echo '發生錯誤，代碼：' . $statement->errorCodce();
    ?>
    <div class="row mt-1">
      <div class="col-12">
        <table class="table table-striped table-sm table-bordered table-hover">
          <thead class="table-primary">
            <tr>
              <th class="text-center align-middle">校系</th>
              <th class="text-center align-middle">一校一系</th>
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
              <th class="text-center align-middle">一校一系</th>
              <th class="text-center align-middle">一般生名額</th>
              <th class="text-center align-middle">第二階段名額</th>
              <th class="text-center align-middle">甄試日期</th>
            </tr>
            <?php } ?>
            <tr class="link" data-href="departmentDetails.php?depid=<?php echo $record['depid']; ?>">
              <td class="align-middle">
                <?php echo "<strong>$record[depid]</strong>$record[title]"; ?>
              </td>
              <td class="text-center align-middle text-white <?php echo ( $record['isRestricted'] == 1 ? 'bg-danger' : 'bg-success'); ?>">
                <?php echo ( $record['isRestricted'] == 1 ? '是' : '否' ); ?>
              </td>
              <td class="text-center align-middle"><?php echo "$record[quotaA]"; ?></td>
              <td class="text-center align-middle"><?php echo "$record[stage2QuotaA]"; ?></td>
              <td class="text-center align-middle"><?php echo ( $record['examDate'] == null ? '--' : date('Y-m-d', $examDate) . ' (' . $weekDay . ')' ); ?></td>
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