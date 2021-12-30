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
	<nav class="navbar navbar-expand-sm bg-dark navbar-dark p-0 pl-2">
    <div class="container-fluid">
      <!-- 功能表壓縮紐 -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>
      <!-- 功能表超連結 -->
      <div class="collapse navbar-collapse" id="collapsibleNavbar">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link<?php echo ( $func == 'departments' ? ' active' : '' ); ?>" href="<?php echo $_SESSION['projectRoot']; ?>/departments/">校系資料查詢</a>
          </li>
        </ul>

        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link<?php echo ( $func == 'quota' ? ' active' : '' ); ?>" href="<?php echo $_SESSION['projectRoot']; ?>/quota/">名額比較</a>
          </li>
        </ul>

      </div>        
    </div>
	</nav>
<?php } ?>