<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<!-- application layout -->
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Profile View | Contract Hound</title>

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
		<div class="modal fade" id="profile">
			<div class="modal-container modal-container-large">
				<div class="modal-dialog">
					<div class="modal-content" ng-hide="upload_step">
						<div class="modal-header modal-header-offset">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close">Close</span></button>

							<div class="member">
								<div class="member-content">
									<div class="member-graphic">
										<div class="avatar avatar-xlarge" style="background-image: url(<?php
										if ($oMember->avatar): ?>/uas/<?php echo $oMember->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
										?>)">
											<img src="<?php
											if ($oMember->avatar): ?>/uas/<?php echo $oMember->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
											?>" />
										</div>
									</div>
									<div class="member-body">
										<div class="member-name">
											<h2><?php echud($oMember->first_name.' '.$oMember->last_name); ?></h2>
										</div>
										<div class="member-meta">
											<strong><?php echud($oMember->role); ?></strong>
										</div>
										<div class="member-meta">
											<span><?php echud($oMember->email); ?></span>
										</div>
									</div>
								</div>
							</div>

						</div>
						<div class="modal-body">
							<div class="divider divider-gap">
								<div class="divider-content">
									<div class="divider-title">
										<h6>Contracts for <?php echud($oMember->first_name.' '.$oMember->last_name); ?></h6>
										<small>(<?php echo number_format($oContracts->count); ?>)</small>
									</div>
									<div class="divider-separator">
										<hr/>
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
											<?php foreach ($oContracts as $oContract):
												$oOwner = (!empty($aOwners[$oContract->owner_id])?$aOwners[$oContract->owner_id]:null);
												if (!in_array($oContract->contract_id,$aContractIdsWithAccess)): ?>
    											<tr>
    												<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link cell-protected"><?php echud($oContract->name); ?></a></td>
    												<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link cell-protected"></a></td>
    												<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link cell-protected"></a></td>
    												<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link cell-protected"></a></td>
    												<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link cell-protected"></a></td>
    												<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link cell-protected"></a></td>
    												<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link cell-protected"></a></td>
    												<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link cell-protected"></a></td>
    											</tr>
    											<?php else: ?>
    											<tr>
    												<th><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link"><?php echud($oContract->name); ?></a></th>
    												<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link"><?php echud($oContract->company); ?></a></td>
    												<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link">$<?php echo number_format($oContract->valued); ?></a></td>
    												<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link"><?php echo ($oContract->type?tl('Buy-side'):tl('Sell-side')); ?></a></td>
    												<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link"><?php if ($oContract->start_date) { /*echo date('n/j/Y',strtotime($oContract->start_date));*/ echo convertto_local_datetime($oContract->start_date,$time_zone,'%x'); } ?></a></td>
    												<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link"><?php if ($oContract->end_date) { /*echo date('n/j/Y',strtotime($oContract->end_date)); }*/ echo convertto_local_datetime($oContract->end_date,$time_zone,'%x'); } ?></a></td>
    												<td><a href="/users/profile/<?php echo $oContract->owner_id; ?>" class="cell-link alternate"><?php echo !empty($oOwner) ? echud($oOwner->first_name.' '.$oOwner->last_name) : '&nbsp;'; ?> </a></td>
    												<td class="cell-small">
    													<a href="/users/profile/<?php echo $oContract->owner_id; ?>" class="cell-link">
        													<div class="avatar" style="background-image: url(<?php echo ($oOwner && $oOwner->avatar) ? ('/uas/' . $oOwner->avatar) : '/ui/img/avatars/default.png'; ?>)">
        														<img src="<?php echo ($oOwner && $oOwner->avatar) ? ('/uas/' . $oOwner->avatar) : '/ui/img/avatars/default.png'; ?>" />
        													</div>
    													</a>
													</td>
    											</tr>
    											<?php endif;  ?>
											<?php endforeach;  ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<script>
			$('#profile').modal('show');
			$('#profile').on('hide.bs.modal', function (e) {
				window.location.href = "/users";
			});
		</script>
    <?php $this->load->view('layout/analytics_snippets', ['intercom' => 1, 'google' => 1]); ?>

	</body>
</html>
