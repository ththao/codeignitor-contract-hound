<div ng-init="teammembers = [
		<?php $bFirst = true;foreach ($aMembers as $oMember):
			if ($oMember->member_id == $iCurrentlyLoggedInParentId) {
				continue;
			} if ($bFirst) {$bFirst = false;} else { echo ',';} ?>
		{email: '<?php echo $oMember->email;
			?>', memid: '<?php echo $oMember->member_id;
			?>', status: '<?php echo $oMember->status;
			?>', fullname: '<?php $oMember->name?echud($oMember->name):echud($oMember->email);
			?>', avatar: '<?php if ($oMember->avatar): echo '/uas/'.$oMember->avatar; else: ?>/ui/img/avatars/default.png<?php endif;
			?>', contract_count: '<?php echo empty($aContractCountsPerMember[$oMember->member_id])?0:$aContractCountsPerMember[$oMember->member_id]; ?>'}
		<?php endforeach; ?>
	];"></div>

<div class="layout-panel">
	<div class="layout-panel-body">
		<div class="layout-panel-main">
			<div class="layout-section">
				<div class="divider divider-gap">
					<div class="divider-content">
						<div class="divider-title">
							<h6>Add a User</h6>
						</div>
						<div class="divider-separator">
							<hr/>
						</div>
					</div>
				</div>

				<div class="form-grid">
					<form action="/users/add_user" method="post">
					<table>
						<tr>
							<td class="form-response"><input name="add_email" type="email" class="form-control input-lg" placeholder="Add user via email..."></td>
							<td class="form-action"><input class="btn btn-primary btn-lg" type="submit" value="Add User" /></td>
						</tr>
					</table>
					</form>
				</div>

				<div class="divider divider-gap">
					<div class="divider-content">
						<div class="divider-title">
							<h6>Edit Users</h6>
						</div>
						<div class="divider-separator">
							<hr/>
						</div>
					</div>
				</div>

				<input type="text" ng-model="searchText" placeholder="Filter users..." class="form-control input-lg input-rounded" />

				<div class="members members-grid" id="searchTextResults">
					<div class="member member-editable" ng-repeat="teammember in teammembers | filter:searchText">
						<div class="member-content dropdown">
							<div class="member-graphic" data-toggle="dropdown" style="background-image: url([[teammember.avatar]])">
								<div class="avatar avatar-medium" style="background-image: url([[teammember.avatar]])">
									<img src="[[teammember.avatar]]" />
								</div>
							</div>
							<div class="member-body" data-countract-count="{{teammember.contract_count}}" data-toggle="dropdown">
								<div class="member-name">
									<h6>
										<span ng-bind="teammember.fullname"></span>
										<small ng-if="teammember.status == '0'" class="text-info">Pending...</small>
										<small ng-if="teammember.status == '3'" class="text-danger">Suspended</small>
									</h6>
								</div>
								<div class="member-meta"><span>[[teammember.email]]</span></div>
							</div>
							<a href="#" class="member-action member-action-remove" data-toggle="dropdown"><span class="caret"></span></a>
							<ul class="dropdown-menu dropdown-menu-right">
								<li><a href="/users/profile<?php if ($iCurrentlyLoggedInMemberId == $iCurrentlyLoggedInParentId): ?>_admin<?php endif; ?>/[[teammember.memid]]">View User Profile</a></li>
								<li class="divider"></li>
								<li><a href="/users/delete_user/[[teammember.memid]]" class="text-danger">Delete User</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="layout-panel-header">
		<div class="title">
			<div class="title-content">
				<div class="title-name">
					<h2>User Management</h2>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$('#user-management').modal('show');
	$('#user-management').on('hide.bs.modal', function (e) {
		window.location.href = "/welcome";
	});

	$('a[data-target="#suspend"]').click(function() {
		oMemberAvatar = $(this).parents('.member-actions').siblings('.member-graphic');
		oMemberBody = $(this).parents('.member-actions').siblings('.member-body');
		oModal = $('#suspend');

		$('.member-name h6',oModal).text($('.member-name h6',oMemberBody).text());
		$('.member-meta span',oModal).text($('.member-meta span',oMemberBody).text());
		$('.avatar',oModal).css('background-image',$('.avatar',oMemberAvatar).css('background-image'));
		$('.avatar img',oModal).prop('src',$('img',oMemberAvatar).prop('src'));
		return true;
	});
 </script>
