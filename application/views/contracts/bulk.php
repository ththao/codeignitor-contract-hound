<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<!-- application layout -->
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Upload Contract | Contract Hound</title>

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
	<body data-ng-app="ContractHoundApp">
		<div class="modal fade" id="upload-contract">
			<div class="modal-container">
				<div class="modal-dialog">

					<div class="alert alert-danger" id="uploaderrormessage" style="display:none;">
					</div>

					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close-small">Close</span></button>
							<h3 class="modal-title">Bulk Upload Contracts</h3>
							<p>You can add contracts to Contract Hound by dragging them into this window. Contracts should be in a PDF or DOC format.</p>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-sm-8" full>
									<div class="row">
										<div class="col-xs-5">
											<img src="/ui/img/instructions/upload.png" class="graphic graphic-medium" />
										</div>
										<div class="col-xs-7">
											<h4>Drag &amp; Drop</h4>
											<p class="help-block">You can add contracts to Contract Hound by dragging &amp; dropping the file into this window.</p>
										</div>
									</div>
								</div>
								<div class="col-sm-4">
									<h4 full>Browse</h4>
									<label id="single-add" class="btn btn-default btn-file btn-lg" data-placeholder="Choose Files"></label>
								</div>
							</div>
							<div class="row">
								<div class="progress" style="display:none; margin-top:50px;">
									<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
										<span class="sr-only">0% Complete</span>
									</div>
								</div>
								<div class="attachments">
									<div class="file-upload-preview-template">
										<div class="attachment attachment-editable">
											<div class="attachment-content">
												<div class="attachment-graphic">
													<span data-icon="file"></span>
												</div>
												<div class="attachment-body">
													<div class="attachment-name">
														<h6 data-dz-name> </h6>
													</div>
													<div class="attachment-extra">
														<div class="progress">
															<div class="progress-bar progress-bar-striped active" role="progressbar" data-dz-uploadprogress style="width: 0%;">
																<span class="sr-only">Uploading</span>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div id="leave-action" class="modal-footer" style="display:none;">
							<a href="/contracts" class="btn btn-primary btn-lg">Return To Contracts</a>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="dropzone">
			<div class="dropzone-container">
				<div class="dropzone-content">
					<div class="dropzone-body">
						<div class="text-center">
							<img src="/ui/img/instructions/upload-white.png" class="graphic graphic-medium" />
							<h1>Drop Contracts Here...</h1>
							<p class="text-large"><em>Contracts should be in a PDF or DOC format.</em></p>
						</div>
					</div>
				</div>
			</div>
		</div>

		<script>
			function adjustProgress(newProgress) {
				if (newProgress > 100 || newProgress < 0) { return false; }
				$('.progress').show();
				progressBar = $('.progress-bar');
				if (newProgress < 100) {
					progressBar.removeClass('progress-bar-success');
				} else if (!progressBar.hasClass('progress-bar-success')) {
					progressBar.addClass('progress-bar-success');
				}
				
				progressBar.attr('aria-valuenow',newProgress);
				progressBar.css('width',newProgress+'%');
				$('.sr-only',progressBar).text(newProgress+'% Complete');
				return true;
			}

			$(document).ready(function(){
				var scope = angular.element($('body')[0]).scope();
				scope.$apply(function() {
					scope.files = [];
					scope.filesize = 0;
					scope.filesizeexceeded = false;
					scope.filesexceeded = false;
				});
			});

			$('#upload-contract').modal('show');
			$('#upload-contract').on('hide.bs.modal', function (e) {
				window.location.href = "/contracts";
			});

			$(document).ready(function() {
				previewTemplate = $('.file-upload-preview-template').html();
				$('.file-upload-preview-template').remove();
				Dropzone.autoDiscover = false;
				var drop = new Dropzone("body",
					{
						url : '/contracts/upload_bulk_ajax',
						previewTemplate: previewTemplate,
						previewsContainer: ".attachments",
						maxFilesize : 20, // mb
						paramName : 'contract_file',
						uploadMultiple : true,
						maxFiles : <?php echo $iCountRemaining; ?>,
						dictResponseError : 'Unable to complete request at this time.',
						dictFileTooBig : 'Files must be 20 MB or less.',
						dictMaxFilesExceeded : 'You can upload up to <?php echo $iCountRemaining; ?> files unless you upgrade your subscription.',
						acceptedFiles : '.pdf,.doc,.docx,.txt',
						success: function(file, response){
							console.log('uploaded: '+file);
							//alert(response);
						}
					}
				);
				drop.on("error", function(file, error) {
					if ($('#uploaderrormessage').is(':hidden')) {
						$('#uploaderrormessage').html('File '+file.name+' could not be added.&nbsp;&nbsp;&nbsp;'+error);
						$('#uploaderrormessage').show(error);
					} else {
						currentErrorMessage = $('#uploaderrormessage').html();
						$('#uploaderrormessage').html(currentErrorMessage+'<br/>File '+file.name+' could not be added.&nbsp;&nbsp;&nbsp;'+error);
					}
					$('.progress-bar',file.previewElement).removeClass('progress-bar-success').addClass('progress-bar-danger').width('100%');
					//drop.removeFile(file);
  				});
  				//drop.on('totaluploadprogress',function(totalPercentage, totalBytesToBeSent, totalBytesSent) {
	  			//	adjustProgress(totalPercentage);
  				//});
				drop.on("addedfile", function(file) {
					console.log(file.name + ' ' + file.size);
					$('#skip-to-files').click();
  				});
				drop.on('complete', function(file) {
					$('.progress .progress-bar',file.previewElement).removeClass('progress-bar-striped')
						.removeClass('active').addClass('progress-bar-success');
					$('.progress .progress-bar .sr-only',file.previewElement).text('100% Complete');
					$('#leave-action').show();
				});
				$('#single-add').click(function() {
					$('body').click();
				});
			});
		</script>
    <?php $this->load->view('layout/analytics_snippets', ['intercom' => 1, 'google' => 1]); ?>

	</body>
</html>
