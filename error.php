<!DOCTYPE html>
<?php
  $message = explode(':', $_GET['msg']);
  include 'config.ini.php';
?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <title>預選系統</title>
  <link rel="icon" href="images/logo.icon.png" type="image/x-icon">
</head>
<body>
<div class="container-fluid">
		<!-- 標題列 -->
		<div class="row d-none d-sm-block">
			<div class="col-12 m-0 pt-3 pb-1 bg-primary">
				<h3 class="text-center text-white"><?php echo $vhSchool; ?>預選系統</h4>
			</div>
		</div>
	</div>
  <div class="container-fluid">
    <div class="row mt-5">
      <div class="alert alert-<?php echo $message[0]; ?> text-center">
        <?php 
        echo $message[1]; 
        foreach ($message AS $i => $data) {
          if ($i <= 1) continue;
          else echo ":$data";
        }
        ?>
      </div>
    </div>
  </div>
</body>
</html>