<?php
function menu($func) { 
  GLOBAL $vhSchool;
  GLOBAL $extraFunction;
?>

	<div class="container-fluid">
		<!-- 標題列 -->
		<div class="row d-none d-sm-block">
			<div class="col-12 m-0 pt-3 pb-1 bg-primary">
				<h3 class="text-center text-white"><?php echo $vhSchool; ?>預選系統</h4>
			</div>
		</div>
	</div>
	
	<!-- 功能表 -->
  <?php if (isset($_SESSION['studentName'])) { ?>
  <nav class="navbar navbar-expand-lg bg-dark navbar-dark p-0">
    <div class="container-fluid">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link<?php echo ( $func == 'changePassword' ? ' active' : '' ); ?>" href="<?php echo $_SESSION['projectRoot']; ?>/changePassword/">
              修改密碼
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link<?php echo ( $func == 'departmentList' ? ' active' : '' ); ?>" href="<?php echo $_SESSION['projectRoot']; ?>/departmentList/">
              校系預選
            </a>
          </li>
          <?php if ($extraFunction) { ?>
          <li class="nav-item">
            <a class="nav-link<?php echo ( $func == 'interviewSetting' ? ' active' : '' ); ?>" href="<?php echo $_SESSION['projectRoot']; ?>/interviewSetting/">
              專業問題模擬面試登記<sub><?php echo ( $_SESSION['simInterView'] == 1 ? '參加' : '不參加' ); ?></sub>
            </a>
          </li>
          <?php } ?>
          <li class="nav-item">
            <a class="nav-link<?php echo ( $func == 'operateRecords' ? ' active' : '' ); ?>" href="<?php echo $_SESSION['projectRoot']; ?>/operateRecords/">
              紀錄查詢
            </a>
          </li>
          <?php if ($_SESSION['preDeps'] != null) { ?>
          <li class="nav-item">
            <a class="nav-link<?php echo ( $func == 'refData' ? ' active' : '' ); ?>" href="<?php echo $_SESSION['projectRoot']; ?>/refData/">
              落點分析
            </a>
          </li>
          <?php } ?>
        </ul>
        <span class="navbar-text text-white">
          操作人員：<?php echo $_SESSION['studentName']; ?>
          <a href="<?php echo $_SESSION['projectRoot']; ?>/logout.php" class="nav-link d-inline">登出</a>
        </span>
      </div>
    </div>
  </nav>
<?php } } ?>