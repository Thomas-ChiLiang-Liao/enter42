<?php
function menu($func) {
  session_start();
?>
<!-- 🔷 橫幅 -->
<div class="bg-primary text-white text-center py-3">
  <h1 class="mb-0">大安高工</h1>
</div>

<!-- 🔶 Navbar -->
<?php if ( isset($_SESSION['server']) ) { ?> 
<nav class="navbar navbar-expand-sm navbar-dark bg-dark">
  <div class="container-fluid">
    <!-- 功能表縮放鈕 -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <!-- 功能表 -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <!-- 左側功能 -->
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link<?= ( $func == 'dailyList' ? ' active' : '' ) ?>" href="<?= $_SESSION['serverRoot'].'/dailyList.php?func=1' ?>" >每人操作人次</a>
        </li>
        <li class="nav-item">
          <a class="nav-link<?= ( $func == 'operateTimesListByTimes' ? ' active' : '' ) ?>" href="<?= $_SESSION['serverRoot'].'/operateTimesListByTimes.php?func=1' ?>">每人操作次數(由大到小排列)</a>
        </li>
        <li class="nav-item">
          <a class="nav-link<?= ( $func == 'operateTimesListByClass' ? ' active' : '' ) ?>" href="<?= $_SESSION['serverRoot'].'/operateTimesListByClass.php?func=1' ?>">每人操作次數-以班查詢</a>
        </li>
      </ul>

      <!-- 右側伺服器資訊 -->
      <span class="navbar-text text-white">
        🌐 登入伺服器：<?php echo $_SESSION['server']; ?>&nbsp;&nbsp;
        <a href="<?php echo $_SESSION['serverRoot']; ?>/logout.php" class="nav-link d-inline">登出</a>
      </span>
    </div>
  </div>
</nav>
<?php } ?>

<!-- Message -->
<?php if ( isset($_SESSION['msg']) ) { 
  $msg = explode(',', $_SESSION['msg']);
?>
<div class="container my-4">
  <div class="row justify-content-center">
    <div class="col-12 col-sm-10 col-md-8 col-lg-6">
      <div class="card shadow">
        <div class="card-header text-white bg-<?= $msg[0] ?>">
          <?= $msg[1] ?>
        </div>
        <div class="card-body text-<?= $msg[0] ?>">
          <?= $msg[2] ?>
        </div>
        <div class="card-footer">
          <?php //print_r ($_SESSION); ?>
          <a href="<?= $_SESSION['serverRoot'] ?>" class="btn btn-<?= $msg[0] ?>">重新登入</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
  unset( $_SESSION['msg'] );
} }
?>