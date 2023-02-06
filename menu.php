<?php
function menu($func) { 
  GLOBAL $vhSchool;
	session_start(); 
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
  <nav class="navbar navbar-expand-lg bg-dark navbar-dark p-0">
    <div class="container-fluid">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link<?php echo ( $func == 'departments' ? ' active' : '' ); ?>" href="<?php echo $_SESSION['projectRoot']; ?>/departments/">校系資料查詢</a>
          </li>

          <li class="nav-item">
            <a class="nav-link<?php echo ( $func == 'quota' ? ' active' : '' ); ?>" href="<?php echo $_SESSION['projectRoot']; ?>/quota/">名額比較</a>
          </li>

          <li class="nav-item">
            <a class="nav-link<?php echo ( $func == 'listByClass' ? ' active' : '' ); ?>" href="<?php echo $_SESSION['projectRoot']; ?>/listByClass/">班級預選統計</a>
          </li>
          
          <li class="nav-item">
            <a class="nav-link<?php echo ( $func == 'listByExamSort' ? ' active' : '' ); ?>" href="<?php echo $_SESSION['projectRoot']; ?>/listByExamSort/">類別預選統計</a>
          </li>          
        
        </ul>
        <span class="navbar-text text-white">
          <a href="<?php echo $_SESSION['projectRoot']; ?>/preSelect/" class="nav-link d-inline">預選登入</a>
        </span>
      </div>
    </div>
  </nav>
<?php } ?>