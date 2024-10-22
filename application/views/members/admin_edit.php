<div class="wrapper wrapper-content">
	<div class="row">
		<div class="col-md-6 col-md-offset-3 col-sm-12">
			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<h5>Edit Member: <?php echo $oMember->member_id; ?></h5>
				</div>
				<div class="ibox-content">
					<div class="row">
						<div class="col-sm-12">
							<?php echo form_open('members/admin_edit/'.$oMember->member_id,'method="post" role="form"') ?>
							<div class="form-group"><label>Email Address</label> <input name="email" type="text" placeholder="Email Address" class="form-control" value="<?php echud($oMember->email); ?>"></div>
							<div class="form-group"><label>New Password</label> <input name="new_password" type="password" placeholder="New Password" class="form-control"></div>
							<div class="form-group"><label>Confirm Password</label> <input name="confirm_new_password" type="password" placeholder="Confirm Password" class="form-control"></div>
							<div>
								<?php if ($oMember->status == MemberModel::StatusPending): ?>
								<a href="<?php echo site_url('members/admin_activate/'.$oMember->member_id); ?>" class="btn btn-sm btn-success m-t-n-xs">Activate</a>
								<?php endif; ?>
								<?php if ($trialMember): ?>
								<a href="#" class="btn btn-sm btn-success btn-extend-trial" member_id="<?php echo $oMember->member_id; ?>">Extend Trial</a>
								<?php endif; ?>
								<button class="btn btn-sm btn-primary pull-right m-t-n-xs" type="submit"><strong>Update</strong></button>
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
        				$(selected).remove();
        				
        				$.notify({ title: 'Success', message: 'Member free trial has been extended.' }, {type: 'success'});
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