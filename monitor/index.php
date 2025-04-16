<!DOCTYPE html>
<?php
  if ( !isset( $_SERVER['HTTP_X_HTTPS'] ) OR ( $_SERVER['HTTP_X_HTTPS'] != 'on' ) ) header( "Location: https://$_SERVER[SERVER_NAME]$_SERVER[REQUEST_URI]" );
  else {
    include '../config.ini.php';
    include 'menu.php';
 ?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <title>預選系統-操作紀錄</title>
  <link rel="icon" href="../images/<?php echo ( $_SERVER["SERVER_NAME"] == "yy33.us" ? "website-design.png" : "logo.icon.png"); ?>" type="image/x-icon">
</head>
<body>
  <?php menu(''); ?>
  <!-- 📥 登入表單 -->
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-12 col-sm-10 col-md-6 col-lg-4">
        <div class="card shadow">
          <div class="card-body p-4">
            <h4 class="text-center mb-4">🔐 登入系統</h4>

            <form method="post" action="login.php">
              <!-- ▼ 伺服器下拉選單 -->
              <div class="mb-3">
                <label for="serverSelect" class="form-label">選擇伺服器</label>
                <select class="form-select" id="serverSelect" name="server" required>
                  <option value="">請選擇</option>
                  <option value="photo.taivs.tp.edu.tw">大安高工</option>
                  <option value="cwds.taivs.tp.edu.tw">木柵高工</option>
                  <option value="yy33.us">開發伺服器</option>
                </select>
              </div>

              <!-- ▼ 帳號 -->
              <div class="mb-3">
                <label for="account" class="form-label">帳號</label>
                <input type="text" class="form-control" id="account" name="account" placeholder="請輸入帳號" required>
              </div>

              <!-- ▼ 密碼輸入欄 -->
              <div class="mb-3">
                <label for="password" class="form-label">密碼</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="請輸入密碼" required>
              </div>

              <!-- ▼ 送出按鈕 -->
              <button type="submit" class="btn btn-primary w-100">登入</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
<?php } ?>