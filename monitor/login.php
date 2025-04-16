<?php
session_start();
$_SESSION['serverRoot'] = "https://$_SERVER[SERVER_NAME]" . dirname( $_SERVER['SCRIPT_NAME'] );
try {
  // 消除可能的 SQL Injection
  foreach ($_POST as $i => $data) {
    $data = str_replace('"','',$data);
    $data = str_replace("'","",$data);
    $_POST[$i] = $data;
  }

  // 建立 PDO 連線
  $pdo = new PDO("mysql:host=$_POST[server]:3307;dbname=enter42;charset=utf8mb4", $_POST['account'], $_POST['password']);

  // 設定錯誤模式：發生錯誤時丟出例外
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  //echo "✅ 資料庫連線成功！";
  //設定 session 變數
  foreach ($_POST as $i => $data) $_SESSION[$i] = $_POST[$i];

} catch (PDOException $e) {
  // 捕捉連線錯誤並顯示（或寫入 log）
  //echo "❌ 資料庫連線失敗：" . $e->getMessage();
  //exit;
  $_SESSION['msg'] = "danger, 資料庫連線時錯誤, ".$e->getMessage();

  // 可選：記錄 log（範例）
  // error_log($e->getMessage(), 3, 'pdo_error.log');
}
header("Location: $_SESSION[serverRoot]/main");
//print_r ($_SESSION);
?>