	<div class="layout-section">
		<div class="cta">
			<div class="cta-content">
				<div class="cta-actions">
					<div class="cta-action">
						<a href="/contracts/upload" class="btn btn-primary btn-lg btn-block">
							<span data-icon="contract"></span>
							<span>Upload a Contract</span>
						</a>
					</div>
					<div class="cta-action">
						<a href="/boards/add" class="btn btn-default btn-lg btn-block">
							<span data-icon="board"></span>
							<span><?php echo lang('Create a Board'); ?></span>
						</a>
					</div>
				</div>
			</div>
		</div>
		<?php if (!empty($aApprovals)): ?>
		<div class="divider divider-gap">
			<div class="divider-content">
				<div class="divider-title">
					<h6>Approvals</h6>
					<small>(<?php echo number_format(count($aApprovals)); ?>)</small>
				</div>
				<div class="divider-separator">
					<hr/>
				</div>
			</div>
		</div>

		<div class="table-responsive table-buffer">
			<table class="table table-borderless table-justified approvals">
				<thead>
					<tr>
						<th>Contract</th>
						<th class="cell-small cell-right"></th>
					</tr>
				</tead>
				<tbody>
					<?php foreach ($aApprovals as $oApproval): ?>
					<tr class="approval info">
						<th><a href="/contracts/view/<?php echo $oApproval->contract_id; ?>" class="cell-link "><?php echud($oApproval->contract->name); ?></a></th>
						<td class="cell-small">
							<div class="dropdown">
								<a href="#" class="btn btn-sm btn-info" data-toggle="dropdown">Approval <span class="caret"></span></a>
								<ul class="dropdown-menu dropdown-menu-right">
									<li class="dropdown-header">Approval Status</li>
									<li>
										<a href="/approvals/approve_step/<?php echo $oApproval->contract_approval_id; ?>">
											<p class="help-block">
												<strong class="approval-status approval-approved">
													<span class="approval-icon"><span data-icon="approved"></span></span>
													<span class="approval-value">Approve</span>
												</strong>
												Sends a notification to the members of the next approval step to approve/reject.
											</p>
										</a>
									</li>
									<li class="divider"></li>
									<li>
										<a href="/approvals/reject_step/<?php echo $oApproval->contract_approval_id; ?>">
											<p class="help-block">
												<strong class="approval-status approval-rejected">
													<span class="approval-icon"><span data-icon="rejected"></span></span>
													<span class="approval-value">Reject</span>
												</strong>
												Terminates the approval workflow and sends a notification to the contract owner.
											</p>
										</a>
									</li>
									<li class="divider"></li>
									<li><a href="/contracts/view/<?php echo $oApproval->contract_id; ?>">View Contract</a></li>
								</ul>
							</div>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php endif; ?>
		
		<?php $this->load->view('boards/boards', ['page' => 'welcome']); ?>

		<div class="divider divider-gap">
			<div class="divider-content">
				<div class="divider-title">
					<h6>Reminders</h6>
				</div>
				<div class="divider-separator">
					<hr/>
				</div>
				<?php if (count($eReminders)): ?>
				<div class="divider-actions">
					<a href="#" class="btn btn-default btn-sm show-all-reminders">Show All</a>
				</div>
				<?php endif; ?>
			</div>
		</div>

		<div class="reminders">
		<?php if (count($oReminders)): ?>
			<?php $this->load->view('welcome/reminders', ['oReminders' => $oReminders]); ?>
		<?php endif; ?>
		<?php if (count($eReminders)): ?>
			<?php $this->load->view('welcome/reminders', ['oReminders' => $eReminders]); ?>
		<?php endif; ?>
		</div>
		
		<?php if (!count($oReminders)): ?>
		<div class="empty no-reminders">
			<div class="empty-content">
				<h6>You're all caught up!</h6>
				<p class="help-block">Reminders let you know when a contract is up for renewal — or any other important action should be taken. You can create as many reminders for your contracts as needed. Start by <a href="/contracts/upload">uploading contracts</a>.</p>
			</div>
		</div>

		<div class="empty no-reminders">
			<div class="empty-content">
				<p class="help-block">Your most recently active contracts will appear here.</p>
				<a href="/contracts/upload">Add a Contract</a>
			</div>
		</div>
		<?php endif; ?>

	</div>
	<script>
		$('.tip-dismiss, .dismiss-tip-button').click(function () {
			$.ajax({
				url: '/welcome/dismiss_trial_notification'
			});
			$('.tips').hide();
			return false;
		});

		$(document).on('click', '.reminder-actions', function(e) {
			if ($(this).parents('.reminder').hasClass('expired-reminder')) {
				e.preventDefault();
				$(this).parents('.reminder').remove();
				if ($('.reminder').length == 0) {
					window.location.reload();
				}
			}
		});
		
		$(document).on('click', '.show-all-reminders', function(e) {
			e.preventDefault();

			if ($('.expired-reminder').length > 0) {
				$('.expired-reminder').removeClass('hide');
				$('.no-reminders').addClass('hide');
				$(this).removeClass('show-all-reminders').addClass('show-current-reminders').html('Show Current');
			} else {
				$(this).hide();
			}
		});

		$(document).on('click', '.show-current-reminders', function(e) {
			e.preventDefault();

			if ($('.expired-reminder').length > 0) {
    			$('.expired-reminder').addClass('hide');
    			if ($('.active-reminder').length == 0) {
    				$('.no-reminders').removeClass('hide');
    			}
    			$(this).removeClass('show-current-reminders').addClass('show-all-reminders').html('Show All');
			} else {
				$(this).hide();
			}
		});
		
		<?php if (isset($_SESSION['email_confirmed']) && $_SESSION['email_confirmed']): ?>
			<?php unset($_SESSION['email_confirmed']); ?>
			
            if (typeof dataLayer === 'undefined') {
            	dataLayer = [{'event':'email_confirmed'}];
            } else {
            	dataLayer.push({'event':'email_confirmed'});
            }
		<?php endif; ?>
	</script>
