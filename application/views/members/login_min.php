<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html dir="ltr" lang="en-US">
	<!-- application layout -->
	<head>
	    <meta http-equiv="content-type" content="text/html; charset=us-ascii">
	
	    <title>Login | Contract Hound</title>
		<link rel="shortcut icon" href="/ui/img/logos/contracthound-favicon.png" />
	    <meta name="viewport" content="width=device-width, maximum-scale=1.0, minimal-ui">
      <meta name="description"
            content="Login to Contract Hound."/>

	    <script src="//code.jquery.com/jquery-1.9.1.min.js" type="text/javascript"></script>
	    <script src="/ui/jqueryui/jquery-ui-1.11.4/jquery-ui.min.js" type="text/javascript"></script>
	    <script src="/ui/modernizr/modernizr.js" type="text/javascript"></script>
	    <script src="/ui/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	    <script src="/ui/suggest/js/bootstrap-suggest.js" type="text/javascript"></script>
	    <script src="/ui/dropzone/dropzone.js" type="text/javascript"></script>
		<script src="/ui/bootstrap-notify/bootstrap-notify.min.js"></script>
	    <script src="/ui/tokenfield/dist/bootstrap-tokenfield.min.js" type="text/javascript"></script>
	    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.5/angular.min.js" type="text/javascript"></script>
		<script src="//d2wy8f7a9ursnm.cloudfront.net/v7/bugsnag.min.js"></script>
		<script>Bugsnag.start({apiKey: '<?= $_ENV['BUGSNAG_API_KEY'] ?>', releaseStage: '<?= ENVIRONMENT ?>'});</script>
	    <script src="/ui/js/app.js" type="text/javascript"></script>

	    <link rel="stylesheet" type="text/css" href="/ui/jqueryui/jquery-ui-1.11.4/jquery-ui.structure.min.css">
	    <link rel="stylesheet" type="text/css" href="/ui/suggest/css/bootstrap-suggest.css">
	    <link rel="stylesheet" type="text/css" href="/ui/tokenfield/dist/css/bootstrap-tokenfield.min.css">
	    <link rel="stylesheet" type="text/css" href="/ui/css/app.css">
	</head>
	
	<body ng-app="ContractHoundApp">
	    <div class="intercept">
	        <div class="intercept-content">
	            <div class="intercept-body">
	                <div class="intercept-frame">
	                    <div class="login">
							<div class="login-logo">
								<div class="login-logo-graphic">
									<!-- BEGIN MODULE -->
									<svg class="drawme" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewbox="0 0 128 128" enable-background="new 0 0 128 128" xml:space="preserve">
										<path fill="none" stroke="#dae3ed" stroke-width="4" stroke-miterlimit="10" d="M74.6,8.7L74.6,8.7l15.6,28.6
										c-0.6,5.5-12.4,16.2-25.9,5.7V19L74.6,8.7h15.3l4.5,6.2h13c0,0,0,5.9,0,7.5c0,10.8-17.8,3-17.8,19.2c0,5.4,3.4,12.7,5.4,17l0,0
										l12.7,3.4c0,0,5.9,1.9,6.8,9.3c2.1,16.7,1.7,13.3,3.4,22.1c-5.8,0-9.7-3-10.3-6c-1.4-7.7-1.9-12.9-1.9-12.9h-9.2h0.1
										c0.7-3.8,0.7-8.3-0.5-13.3c-0.2-0.7-0.5-1.6-1-2.8c0.5,1.2,0.9,2.1,1,2.8c1.2,5,1.2,9.4,0.5,13.3c-2,11.5-10,17.9-10,17.9l5,17.6
										c0,0,1.4,0,2.7,0c8.3,0,8.5,9.1,8.5,9.1h-3.2H88.1l-6.6-7c0,0-17.7-33.1-17.7-43c0-5.9,4.8-10.7,10.7-10.7c5.9,0,10.7,4.8,10.7,10.7
										c0,10.6-21.9,40.8-21.9,40.8h2.2c2.3,0,7.7,2.4,7.7,9.1H27c-4.3,0-16.7-4.6-16.7-14.5c0,9.9,12.5,14.5,16.7,14.5h30.8
										c0-6.8-5.4-9.1-7.7-9.1c-3.3,0-7.1,0-7.1,0s6.3-3.1,11.1-5.6c2.4-1.2,6.9-3.4,6.9-8.6c0-5.7-4.4-8.4-8.9-8.4H38.9h13.3
										c4.7,0,8.9,2.7,8.9,8.4c0,5.2-4.4,7.4-6.9,8.6c-4.9,2.4-11.1,5.6-11.1,5.6s3.7,0,7.1,0c2.3,0,7.7,2.4,7.7,9.1H32.2
										c-2.5,0-4.6-2-4.6-4.6c0-2.7,2-4.7,4.6-4.7c0,0-6.8-5.3-6.8-12.9c0-5.8,2.2-9.2,3.9-11.3c9.3-11.9,34.9-40.7,34.9-40.7v-2.3" />
									</svg>
		
								<!-- END MODULE -->
								</div>
							</div>

	                        <form class="login-form" action="/members/login" method="post">
	                            <h6>Email</h6><input name="email" type="email" class="form-control input-lg">
	
	                            <h6>Password</h6><input name="password" type="password" class="form-control input-lg">
	
	                            <div class="login-form-footer">
	                                <div class="login-form-footer-item">
	                                    <input type="submit" class="btn btn-primary btn-lg" value="Login">
	                                </div>
	                            </div>
	                        </form>
	
                            <div class="login-links">
                                <a href="/members/register">Create a new account</a> <a href="/members/request_reset_password">Reset password</a>
                            </div>

                            <div class="login-extra">
                                <p class="help-block">By logging in, you agree to the Contract Hound <a href="https://www.contracthound.com/terms/" target="_blank">Terms of Service</a>.</p>

                                <p class="help-block">&copy; Copyright <?php echo date('Y'); ?> - Flightpath Publishing, All rights reserved.</p>
                            </div>
	                    </div>
	                </div>
	            </div>
	
	            <div class="intercept-ad">
	                <div class="intercept-ad-background background-silver" style="background-image: url(/ui/img/ads/beagle.jpg);"></div>
	
	                <div class="intercept-ad-graphic"><img src="/ui/img/ads/beagle-circle.jpg"></div>
	
	                <div class="intercept-ad-content">
	                    <h1>Contract Hound</h1>
	
	                    <p>Contract Hound makes contract management easy.</p><a href="https://www.contracthound.com" class="btn btn-primary btn-lg">Learn More</a>
	                </div>
	            </div>
	        </div>
	    </div>

		<?php if ($this->session->flashdata('error')): ?>
		<script>
			var notifications = new Array(
				[{ title: 'Error: ', message: '<?php echo $this->session->flashdata('error'); ?>' },{ delay: 7000, type: 'danger' }]
			);
		</script>
		<?php endif; ?>
    <?php $this->load->view('layout/analytics_snippets', ['intercom' => 1, 'google' => 1]); ?>

	</body>
</html>
