<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Transfer Ownership | Contract Hound</title>
		
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

					<form method="post" action="/contracts/transfer/<?php echo $oContract->contract_id; ?>"
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close-small">Close</span></button>
							<h3 class="modal-title">Transfer Ownership</h3>
							<p>Select a team member to transfer ownership of this contract.</p>
						</div>
						<div class="modal-body">
							<p><input name="new_owner_email" class="form-control input-lg" placeholder="Find a team members by name, or email..." id="transfer-ownership" /></p>
							
							<div class="divider">
								<div class="divider-content">
									<div class="divider-title">
										<h6>Current Access</h6>
										<small><?php echud($oContract->name); ?></small>
									</div>
									<div class="divider-separator">
										<hr />
									</div>
									
								</div>
							</div>
							
							<div class="members">
								<?php $oOwner = $aTeamMembers[$oContract->owner_id]; ?>
								<label class="member member-option">
									<input type="radio" name="transfer" checked="" value="1" />
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
								</label>

								<?php foreach ($aTeamMembers as $oMember):
									if ($oMember->member_id == $oContract->owner_id) {
										continue;
									} ?>
								<label class="member member-option">
									<input type="radio" name="transfer" value="2" />
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
											<div class="member-meta">
												<span><?php echo $oMember->level; ?></span>
											</div>
										</div>
									</div>
								</label>
								<?php endforeach; ?>
							</div>
						</div>
						<div class="modal-footer">
							<a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="btn btn-lg btn-text" ng-dismiss="modal">Cancel</a>
							<button type="submit" name="submit" class="btn btn-lg btn-primary">Transfer</button>
						</div>
					</div>
					</form>
		
				</div>
			</div>
		</div>
		
		<script>
			$('#upload-contract').modal('show');
			$('#upload-contract').on('hide.bs.modal', function (e) {
				window.location.href = "<?php 
					if (!empty($sLastPage)): 
						echo site_url($sLastPage); 
					else:
						echo site_url('contracts/view/'.$oContract->contract_id); 
					endif; ?>";
			});
		
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
					<?php foreach ($oAccountMembers as $oMember): ?>
					{value: '<?php echo $oMember->email; ?>', fullname: '<?php $oMember->last_name?echud($oMember->first_name.' '.$oMember->last_name):echud($oMember->email); ?>', email: '<?php echud($oMember->email); ?>'},
					<?php endforeach; ?>
					{value: null}
				];
		
				$( "#transfer-ownership" ).autocomplete({
					minLength: 0,
					source: user_search,
					focus: function( event, ui ) {
						$( "#transfer-ownership" ).val( ui.item.value );
						return false;
					},
					select: function( event, ui ) {
						$( "#transfer-ownership" ).val( ui.item.value );
						return false;
					}
				})
				.autocomplete( "instance" )
					._renderItem = function( ul, item ) {
						if ( item.value === null ) {
							return $( "<li class='ui-separator'>" )
								<?php /*.append( "<a href='#' class='text-italic'>Add member via email <span class='text-light'>— </span><span class='text-dark'>"+$( "#transfer-ownership" ).val()+"</span></a>" )*/ ?>
								.append( "<a href='/users' class='text-italic'>Add the member before tranferring ownership to prevent issues.</a>" )
								.appendTo( ul );
						} else {
							return $( "<li>" )
								.append( "<div class='value-"+item.value+"'><h6>" + item.fullname + " <small>" + item.email + "</small></h6></div>" )
								.appendTo( ul );
		
						}
					};
				});
		</script>

		<?php $this->load->view('layout/analytics_snippets', ['intercom' => 1, 'google' => 1]); ?>
    
	</body>
</html>
