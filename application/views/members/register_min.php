<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Register | Contract Hound</title>
		
		<link rel="shortcut icon" href="/ui/img/logos/contracthound-favicon.png" />
		<meta name="viewport" content="width=device-width, maximum-scale=1.0, minimal-ui" />
		
		<script src="//code.jquery.com/jquery-1.9.1.min.js"></script>
		<script src="/ui/jqueryui/jquery-ui-1.11.4/jquery-ui.min.js"></script>
		<script src="/ui/modernizr/modernizr.js"></script>
		<script src="/ui/bootstrap/js/bootstrap.min.js"></script>
		<script src="/ui/suggest/js/bootstrap-suggest.js"></script>
		<script src="/ui/dropzone/dropzone.js"></script>
		<script src="/ui/tokenfield/dist/bootstrap-tokenfield.min.js"></script>
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
							<div class="login-logo">
								<div class="login-logo-graphic">
									<svg class="drawme" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewbox="0 0 128 128" enable-background="new 0 0 128 128" xml:space="preserve">
										<path fill="none" stroke="#dae3ed" stroke-width="4" stroke-miterlimit="10" d="M53.7,9.4L53.7,9.4L38.3,37.6
											c0.6,5.4,12.2,16,25.6,5.6V19.6L53.7,9.4H38.6l-4.4,6.1H21.4c0,0,0,5.8,0,7.4c0,10.7,17.6,3,17.6,19c0,5.3-3.4,12.5-5.3,16.8l0,0
											l-12.5,3.4c0,0-5.8,1.9-6.7,9.2c-2.1,16.5-1.7,13.1-3.4,21.8c5.7,0,9.6-3,10.2-5.9c1.4-7.6,1.9-12.7,1.9-12.7h9.1l-0.1,0
											c-0.7-3.8-0.7-8.2,0.5-13.1c0.2-0.7,0.5-1.6,1-2.8c-0.5,1.2-0.9,2.1-1,2.8c-1.2,4.9-1.2,9.3-0.5,13.1c2,11.4,9.9,17.7,9.9,17.7
											l-4.9,17.4c0,0-1.4,0-2.7,0c-8.2,0-8.4,9-8.4,9h3.2h11.1l6.5-6.9c0,0,17.5-32.7,17.5-42.5c0-5.8-4.7-10.6-10.6-10.6
											c-5.8,0-10.6,4.7-10.6,10.6c0,10.5,21.6,40.3,21.6,40.3h-2.2c-2.3,0-7.6,2.4-7.6,9h45.7c4.2,0,16.5-4.5,16.5-14.3
											c0,9.8-12.3,14.3-16.5,14.3H70.3c0-6.7,5.3-9,7.6-9c3.3,0,7,0,7,0s-6.2-3.1-11-5.5c-2.4-1.2-6.8-3.4-6.8-8.5c0-5.6,4.3-8.3,8.8-8.3
											h13.1H75.9c-4.6,0-8.8,2.7-8.8,8.3c0,5.1,4.3,7.3,6.8,8.5c4.8,2.4,11,5.5,11,5.5s-3.7,0-7,0c-2.3,0-7.6,2.4-7.6,9h25.3
											c2.5,0,4.5-2,4.5-4.5c0-2.7-2-4.6-4.5-4.6c0,0,6.7-5.2,6.7-12.7c0-5.7-2.2-9.1-3.9-11.2c-9.2-11.8-34.5-40.2-34.5-40.2v-2.3" />
									</svg>
								</div>
							</div>
		
							<div class="login-header">
								<h2>Get started for free!</h2>
								<p>Managing your contracts is easy with Contract Hound. Start your 14-day free trial today. No credit card required.</p>
								<p class="help-block">Already have a Contract Hound account? <a href="/members/login">Log in</a></p>
							</div>

							<?php if ($this->session->flashdata('error')): ?>
							<div class="alert alert-danger alert-dismissable">
			                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
								<?php echo $this->session->flashdata('error'); ?>
							</div>
							<?php endif; ?>
							
							<form class="login-form" action="/members/register" method="post">
								<h6>Email</h6>
								<input name="email" type="email" class="form-control input-lg" />
							
								<h6>Password</h6>
								<input name="password" type="password" class="form-control input-lg" />
								<p class="help-block">Passwords should be super secure and stuff...</p>
							
								<h6>Company Name</h6>
								<input type="text" class="form-control input-lg" />
								<p class="help-block">Company name is displayed to other team members in your account.</p>

								<div class="login-form-footer">
									<div class="login-form-footer-item">
										<input type="submit" class="btn btn-primary btn-lg" value="Create my Account" />
									</div>
								</div>
							<form>
		
							<div class="login-extra">
								<p class="help-block">By clicking "Create my Account", you agree to the Contract Hound <a href="https://www.contracthound.com/terms/" target="_blank">Terms of Service</a>.</p>
								<p class="help-block">&copy; Copyright <?php echo date('Y'); ?> - Contract Hound LLC., All rights reserved.</p>
							</div>
						</div>
		
					</div>
				</div>

				<div class="intercept-ad">
					<div class="intercept-ad-background background-silver" style="background-image: url(/ui/img/ads/pencil.jpg);"></div>
					<div class="intercept-ad-graphic">
						<img src="/ui/img/ads/pencil-circle.jpg" />
					</div>
					<div class="intercept-ad-content intercept-ad-left">
						<h3>Contract Hound Header...</h3>
						<p>Your Contract Hound free trial lets you manage the schedules and renewals of any contract - without the hassle. Your free trial comes with these features:</p>
						<h4>Feature Name <small>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor.</small></h4>
						<h4>Feature Name <small>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.</small></h4>
						<h4>Feature Name <small>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do.</small></h4>
					</div>
				</div>
			</div>
		</div>

    <?php $this->load->view('layout/analytics_snippets', ['intercom' => 1, 'google' => 1]); ?>

	</body>
</html>
