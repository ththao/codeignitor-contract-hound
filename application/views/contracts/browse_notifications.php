
<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<!-- application layout -->
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Browse Notifications</title>
		
		<link rel="shortcut icon" href="http://somegoodpixels.github.io/contracthound-ui/ui/img/logos/contracthound-favicon.png" />
		<meta name="viewport" content="width=device-width, maximum-scale=1.0, minimal-ui" />
		
		<script src="//code.jquery.com/jquery-1.9.1.min.js"></script>
		<script src="http://somegoodpixels.github.io/contracthound-ui/ui/jqueryui/jquery-ui-1.11.4/jquery-ui.min.js"></script>
		<script src="http://somegoodpixels.github.io/contracthound-ui/ui/modernizr/modernizr.js"></script>
		<script src="http://somegoodpixels.github.io/contracthound-ui/ui/bootstrap/js/bootstrap.min.js"></script>
		<script src="http://somegoodpixels.github.io/contracthound-ui/ui/suggest/js/bootstrap-suggest.js"></script>
		<script src="http://somegoodpixels.github.io/contracthound-ui/ui/dropzone/dropzone.js"></script>
		<script src="http://somegoodpixels.github.io/contracthound-ui/ui/tokenfield/dist/bootstrap-tokenfield.min.js"></script>
		<script src="http://somegoodpixels.github.io/contracthound-ui/ui/bootstrap-notify/bootstrap-notify.min.js"></script>
		<script src="http://somegoodpixels.github.io/contracthound-ui/ui/bootstrap-validator/dist/validator.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.5/angular.min.js"></script>
		<script src="//d2wy8f7a9ursnm.cloudfront.net/v7/bugsnag.min.js"></script>
		<script>Bugsnag.start({apiKey: '<?= $_ENV['BUGSNAG_API_KEY'] ?>', releaseStage: '<?= ENVIRONMENT ?>'});</script>
		<script src="http://somegoodpixels.github.io/contracthound-ui/ui/js/app.js"></script>

		<link rel="stylesheet" type="text/css" href="http://somegoodpixels.github.io/contracthound-ui/ui/jqueryui/jquery-ui-1.11.4/jquery-ui.structure.min.css" />
		<link rel="stylesheet" type="text/css" href="http://somegoodpixels.github.io/contracthound-ui/ui/suggest/css/bootstrap-suggest.css" />
		<link rel="stylesheet" type="text/css" href="http://somegoodpixels.github.io/contracthound-ui/ui/tokenfield/dist/css/bootstrap-tokenfield.min.css" />

		<link rel="stylesheet" type="text/css" href="http://somegoodpixels.github.io/contracthound-ui/ui/css/app.css" />

	</head>
	<body data-ng-app="ContractHoundApp">
		<div class="modal fade" id="browse-notifications">
			<div class="modal-container modal-container-large">
				<div class="modal-dialog">
					
					<div class="modal-content" ng-hide="upload_step">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close">Close</span></button>
						 	<h2 class="modal-title">Browse Notifications</h2>
						 	<p>Notifications are sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
						 
							<div class="modal-header-form">
								<input type="text" class="form-control input-rounded input-lg" placeholder="Search Notifications..." />
							</div>
							
						</div>
						<div class="modal-body">
		
							<div class="notifications">
								<div class="messages">
									<div class="message linked">
										<div class="message-content">
											<div class="message-graphic">
												<div class="avatar avatar-medium" style="background-image: url(http://somegoodpixels.github.io/contracthound-ui/ui/img/samples/avatar1.jpg)">
													<img src="http://somegoodpixels.github.io/contracthound-ui/ui/img/samples/avatar1.jpg" />
												</div>
											</div>
											<div class="message-body">
												<div class="message-header">
													<h6>John Doe <small>2/14 9:30am</small></h6>
												</div>
												<div class="message-activity">
													<p>moved Buy <a href="#">Agreement 11.2</a> from <a href="#">In Progress</a> to <a href="#">Executed</a></p>
												</div>
											</div>
										</div>
										<a class="message-link" href="#linktocontent">Entire Message Link</a>
									</div>
							
									<div class="message success linked">
										<div class="message-content">
											<div class="message-body ">
												<div class="message-header">
													<h6>Contract Hound <small>2/17 12:00am</small></h6>
												</div>
												<div class="message-activity">
													<p><a href="#">Agreement 11.2</a> is now fully approved.</p>
												</div>
											</div>
										</div>
										<a class="message-link" href="#linktocontent">Entire Message Link</a>
									</div>
							
									<div class="message linked">
										<div class="message-content">
											<div class="message-graphic">
												<div class="avatar avatar-medium avatar-icon success">
													<span data-icon="approved"></span>
												</div>
											</div>
											<div class="message-body">
												<div class="message-header">
													<h6>John Doe <small>2/14 9:30am</small></h6>
												</div>
												<div class="message-activity">
													<p>approved step 2 of 3 for <a href="#">Agreement 11.2</a>.</p>
												</div>
											</div>
										</div>
										<a class="message-link" href="#linktocontent">Entire Message Link</a>
									</div>
									<div class="message linked">
										<div class="message-content">
											<div class="message-graphic">
												<div class="avatar avatar-medium" style="background-image: url(http://somegoodpixels.github.io/contracthound-ui/ui/img/samples/avatar2.jpg)">
													<img src="http://somegoodpixels.github.io/contracthound-ui/ui/img/samples/avatar2.jpg" />
												</div>
											</div>
											<div class="message-body">
												<div class="message-header">
													<h6>Ron Dillard <small>2/16 9:30am</small></h6>
												</div>
												<div class="message-activity">
													<p>added as an editor of <a href="#">Buy Agreement 11.2</a></p>
												</div>
											</div>
										</div>
										<a class="message-link" href="#linktocontent">Entire Message Link</a>
									</div>
									<div class="message linked">
										<div class="message-content">
											<div class="message-graphic">
												<div class="avatar avatar-medium" style="background-image: url(http://somegoodpixels.github.io/contracthound-ui/ui/img/samples/avatar2.jpg)">
													<img src="http://somegoodpixels.github.io/contracthound-ui/ui/img/samples/avatar2.jpg" />
												</div>
											</div>
											<div class="message-body">
												<div class="message-header">
													<h6>Ron Dillard <small>2/16 9:30am</small></h6>
												</div>
												<div class="message-activity">
													<p>mentioned you on <a href="#">Sales Agreement, Locus Software</a></p>
												</div>
												<div class="message-comment">
													<blockquote>Hey <a href="#">@chet</a>, could you review the details on this? The section on renewal terms doesn't seem right tot me. Are we really agreeing to auto-renew for 10% more every 6 months?</blockquote>
												</div>
											</div>
										</div>
										<a class="message-link" href="#linktocontent">Entire Message Link</a>
									</div>
									<div class="message danger linked">
										<div class="message-content">
											<div class="message-body ">
												<div class="message-header">
													<h6>Contract Hound <small>2/15 12:00am</small></h6>
												</div>
												<div class="message-activity">
													<p>reminder for <a href="#">John Deere License Agreement</a></p>
												</div>
												<div class="message-comment">
													<blockquote>Call Mark Taylor at 336-908-1212 to make sure that this renewal is okay for next year. We may need to re-negotiate, in which case contact Kaye Taylor in Legal.</blockquote>
												</div>
											</div>
										</div>
										<a class="message-link" href="#linktocontent">Entire Message Link</a>
							
									</div>
									<div class="message linked">
										<div class="message-content">
											<div class="message-graphic">
												<div class="avatar avatar-medium" style="background-image: url(http://somegoodpixels.github.io/contracthound-ui/ui/img/samples/avatar1.jpg)">
													<img src="http://somegoodpixels.github.io/contracthound-ui/ui/img/samples/avatar1.jpg" />
												</div>
											</div>
											<div class="message-body">
												<div class="message-header">
													<h6>John Doe <small>2/14 9:30am</small></h6>
												</div>
												<div class="message-activity">
													<p>moved Buy <a href="#">Agreement 11.2</a> from <a href="#">In Progress</a> to <a href="#">Executed</a></p>
												</div>
											</div>
										<a class="message-link" href="#linktocontent">Entire Message Link</a>
										</div>
									</div>
									<div class="message linked">
										<div class="message-content">
											<div class="message-graphic">
												<div class="avatar avatar-medium" style="background-image: url(http://somegoodpixels.github.io/contracthound-ui/ui/img/samples/avatar2.jpg)">
													<img src="http://somegoodpixels.github.io/contracthound-ui/ui/img/samples/avatar2.jpg" />
												</div>
											</div>
											<div class="message-body">
												<div class="message-header">
													<h6>Ron Dillard <small>2/16 9:30am</small></h6>
												</div>
												<div class="message-activity">
													<p>added a comment to <a href="#">Buy Agreement 11.2</a></p>
												</div>
												<div class="message-comment">
													<blockquote>Hey <a href="#">@chet</a>, could you review the details on this? The section on renewal terms doesn't seem right tot me. Are we really agreeing to auto-renew for 10% more every 6 months?</blockquote>
												</div>
											</div>
										</div>
										<a class="message-link" href="#linktocontent">Entire Message Link</a>
									</div>
									<div class="message linked">
										<div class="message-content">
											<div class="message-graphic">
												<div class="avatar avatar-medium avatar-icon danger">
													<span data-icon="rejected"></span>
												</div>
											</div>
											<div class="message-body">
												<div class="message-header">
													<h6>John Doe <small>2/14 9:30am</small></h6>
												</div>
												<div class="message-activity">
													<p>rejected <a href="#">Agreement 11.1</a>.</p>
												</div>
											</div>
										</div>
										<a class="message-link" href="#linktocontent">Entire Message Link</a>
									</div>
								</div>
							</div>
		
		
							<div class="revelator">
								<a href="#" class="btn btn-default btn-sm">Load More</a>
							</div>
		
						</div>
					</div>
		
				</div>
			</div>
		</div>
		
		<script>
		$('#browse-notifications').modal('show')	
		</script>
		
		<?php $this->load->view('layout/analytics_snippets', ['intercom' => 1]); ?>
	</body>
</html>
