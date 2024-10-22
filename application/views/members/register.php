<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<!-- application layout -->
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Register | Contract Hound</title>
		
		<link rel="shortcut icon" href="/ui/img/logos/contracthound-favicon.png" />
		<meta name="viewport" content="width=device-width, maximum-scale=1.0, minimal-ui" />
    <meta name="description"
          content="Sign up for a 14-day free trial of Contract Hound, the #1 contract management software for small businesses."/>
		
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
		<script src="/ui/country-select/js/countrySelect.js"></script>
		<script src="/ui/js/app.js"></script>

		<link rel="stylesheet" type="text/css" href="/ui/jqueryui/jquery-ui-1.11.4/jquery-ui.structure.min.css" />
		<link rel="stylesheet" type="text/css" href="/ui/suggest/css/bootstrap-suggest.css" />
		<link rel="stylesheet" type="text/css" href="/ui/tokenfield/dist/css/bootstrap-tokenfield.min.css" />
		
		<link rel="stylesheet" type="text/css" href="/ui/country-select/css/countrySelect.css">
		<link rel="stylesheet" type="text/css" href="/ui/css/app.css" />
		<script src="https://www.google.com/recaptcha/api.js?render=<?php echo $_ENV['RECAPTCHA_SITE_KEY']; ?>"></script>
        <script>
        	$(document).ready(function() {
            	$("#country").countrySelect({
            		defaultCountry: "us",
            		preferredCountries: ['us', 'au', 'gb', 'sg'],
  					responsiveDropdown: true
            	});
            	
                   // when form is submit
                $(document).on('click', '.btn-submit-signup', function(e) {
                    // we stoped it
                    e.preventDefault();
                    
                    // needs for recaptacha ready
                    grecaptcha.ready(function() {
                        // do request for recaptcha token
                        // response is promise with passed token
                        grecaptcha.execute('<?php echo $_ENV['RECAPTCHA_SITE_KEY']; ?>', {action: 'submit'}).then(function(token) {
                            // add token to form
                            $('.signup-form').prepend('<input type="hidden" name="recaptcha" value="' + token + '">');
                            $('.signup-form').submit();
                        });
                    });
            	});
            	
            	$(document).on('click', '#country', function(e) {
            		$('.selected-flag').trigger('click');
            	});
        	});
        </script>
        <style>
            .country-select { width: 100%; }
            #country { background-color: #e9eef5; cursor: pointer; }
            #country:hover { background-color: #dce1e9; }
        </style>
	</head>
	<body ng-app="ContractHoundApp">
		<div class="intercept">
			<div class="intercept-content">
				<div class="intercept-body">
					<div class="intercept-frame">
						<div class="login">
							<div class="login-header">
								<h1>Try Contract Hound Free</h1>
								<p>Nothing to install and no credit card required.</p>
								<p class="help-block">Already have an account? <a href="/members/login">Log in</a></p>
							</div>
							
							<form class="login-form signup-form" action="/members/register" method="post">
								<div class="form-grid form-grid-large">
									<table>
										<tr>
											<td class="form-label">
												<label class="text-large">Name</label>
											</td>
											<td class="form-response">
												<input type="text" class="form-control input-lg" name="first_name" placeholder="First name" />
											</td>
											<td class="form-response">
												<input type="text" class="form-control input-lg" name="last_name" placeholder="Last name" />
											</td>
										</tr>
										<tr>
											<td class="form-label">
												<label class="text-large">Email</label>
											</td>
											<td class="form-response" colspan="2">
												<input type="text" class="form-control input-lg" name="email" placeholder="jon@acme.co" <?php if (!empty($sEmail)): ?>value="<?php echud($sEmail); ?>" <?php endif; ?>/>
												<p class="help-block">Please use your business email address.</p>
											</td>
										</tr>
										<tr>
											<td class="form-label">
												<label class="text-large">Password</label>
											</td>
											<td class="form-response" colspan="2">
												<input type="password" class="form-control input-lg" name="password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" />
												<p class="help-block">Passwords should be more than 8 characters.</p>
											</td>
										</tr>
										<tr>
											<td class="form-label">
												<label class="text-large">Company</label>
											</td>
											<td class="form-response" colspan="2">
												<input type="text" class="form-control input-lg" name="company" placeholder="Acme Co." />
											</td>
										</tr>
										<tr>
											<td class="form-label">
												<label class="text-large">Country</label>
											</td>
											<td class="form-response" colspan="2">
												<input type="text" class="form-control input-lg" id="country" placeholder="Select Country" readonly />
												<input type="hidden" id="country_code" name="country_code" />
											</td>
										</tr>
									</table>
								</div>
							
								<div class="login-form-footer">
									<div class="login-form-footer-item">
										<input type="submit" class="btn btn-primary btn-lg btn-submit-signup" value="Create my Account" />
									</div>
								</div>
							</form>
		
							<div class="login-extra">
								<p class="help-block">By clicking "Create my Account", you agree to the Contract Hound <a href="https://www.contracthound.com/terms/" target="_blank">Terms of Service</a>.</p>
								<p class="help-block">&copy; Copyright <?php echo date('Y'); ?> - Flightpath Publishing, All rights reserved.</p>
							</div>
						</div>
					</div>
				</div>
				<div class="intercept-ad" style="background-color: rgb(43,63,169);">
					<div class="intercept-ad-content">
						<div class="intercept-logo">
							<img src="/ui/img/logos/contracthound-lockup-white.svg" />
						</div>
						<p style="color: #fff; font-size: 18px;">Set contract reminders with Contract Hound and you'll never lose track of a contract again.</p>
                        <p style="color: #fff; font-size: 16px; margin: 2rem 0;"><strong>“Contract Hound to us is worth in the thousands of dollars per month.”</strong><br/> - Cady Gerlach, General Counsel at Shelter House</p>
                        <p style="color: #fff; font-size: 16px; margin: 2rem 0;">“<strong>I’ve never had that fear of missing a file since switching to Contract Hound.”</strong><br/> - Vanessa Rodriguez, Executive Assistant at Bragg</p>					</div>
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
