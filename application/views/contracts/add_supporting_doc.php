<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Upload A Supporting Document | Contract Hound</title>

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
		<div class="modal fade" id="upload-version">
			<div class="modal-container">
				<div class="modal-dialog">
					<div class="modal-content main-upload">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close-small">Close</span></button>
							<h3 class="modal-title">Upload A Supporting Document</h3>
							<p>Supporting documents should be in a PDF or DOC format.</p>
						</div>
						<div class="modal-body modal-body-padding-bottom">
							<div class="row">
								<div class="col-sm-8" full>
									<div class="row">
										<div class="col-xs-5">
											<img src="/ui/img/instructions/upload.png" class="graphic graphic-medium" />
										</div>
										<div class="col-xs-7">
											<h4>Drag &amp; Drop</h4>
											<p class="help-block">You can upload by dragging &amp; dropping them into this window.</p>
										</div>
									</div>
								</div>
								<div class="col-sm-4">
									<h4 full>Browse</h4>
									<label id="single-add" class="btn btn-default btn-file btn-lg" data-placeholder="Choose Files"></label>
								</div>
							</div>
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
							<h1>Drop Document Here...</h1>
							<p class="text-large"><em>Documents should be in a PDF or DOC format.</em></p>
						</div>
					</div>
				</div>
			</div>
		</div>

		<script>
			$('#upload-version').modal('show');
			$('#upload-version').on('hide.bs.modal', function (e) {
				window.location.href = "/contracts/view/<?php echo $oContract->contract_id; ?>";
			});

			previewTemplate = '<div style="display:none"></div>';
			Dropzone.autoDiscover = false;
			var drop = new Dropzone("body",
				{
					url : '/contracts/upload_support_document_ajax/<?php echo $oContract->contract_id; ?>',
					previewTemplate: previewTemplate,
					maxFilesize : 15, // 15mb
					paramName : 'contract_file',
					uploadMultiple : false,
					createImageThumbnails: false,
					maxFiles : 1,
					dictResponseError : 'Unable to complete request at this time.',
					success: function(file, response){
						console.log('uploaded: '+file);
						//alert(response);
						this.removeFile(file);
					}
				}
			);
			drop.on("addedfile", function(file) {
				console.log(file);
			});
			drop.on('complete', function(file) {
				window.location.href = "/contracts/view/<?php echo $oContract->contract_id; ?>";
			});

			$('#single-add').click(function() {
				$('body').click();
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
