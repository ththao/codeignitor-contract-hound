<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<!-- application layout -->
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Password reset | Contract Hound</title>
		
		<link rel="shortcut icon" href="/ui/img/logos/contracthound-favicon.png" />
		<meta name="viewport" content="width=device-width, maximum-scale=1.0, minimal-ui" />
		
		<script src="//code.jquery.com/jquery-1.9.1.min.js"></script>
		<script src="/ui/jqueryui/jquery-ui-1.11.4/jquery-ui.min.js"></script>
		<script src="/ui/modernizr/modernizr.js"></script>
		<script src="/ui/bootstrap/js/bootstrap.min.js"></script>
		<script src="/ui/suggest/js/bootstrap-suggest.js"></script>
		<script src="/ui/dropzone/dropzone.js"></script>
		<script src="/ui/tokenfield/dist/bootstrap-tokenfield.min.js"></script>
		<script src="/ui/bootstrap-notify/bootstrap-notify.min.js"></script>
		<script src="/ui/bootstrap-validator/dist/validator.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.5/angular.min.js"></script>
		<script src="//d2wy8f7a9ursnm.cloudfront.net/v7/bugsnag.min.js"></script>
		<script>Bugsnag.start({apiKey: '<?= $_ENV['BUGSNAG_API_KEY'] ?>', releaseStage: '<?= ENVIRONMENT ?>'});</script>
		<script src="/ui/js/app.js"></script>

		<link rel="stylesheet" type="text/css" href="/ui/jqueryui/jquery-ui-1.11.4/jquery-ui.structure.min.css" />
		<link rel="stylesheet" type="text/css" href="/ui/suggest/css/bootstrap-suggest.css" />
		<link rel="stylesheet" type="text/css" href="/ui/tokenfield/dist/css/bootstrap-tokenfield.min.css" />
		<link rel="stylesheet" type="text/css" href="/ui/css/app.css" />
	</head>
	<body ng-app="ContractHoundApp">
		<div class="intercept">
			<div class="intercept-content">
				<div class="intercept-body">
					<div class="intercept-frame">
						<div class="login">
							<div class="login-header">
								<h2>Reset your Password</h2>
								<p>Let's get you back on track. Enter your email address below, and we'll send you a link to reset your password.</p>
							</div>
							
							<form class="login-form" method="post" action="/members/request_reset_password">
								<h6>Email Address</h6>
								<input type="text" name="email" class="form-control input-lg" />
		
								<div class="login-form-footer">
									<div class="login-form-footer-item">
										<input type="submit" class="btn btn-warning btn-lg" value="Reset my Password" />
									</div>
								</div>
							<form>
		
							<div class="login-links">
								<a href="/members/register">Create a new account</a>
								<a href="/members/login">Login</a>
							</div>

							<div class="login-extra">
								<p class="help-block">By logging in, you agree to the Contract Hound <a href="https://www.contracthound.com/terms/" target="_blank">Terms of Service</a>.</p>
								<p class="help-block">&copy; Copyright <?php echo date('Y'); ?> - Flightpath Publishing, All rights reserved.</p>
							</div>
		
						</div>
		
					</div>
				</div>
				<div class="intercept-ad">
					<div class="intercept-ad-background background-silver" style="background-image: url(/ui/img/ads/dismay.jpg);"></div>
					<div class="intercept-ad-graphic">
						<img src="/ui/img/ads/dismay-circle.jpg" />
					</div>
					<div class="intercept-ad-content">
						<h1><span class="text-italic">We can fix this.</span></h1>
						<p>We have the technology...</p>
					</div>
				</div>
			</div>
		</div>
		
		

		<?php if ($this->session->flashdata('success')): ?>
		<script>
			var notifications = new Array(
				[{ title: 'Success: ', message: '<?php echo $this->session->flashdata('success'); ?>' },{ delay: 70000, type: 'success' }]
			);
		</script>
		<?php elseif ($this->session->flashdata('error')): ?>
		<script>
			var notifications = new Array(
				[{ title: 'Error: ', message: '<?php echo $this->session->flashdata('error'); ?>' },{ delay: 70000, type: 'danger' }]
			);
		</script>
		<?php endif; ?>

		<?php $this->load->view('layout/analytics_snippets', ['intercom' => 1]); ?>

	</body>
</html>
