<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<!-- application layout -->
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Upgrade | Contract Hound</title>
		
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
								<h2 class="uhcwa">Upgrade your Account Today!</h2>
								<p>Your account will now have <span id="contract_limit_label">50</span> contracts for 
									$<span id="total_price">95</span> per month. <br/>Change your subscription at any time.</p>
							</div>
							
							<form class="login-form" method="post" action="/billing/update_subscription">
    							<div class="form-grid form-grid-large">
    								<table>
    									<tr>
    										<td class="form-label" colspan="2">
    											<label class="text-large">How many contracts do you manage?</label>
    										</td>
    									</tr>
    									<tr>
    										<td class="form-response">
    											<select id="plan_id" name="plan_id" class="form-control input-lg">
    												<?php foreach ($oPrices as $plan_id => $price): ?>
    													<?php $selected = ((!$oSub->plan_id && $plan_id == 1) || ($oSub->plan_id + 1 == $plan_id)) ? 'selected' : ''; ?>
    													<option <?php echo $selected; ?> value="<?php echo $price['label']; ?>" price_id="<?php echo $price['stripe_plan_id']; ?>"><?php echo $price['label']; ?> Contracts</option>
    												<?php endforeach; ?>
    												<option <?php echo ($oSub->plan_id > 9) ? 'selected' : ''; ?>value="0">500+ Contracts</option>
    											</select>
    										</td>
    										<td class="form-response form-compact">
    											<label id="non-enterprise-text" class="text-large">$<span id="total">95</span><em class="text-green text-normal">/mo</em></label>
    											<label id="enterprise-text" style="display:none;" class="text-large">Let's talk!</label>
    										</td>
    									</tr>
    								</table>
    							</div>
    							<p id="enterprise-text-long" class="custom-extra text-large text-light text-italic" style="display:none;">
    								Contact us at <a href="mailto:sales@contracthound.com">sales@contracthound.com</a> and let us know how many contracts you need to manage. We'll follow up shortly with a custom quote.
    							</p>
    	
    							<div class="login-form-footer">
    								<div class="login-form-footer-item">
    									<button type="submit" class="btn btn-primary btn-lg <?php echo !$oSub->plan_id ? 'btn-checkout' : ''; ?>">
    									<?php echo !$oSub->plan_id ? 'Continue to Billing Details' : 'Confirm Upgrade'; ?>
    									</button>
    									<a href="/billing" class="btn btn-lg btn-text">Return To Billing</a>
    								</div>
    							</div>
							</form>
							<div class="login-extra">
								<p class="help-block">&copy; Copyright <?php echo date('Y'); ?> - Flightpath Publishing, All rights reserved.</p>
							</div>
						</div>
					</div>
				</div>
				<div class="intercept-ad">
					<div class="intercept-ad-background background-silver" style="background-image: url(/ui/img/ads/screenshot.jpg);"></div>
					<div class="intercept-ad-graphic">
						<img src="/ui/img/ads/screenshot-circle.jpg" />
					</div>
					<div class="intercept-ad-content">
						<div class="intercept-logo">
							<img src="/ui/img/logos/contracthound-lockup-dark.svg" />
						</div>
						
						<p class="text-larger">Easily store, organize, &amp; configure alerts for your company&apos;s contracts</p>
					</div>
				</div>
			</div>
		</div>
		
    	<script src="https://js.stripe.com/v3/"></script>
		<script type="text/javascript">
			function switchPrice() {
				planValue = $('#plan_id').val();
				if (planValue == 0) {
					$('.login-header p').hide();
				} else {
					$('.login-header p').show();
				}
				planPrice = planValue/50*95;
				$('#non-enterprise-text span').text(planPrice);
				contractLimit = $('#plan_id :selected').val();

				if (planValue == 0) {
					$('#non-enterprise-text').hide();
					$('#enterprise-text').show();
					$('#enterprise-text-long').show();
					$('input[type=submit]').hide();
				} else {
					$('#total_price').text(planPrice);
					$('#contract_limit_label').text(contractLimit);
					$('#enterprise-text').hide();
					$('#enterprise-text-long').hide();
					$('#non-enterprise-text').show();
					$('input[type=submit]').show();
				}
			};
		
			var handleResult = function(result) {
				if (result.error) {
					var notifications = new Array(
        				[{ title: 'Error', message: result.error.message },{ type: 'danger' }]
        			);
				}
			};

			$(document).ready(function(){
				switchPrice();
				
				$('#plan_id').on('change', function() {
					switchPrice()
				});
        		
                // Setup event handler to create a Checkout Session when button is clicked
                $('.btn-checkout').on("click", function(e) {
                	e.preventDefault();
                	
                	var selected = $(this);
                    if ($(selected).hasClass('disabled')) {
                    	return false;
                    }
                    var caption = $(selected).html();

                    $.ajax({
        	            type: "POST",
        	            url: "/billing/create_checkout_session",
        	            dataType: 'json',
        	            data: {
        	                priceId: $('#plan_id :selected').attr('price_id')
        	            },
        	            beforeSend: function() {
        	            	$(selected).addClass('disabled').html('<img src="/ui/img/ajax-loading.gif"/>');
        	            },
        	            success: function(data) {
                			var stripe = Stripe("<?php echo $_ENV['STRIPE_PUBLIC_KEY']; ?>");
        	                stripe.redirectToCheckout({sessionId: data.sessionId}).then(handleResult);
        	            },
        	            complete: function() {
        	            	$(selected).removeClass('disabled').html(caption);
        	            }
        	        });
    			});
			});

			<?php if ($this->session->flashdata('error')): ?>
			var notifications = new Array(
				[{ title: 'Error', message: '<?php echo $this->session->flashdata('error'); ?>' },{ type: 'danger' }]
			);
			<?php endif; ?>

		</script>
		
		<?php $this->load->view('layout/analytics_snippets', ['intercom' => 1, 'google' => 1]); ?>

	</body>
</html>