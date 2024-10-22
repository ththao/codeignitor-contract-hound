<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Browse Contracts | Contract Hound</title>

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
		<div class="modal fade" id="browse-contracts">
			<div class="modal-container modal-container-large">
				<div class="modal-dialog">

					<div class="modal-content" ng-hide="upload_step">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close">Close</span></button>
							<div class="modal-header-actions">
								<a full href="/contracts/upload" class="btn btn-primary btn-lg"><span data-icon="add-small"></span> <span>Add Contracts<span></a>
								<a mobile href="/contracts/upload" class="btn btn-primary btn-lg"><span data-icon="add"></span></a>
							</div>
							<h2 class="modal-title">Browse Contracts</h2>
							<p>These are all the contracts you manage in Contract Hound.</p>
							<div class="modal-header-form">
								<input type="text" class="form-control input-rounded input-lg" id="search_phrase" placeholder="Search Contracts..." />
							</div>
						</div>
						<div class="modal-body">
							<div class="divider">
								<div class="divider-content">
									<div class="divider-title">
										<h6>Contracts</h6>
										<small>(<?php echo number_format($oContracts->count); ?>)</small>
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

							<div style="width: 100%;">
								<div class="table-responsive table-alignment">
									<table class="table table-hover table-borderless table-justified">
										<thead>
											<tr>
												<th>Contract</th>
												<th>Vendor</th>
												<th>Amount</th>
												<th>Type</th>
												<th>Start</th>
												<th>End</th>
												<th>Owner</th>
												<th class="cell-small"></th>
											</tr>
										</thead>
										<tbody>
										<?php foreach ($oContracts as $oContract): ?>
											<tr>
												<th><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link"><?php echud($oContract->name); ?></a></th>
												<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link"><?php echud($oContract->company); ?></a></td>
												<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link"><?php if ($oContract->valued) { echo $sCurrency.number_format($oContract->valued); } ?></a></td>
												<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link"><?php echo $oContract->type?'Buy-side':'Sell-side'; ?></a></td>
												<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link"><?php if ($oContract->start_date) { /*echo date('n/j/Y',strtotime($oContract->start_date));*/ echo convertto_local_datetime($oContract->start_date,$time_zone,'%x');} ?></a></td>
												<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link"><?php if ($oContract->end_date) { /*echo date('n/j/Y',strtotime($oContract->end_date)); */ echo convertto_local_datetime($oContract->end_date,$time_zone,'%x'); } ?></a></td>
												<?php if (!empty($aOwners[$oContract->owner_id])):
												$oOwner = $aOwners[$oContract->owner_id]; ?>
												<td><a href="/members/profile/<?php echo $oContract->owner_id;
													?>" class="cell-link alternate"><?php if ($oOwner->name) { echud($oOwner->name); } else { echud($oOwner->email); }
													?></a></td>
												<td class="cell-small"><a href="/members/profile/<?php echo $oContract->owner_id;
													?>" class="cell-link"><div class="avatar" style="background-image: url(<?php
													if ($oOwner->avatar): ?>/uas/<?php echo $oOwner->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
													?>)"><img src="<?php if ($oOwner->avatar): ?>/uas/<?php echo $oOwner->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
													?>" /></div></a></td>
												<?php else: ?>
												<td colspan="2">&nbsp;</td>
												<?php endif; ?>
											</tr>
										</tbody>
										<?php endforeach; ?>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<script>
			$('#browse-contracts').modal('show')
			$('#browse-contracts').on('hide.bs.modal', function (e) {
				window.location.href = "<?php echo site_url('welcome'); ?>";
			});

			<?php if ($this->session->flashdata('success')): ?>
			var notifications = new Array(
				[{ title: 'Success: ', message: '<?php echo $this->session->flashdata('success'); ?>' },{ delay: 7000, type: 'success' }]
			);
			<?php elseif ($this->session->flashdata('error')): ?>
			var notifications = new Array(
				[{ title: 'Error: ', message: '<?php echo $this->session->flashdata('error'); ?>' },{ delay: 7000, type: 'danger' }]
			);
			<?php elseif ($this->session->flashdata('warning')): ?>
			var notifications = new Array(
				[{ title: 'Warning: ', message: '<?php echo $this->session->flashdata('warning'); ?>' },{ delay: 7000, type: 'warning' }]
			);
			<?php endif; ?>
		</script>

		<?php $this->load->view('layout/analytics_snippets', ['intercom' => 1, 'google' => 1]); ?>

	</body>
</html>
