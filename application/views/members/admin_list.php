<div class="row">
	<div class="col-sm-12">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Members</h5>
			</div>
			<div class="ibox-content">
				<p>s=<a title="Email Asc" href="<?php echo site_url('members/admin_list?s=e');
					?>">e</a>,<a title="Email Asc" href="<?php echo site_url('members/admin_list?s=ea');
					?>">ea</a>,<a title="Email Desc" href="<?php echo site_url('members/admin_list?s=ed');
					?>">ed</a>,<a title="Member Id Asc" href="<?php echo site_url('members/admin_list?s=m');
					?>">m</a>,<a title="Member Id Asc" href="<?php echo site_url('members/admin_list?s=ma');
					?>">ma</a>,<a title="Member Id Desc" href="<?php echo site_url('members/admin_list?s=md');
					?>">md</a>,<a title="Status Asc" href="<?php echo site_url('members/admin_list?s=s');
					?>">s</a>,<a title="Status Asc" href="<?php echo site_url('members/admin_list?s=sa');
					?>">sa</a>,<a title="Status Desc" href="<?php echo site_url('members/admin_list?s=sd');
					?>">sd</a></p>
				<table class="table table-hover">
					<thead>
					<tr>
						<th>Id</th>
						<th>Member</th>
						<th>Type</th>
						<th>Status</th>
						<th>Plan</th>
						<th style="width: 140px;">Actions</th>
					</tr>
					</thead>
					<tbody>
					<?php if (count($oMembers)):
						foreach ($oMembers as $oMember): ?>
							<tr>
								<th><?php echo number_format($oMember->member_id); ?></th>
								<td class="email"><?php echud($oMember->email); if (in_array($oMember->member_id,$aAdminIds)): ?> (Admin)<?php endif; if ($oMember->status == MemberModel::StatusDeleted): ?> (Deleted)<?php endif; ?></td>
								<td><?php if ($oMember->member_id == $oMember->parent_id) { echo "Parent"; } else { echo "Sub"; } ?></td>
								<td>
									<?php echo $oMember->readable_status; ?>
									<?php if ($oMember->status == MemberModel::StatusPending): ?>
										<a href="<?php echo site_url('members/admin_activate/'.$oMember->member_id); ?>">Activate</a>
										<a href="<?php echo site_url('members/admin_resend_confirmation/'.$oMember->member_id); ?>">Resend</a>
									<?php endif; ?>
								</td>
								<td>
									<?php if ($oMember->subscription): ?>
									<?php $subscription = $oMember->subscription; ?>
									<?php echo $subscription->translated_status; ?>
									<?php if ($subscription->status == SubscriptionModel::StatusTrial && $subscription->expire_date < date('Y-m-d H:i:s')): ?>
										Expired <a href="#" class="btn-extend-trial" member_id="<?php echo $oMember->member_id; ?>">Extend Trial</a>
									<?php endif; ?>
									<?php if ($subscription->status == SubscriptionModel::StatusExpired): ?>
										<a href="#" class="btn-extend-trial" member_id="<?php echo $oMember->member_id; ?>">Extend Trial</a>
									<?php endif; ?>
									<?php endif; ?>
								</td>
								<td style="width: 140px;">
									<a title="Login As" href="<?php echo site_url('members/admin_login_as/'.$oMember->member_id); ?>">LA</a>
									<a title="Edit" href="<?php echo site_url('members/admin_edit/'.$oMember->member_id); ?>">E</a>
									<?php if (!in_array($oMember->member_id,$aAdminIds) && $oMember->status != MemberModel::StatusDeleted): ?>
										<a class="confirm-delete" title="Delete" href="<?php echo site_url('members/admin_delete/'.$oMember->member_id); ?>">D</a>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach;
					endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<div class="wrapper wrapper-content">
	<div class="row">
		<div class="col-md-6 col-md-offset-3 col-sm-12 col-lg-4 col-lg-offset-4">
			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<h5>Add New Member</h5>
				</div>
				<div class="ibox-content">
					<div class="row">
						<div class="col-sm-12">
							<?php echo form_open('members/admin_add','method="post" role="form"') ?>
							<div class="form-group"><label>Email Address</label> <input name="email" type="text" placeholder="Email Address" class="form-control"></div>
							<div class="form-group"><label>Password</label> <input name="password" type="password" placeholder="Password" class="form-control"></div>
							<div>
								<button class="btn btn-sm btn-primary pull-right m-t-n-xs" type="submit"><strong>Submit</strong></button>
							</div>
							<?php echo form_close(); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
	
            			
		$(document).on('click', '.btn-extend-trial', function(e) {
			e.preventDefault();
			
			var selected = $(this);
            if ($(selected).hasClass('disabled')) {
            	return false;
            }
            var caption = $(selected).html();

            $.ajax({
	            type: "POST",
	            url: "/members/extend_trial",
	            dataType: 'json',
	            data: {
	                member_id: $(selected).attr('member_id')
	            },
	            beforeSend: function() {
	            	$(selected).addClass('disabled').html('<img src="/ui/img/ajax-loading.gif"/>');
	            },
	            success: function(data) {
	            	if (data.status == 1) {
        				$(selected).parents('td').html('Free Trial');
        			} else {
            			$.notify({ title: 'Error', message: 'There was an error while trying to extend free trial. Please try again.'}, {type: 'danger'});
        			}
	            },
	            complete: function() {
	            	$(selected).removeClass('disabled').html(caption);
	            }
	        });
		});
	});
</script>