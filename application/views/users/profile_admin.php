<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<!-- application layout -->
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Profile Admin View | Contract Hound</title>

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
					<div class="modal-content">
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

							<h4>Contracts for <?php echud($oMember->first_name.' '.$oMember->last_name); ?></h4>
							<div class="divider divider-gap">
								<div class="divider-content">
									<div class="divider-title">
										<h6>Owner</h6>
										<small>(<?php echo number_format($iCountOwner); ?>)</small>
									</div>
									<div class="divider-separator">
										<hr/>
									</div>
								</div>
							</div>
							<div class="table-responsive table-alignment">
								<table class="table table-hover table-borderless table-justified">
									<tbody>
										<?php foreach ($oContracts as $oContract):
											if ($oContract->owner_id != $oMember->member_id) {
												continue;
											}

											$sExtraCSS = '';;
											$oOwner = (!empty($aOwners[$oContract->owner_id])?$aOwners[$oContract->owner_id]:null);
											if (!in_array($oContract->contract_id,$aContractIdsWithAccess)) { $sExtraCSS = ' cell-protected'; } ?>
										<tr>
											<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link<?php echo $sExtraCSS; ?>"><?php echud($oContract->name); ?></a></td>
											<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link<?php echo $sExtraCSS; ?>"><?php echud($oContract->company); ?></a></td>
											<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link<?php echo $sExtraCSS; ?>">$<?php echo number_format($oContract->valued); ?></a></td>
											<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link<?php echo $sExtraCSS; ?>"><?php echo ($oContract->type?tl('Buy-side'):tl('Sell-side')); ?></a></td>
											<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link<?php echo $sExtraCSS; ?>"><?php if ($oContract->start_date) { /*echo date('n/j/Y',strtotime($oContract->start_date));*/ echo convertto_local_datetime($oContract->start_date,$time_zone,'%x'); } ?></a></td>
											<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link<?php echo $sExtraCSS; ?>"><?php if ($oContract->end_date) { /*echo date('n/j/Y',strtotime($oContract->end_date));*/ } echo convertto_local_datetime($oContract->end_date,$time_zone,'%x'); ?></a></td>
										</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>

							<div class="divider divider-gap">
								<div class="divider-content">
									<div class="divider-title">
										<h6>Editor</h6>
										<small>(<?php echo number_format($iCountEditor); ?>)</small>
									</div>
									<div class="divider-separator">
										<hr/>
									</div>
								</div>
							</div>
							<div class="table-responsive table-alignment">
								<table class="table table-hover table-borderless table-justified">
									<tbody>
										<?php foreach ($oContracts as $oContract):
											if (is_null($oContract->level) || $oContract->level != ContractMemberModel::LEVEL_EDITOR) {
												continue;
											}

											$sExtraCSS = '';;
											$oOwner = (!empty($aOwners[$oContract->owner_id])?$aOwners[$oContract->owner_id]:null);
											if (!in_array($oContract->contract_id,$aContractIdsWithAccess)) { $sExtraCSS = ' cell-protected'; } ?>
										<tr data-level="<?php print_r($oContract->level); ?>">
											<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link<?php echo $sExtraCSS; ?>"><?php echud($oContract->name); ?></a></td>
											<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link<?php echo $sExtraCSS; ?>"><?php echud($oContract->company); ?></a></td>
											<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link<?php echo $sExtraCSS; ?>">$<?php echo number_format($oContract->valued); ?></a></td>
											<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link<?php echo $sExtraCSS; ?>"><?php echo ($oContract->type?tl('Buy-side'):tl('Sell-side')); ?></a></td>
											<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link<?php echo $sExtraCSS; ?>"><?php if ($oContract->start_date) { /*echo date('n/j/Y',strtotime($oContract->start_date));*/echo convertto_local_datetime($oContract->start_date,$time_zone,'%x'); } ?></a></td>
											<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link<?php echo $sExtraCSS; ?>"><?php if ($oContract->end_date) { /*echo date('n/j/Y',strtotime($oContract->end_date));*/echo convertto_local_datetime($oContract->end_date,$time_zone,'%x'); } ?></a></td>
											<td>
												<div class="dropdown">
													<a href="#" class="btn btn-sm btn-default" data-toggle="dropdown">
														Editor
														<span class="caret">
													</a>
													<ul class="dropdown-menu dropdown-menu-right">
														<li>
															<a href="#">
																<strong>Editor</strong>
																<p class="help-block help-block-inline">Editors can change details and upload new versions.</p>
															</a>
														</li>
														<li><a href="/contracts/transfer_to/<?php echo $oContract->contract_id.'/'.$oMember->member_id; ?>"><strong>Make Owner</strong></a></li>
														<li><a href="/contracts/change_access/<?php echo $oContract->contract_id.'/'.$oMember->member_id; ?>/1"><strong>Make Read-Only</strong></a></li>
														<li class="divider"></li>
														<li><a href="/contracts/remove_access/<?php echo $oContract->contract_id.'/'.$oMember->member_id; ?>"><span class="text-danger">Remove Access</span></a></li>
													</ul>
												</div>
											</td>
										</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>

							<div class="divider divider-gap">
								<div class="divider-content">
									<div class="divider-title">
										<h6>Read-Only</h6>
										<small>(<?php echo number_format($iCountReadOnly); ?>)</small>
									</div>
									<div class="divider-separator">
										<hr/>
									</div>
								</div>
							</div>
							<div class="table-responsive table-alignment">
								<table class="table table-hover table-borderless table-justified">
									<tbody>
										<?php foreach ($oContracts as $oContract):
											if (is_null($oContract->level) || $oContract->level != ContractMemberModel::LEVEL_VIEW_ONLY) {
												continue;
											}

											$sExtraCSS = '';
											$oOwner = (!empty($aOwners[$oContract->owner_id])?$aOwners[$oContract->owner_id]:null);
											if (!in_array($oContract->contract_id,$aContractIdsWithAccess)) { $sExtraCSS = ' cell-protected'; } ?>
										<tr>
											<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link<?php echo $sExtraCSS; ?>"><?php echud($oContract->name); ?></a></td>
											<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link<?php echo $sExtraCSS; ?>"><?php echud($oContract->company); ?></a></td>
											<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link<?php echo $sExtraCSS; ?>">$<?php echo number_format($oContract->valued); ?></a></td>
											<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link<?php echo $sExtraCSS; ?>"><?php echo ($oContract->type?tl('Buy-side'):tl('Sell-side')); ?></a></td>
											<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link<?php echo $sExtraCSS; ?>"><?php if ($oContract->start_date) { /*echo date('n/j/Y',strtotime($oContract->start_date));*/echo convertto_local_datetime($oContract->start_date,$time_zone,'%x'); } ?></a></td>
											<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link<?php echo $sExtraCSS; ?>"><?php if ($oContract->end_date) { /*echo date('n/j/Y',strtotime($oContract->end_date));*/echo convertto_local_datetime($oContract->end_date,$time_zone,'%x'); } ?></a></td>
											<td>
												<div class="dropdown">
													<a href="#" class="btn btn-sm btn-default" data-toggle="dropdown">
														Read-Only
														<span class="caret">
													</a>
													<ul class="dropdown-menu dropdown-menu-right">
														<li>
															<a href="#">
																<strong>Read-Only</strong>
																<p class="help-block help-block-inline">Read-Only users can only view details, notifications, and activity of a contract.</p>
															</a>
														</li>
														<li><a href="/contracts/transfer_to/<?php echo $oContract->contract_id.'/'.$oMember->member_id; ?>"><strong>Make Owner</strong></a></li>
														<li><a href="/contracts/change_access/<?php echo $oContract->contract_id.'/'.$oMember->member_id; ?>/0"><strong>Make Editor</strong></a></li>
														<li class="divider"></li>
														<li><a href="/contracts/remove_access/<?php echo $oContract->contract_id.'/'.$oMember->member_id; ?>"><span class="text-danger">Remove Access</span></a></li>
													</ul>
												</div>
											</td>
										</tr>
										<?php endforeach;  ?>
									</tbody>
								</table>
							</div>

							<?php if (!empty($oAccessLogs) && $oAccessLogs->count): ?>
							<div class="divider divider-gap">
								<div class="divider-content">
									<div class="divider-title">
										<h6>Login Activity</h6>
										<small>(last <?php echo $oAccessLogs->count; ?>)</small>
									</div>
									<div class="divider-separator">
										<hr/>
									</div>
								</div>
							</div>
							<div class="table-responsive table-alignment col-md-4 col-sm-12">
								<table class="table table-hover table-borderless table-justified">
									<thead>
										<tr>
											<th>Date</th>
											<th>First Name</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($oAccessLogs as $oAccessLog): ?>
										<tr>
											<th scope="row"><?php //echo date('n/j g:ia',strtotime($oAccessLog->create_date)); ?><?php echo convertto_local_datetime($oAccessLog->create_date,$time_zone,'%x %X'); ?>
											</th>
											<td><?php echo $oAccessLog->getReadableActionType(); ?></td>
										</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
							<?php endif; ?>
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
