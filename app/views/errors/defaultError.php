<!DOCTYPE html>
<html>
	<head>
		<title>vFramework</title>

		<link href='http://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
		<link href='<?=RESOURCES?>bootstrap/css/bootstrap.css' rel='stylesheet' type='text/css'>
		<link href='<?=RESOURCES?>fonts/Font-awesome/css/font-awesome.min.css' rel='stylesheet'>

		<style type="text/css">
			body {font-family: 'Lato', sans-serif;}
			h2 {font-size: 50px; color: #B82020;}
			p {font-size: 16px; font-weight: bold;}
		</style>
	</head>

	<body>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<h2><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> A error has been detected!</h2>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="well">
						<?php foreach ( $this->params[ 'errors' ] as $error ): ?>
							<p><?=$error?></p>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
