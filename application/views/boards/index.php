<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Browse Boards | Contract Hound</title>

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
		<div class="modal fade" id="browse-boards">
			<div class="modal-container modal-container-large">
				<div class="modal-dialog">
					<div class="modal-content" ng-hide="upload_step">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close">Close</span></button>
							<div class="modal-header-actions">
								<a full href="/boards/add" class="btn btn-primary btn-lg"><span data-icon="add-small"></span> <span>Add Board</span></a>
								<a mobile href="/boards/add" class="btn btn-primary btn-lg"><span data-icon="add"></span></a>
							</div>
							<h2 class="modal-title">Browse Boards</h2>
							<p>Boards help you organize your contracts. These are all the boards managed in Contract Hound.</p>
							<div class="modal-header-form">
								<input type="text" class="form-control input-rounded input-lg" placeholder="Search Boards..." />
							</div>
						</div>

						<div class="modal-body">
							<div class="divider">
								<div class="divider-content">
									<div class="divider-title">
										<h6>Boards</h6>
										<small>(<?php echo number_format($oBoards->count); ?>)</small>
									</div>
									<div class="divider-separator">
										<hr/>
									</div>
									<div class="divider-actions">
										<div class="dropdown">
											<a href="#" class="text-light text-italic" data-toggle="dropdown">
												Sort by
												<span class="caret"></span>
											</a>
											<ul class="dropdown-menu dropdown-menu-right">
												<li><a href="#">Variable 1...</a></li>
												<li><a href="#">Variable 2...</a></li>
											</ul>
										</div>
									</div>
								</div>
							</div>

							<div class="boards boards-grid">
								<?php foreach ($oBoards as $oBoard): ?>
								<a href="/boards/view/<?php echo $oBoard->board_id; ?>" class="board board-editable">
									<div class="board-content">
										<div class="board-graphic">
											<span data-icon="board"></span>
										</div>
										<div class="board-body">
											<div class="board-name">
												<h6><?php echud($oBoard->name); ?></h6>
											</div>
											<div class="board-meta">
												<span><?php foreach ($aBoardContractCounts as $aBCC) {
														if ($aBCC['board_id'] == $oBoard->board_id) {
															if ($aBCC['board_count'] != 1) {
																echo number_format($aBCC['board_count']).' Contracts';
															} else {
																echo '0 Contracts';
															}
														} else {
															echo '0 Contracts';
														}
												} ?></span>
											</div>
										</div>
									</div>
								</a>
								<?php endforeach; ?>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>

		<script>
			$('#browse-boards').modal('show');
			$('#browse-boards').on('hide.bs.modal', function (e) {
				window.location.href = "<?php echo site_url('welcome'); ?>";
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
