<?php
function menu($func) { 
  GLOBAL $vhSchool;
?>

	<div class="container-fluid">
		<!-- 標題列 -->
		<div class="row d-none d-sm-block">
			<div class="col-12 m-0 pt-3 pb-1 bg-primary">
				<h3 class="text-center text-white"><?php echo $vhSchool; ?>預選系統-管理</h4>
			</div>
		</div>
	</div>
	
	<!-- 功能表 -->
  <?php if (isset($_SESSION['name'])) { ?>
  <nav class="navbar navbar-expand-lg bg-dark navbar-dark p-0">
    <div class="container-fluid">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link<?php echo ( $func == 'changePassword' ? ' active' : '' ); ?>" href="<?php echo $_SESSION['projectRoot']; ?>/changePassword/">修改密碼</a>
          </li>

          <?php if ($_SESSION['optype'] == 1) { ?>
          <li class="nav-item">
            <a class="nav-link<?php echo ( $func == 'operatorMaintain' ? ' active' : '' ); ?>" href="<?php echo $_SESSION['projectRoot']; ?>/operatorMaintain/">操作人員設定</a>
          </li>

          <li class="nav-item">
            <a class="nav-link<?php echo ( $func == 'studentDataUpload' ? ' active' : '' ); ?>" href="<?php echo $_SESSION['projectRoot']; ?>/studentDataUpload/">學生/班級資料上傳</a>
          </li>
          <?php } ?>

          <li class="nav-item">
            <a class="nav-link<?php echo ( $func == 'viewStudentData' ? ' active' : '' ); ?>" href="<?php echo $_SESSION['projectRoot']; ?>/viewStudentData/">檢視學生資料</a>
          </li>
        
          <?php if ($_SESSION['optype'] == 1) { ?>
          <li class="nav-item">
            <a class="nav-link<?php echo ( $func == 'webPageSetting' ? ' active' : '' ); ?>" href="<?php echo $_SESSION['projectRoot']; ?>/webPageSetting/">網頁開關設定</a>
          </li>
          <li class="nav-item">
            <a class="nav-link<?php echo ( $func == 'scoreUpload' ? ' active' : '' ); ?>" href="<?php echo $_SESSION['projectRoot']; ?>/scoreUpload/">統測成績上傳</a>
          </li>
          <?php } ?>

          <li class="nav-item">
            <a class="nav-link<?php echo ( $func == 'dataDownload' ? ' active' : '' ); ?>" href="<?php echo $_SESSION['projectRoot']; ?>/dataDownload/">預選結果下載</a>
          </li>

          <li class="nav-item">
            <a class="nav-link<?php echo ( $func == 'simulateInterviewDownload' ? ' active' : '' ); ?>" href="<?php echo $_SESSION['projectRoot']; ?>/simulateInterviewDownload/">專業問題模擬面試名單(報名結果)</a>
          </li>

          <li class="nav-item">
            <a class="nav-link<?php echo ( $func == 'phase1Upload' ? ' active' : '' ); ?>" href="<?php echo $_SESSION['projectRoot']; ?>/phase1Upload/">一階篩選結果上傳</a>
          </li>          

          <li class="nav-item">
            <a class="nav-link<?php echo ( $func == 'simulateInterviewDownloadB' ? ' active' : '' ); ?>" href="<?php echo $_SESSION['projectRoot']; ?>/simulateInterviewDownloadB/">專業問題模擬面試名單(一階通過)</a>
          </li>          

        </ul>
        <span class="navbar-text text-white">
          操作人員：<?php echo $_SESSION['name']; ?>
          <a href="<?php echo $_SESSION['projectRoot']; ?>/logout.php" class="nav-link d-inline">登出</a>
        </span>
      </div>
    </div>
  </nav>
<?php } } ?>