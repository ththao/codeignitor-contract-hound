<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Add Contract Access | Contract Hound</title>

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
		<div class="modal fade" id="upload-contract">
			<div class="modal-container">
				<div class="modal-dialog">
					<form method="post" action="/contracts/update_team/<?php echo $oContract->contract_id; ?>"
					<div class="modal-content" ng-hide="upload_step">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close-small">Close</span></button>
							<h3 class="modal-title">Choose User Contract Access</h3>
							<p>Choose who can see and edit these contracts. You change this information on individual contracts later.</p>
						</div>
						<div class="modal-body">
							<p><input name="new_member" class="form-control input-lg" placeholder="Add users by their email address" id="add-users" /></p>

							<div class="members">
								<?php $oOwner = $aTeamMembers[$oContract->owner_id]; ?>
								<div class="member member-editable">
									<div class="member-content">
										<div class="member-graphic">
											<div class="avatar avatar-medium" style="background-image: url(<?php
											if ($oOwner->avatar): ?>/uas/<?php echo $oOwner->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
											?>)">
												<img src="<?php
												if ($oOwner->avatar): ?>/uas/<?php echo $oOwner->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
												?>" />
											</div>
										</div>
										<div class="member-body">
											<div class="member-name">
												<h6><?php $oOwner->name?echud($oOwner->name):echud($oOwner->email); ?></h6>
											</div>
											<div class="member-meta">
												<span>Owner</span>
											</div>
										</div>
									</div>
								</div>

								<?php foreach ($aTeamMembers as $oMember):
									if ($oMember->member_id == $oContract->owner_id || $oMember->status == MemberModel::StatusDeleted) {
										continue;
									} ?>
									<div class="member member-editable">
										<div class="member-content">
											<div class="member-graphic">
												<div class="avatar avatar-medium" style="background-image: url(<?php
												if ($oMember->avatar): ?>/uas/<?php echo $oMember->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
												?>)">
													<img src="<?php
													if ($oMember->avatar): ?>/uas/<?php echo $oMember->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
													?>" />
												</div>
											</div>
											<div class="member-body">
												<div class="member-name">
													<h6><?php $oMember->name?echud($oMember->name):echud($oMember->email); ?></h6>
												</div>
												<?php /*<div class="member-meta">
													<div class="dropdown">
														<a href="#" data-toggle="dropdown"><?php echo $oMember->level; ?> <span class="caret"></span></a>
														<ul class="dropdown-menu">
															<li><a href="#">Owner</a></li>
															<li><a href="#">Read-Only</a></li>
														</ul>
													</div>
												</div>*/ ?>
												<div class="member-meta">
													<span><?php echo $oMember->level; ?></span>
												</div>
											</div>
											<?php /*<div class="member-actions">
												<a href="#" class="member-action-remove">
													<span data-icon="close-small">Remove</span>
												</a>
											</div>*/ ?>
										</div>
									</div>
								<?php endforeach; ?>
							</div>

						</div>
						<div class="modal-footer">
							<a href="<?php /*echo '/contracts/view/'.$oContract->contract_id;*/ ?>#" class="btn btn-lg btn-text" data-dismiss="modal">Cancel</a>
							<button type="submit" name="submit" class="btn btn-lg btn-primary">Finish</button>
						</div>
					</div>
					</form>
				</div>
			</div>
		</div>

		<script>
			$('#upload-contract').modal('show');

			$(function() {
				function user_search(request, response) {
					function hasMatch(s) { return s && s.toLowerCase().indexOf(request.term.toLowerCase())!==-1; }
					var i, l, obj, matches = [];
					if ( request.term === "" ) { response([]); return; }
					for (i = 0, l = users.length; i<l; i++) {
						obj = users[i];
						if (
							hasMatch('@'+obj.value)
							|| hasMatch(obj.fullname)
							|| hasMatch(obj.email)
							|| (obj.value===null)
						) {
							matches.push(obj);
						}
					}
					response(matches);
				}

				var users = [
					<?php foreach ($oAccountMembers as $oMember):
						if ($oMember->member_id == $oOwner->member_id || isset($aTeamMembers[$oMember->member_id])) {
							continue;
						} ?>
					{value: '<?php echo $oMember->email; ?>', fullname: '<?php $oMember->last_name?echud($oMember->first_name.' '.$oMember->last_name):echud($oMember->email); ?>', email: '<?php echud($oMember->email); ?>'},
					<?php endforeach; ?>
					{value: null}
				];

				$( "#add-users" ).autocomplete({
					minLength: 0,
					source: user_search,
					focus: function( event, ui ) {
						$( "#add-users" ).val( ui.item.value );
						return false;
					},
					select: function( event, ui ) {
						$( "#add-users" ).val( ui.item.value );
						return false;
					}
				})
				.autocomplete( "instance" )
				._renderItem = function( ul, item ) {
					if ( item.value === null ) {
						return $( "<li class='ui-separator'>" )
							.append( "<a href='#' class='text-italic'>Add member via email <span class='text-light'>— </span><span class='text-dark'>"+$( "#add-users" ).val()+"</span></a>" )
							.appendTo( ul );
					} else {
						return $( "<li>" )
							.append( "<div class='value-"+item.value+"'><h6>" + item.fullname + " <small>" + item.email + "</small></h6></div>" )
							.appendTo( ul );

					}
				};
			});

			<?php if ($this->session->flashdata('error')): ?>
			var notifications = new Array(
				[{ title: 'Error:', message: '<?php echo $this->session->flashdata('error'); ?>' },{ type: 'danger' }]
			);
			<?php endif; ?>

			$('#upload-contract').on('hide.bs.modal', function (e) {
				window.location.href = "//app.contracthound.com/contracts/view/<?php echo $oContract->contract_id; ?>";
			});
		</script>

		<?php $this->load->view('layout/analytics_snippets', ['intercom' => 1, 'google' => 1]); ?>
      
	</body>
</html>
