<div class="layout-panel">
	<div class="layout-panel-body">
		<div class="layout-panel-main">
			<div class="layout-section">
				<form method="post" action="/members/contact_preferences">
					<div class="divider divider-gap">
						<div class="divider-content">
							<div class="divider-title">
								<small>Emails</small>
							</div>
							<div class="divider-separator">
								<hr/>
							</div>
						</div>
					</div>
					<h4>I would like to receive notifications by email...</h4>
					<div class="form-inline">
						<div class="form-group">
							<label class="option">
								<input type="radio" name="email-notifications"<?php if ($oMember->notification_frequency == 0): ?> checked<?php endif; ?> value="0" />
								<i class="option-icon"></i>
								Immediately
							</label>
						</div>
						<div class="form-group">
							<label class="option">
								<input type="radio" name="email-notifications"<?php if ($oMember->notification_frequency == 1): ?> checked<?php endif; ?> value="1" />
								<i class="option-icon"></i>
								Once per day
							</label>
						</div>
						<div class="form-group">
							<label class="option">
								<input type="radio" name="email-notifications"<?php if ($oMember->notification_frequency == 2): ?> checked<?php endif; ?> value="2" />
								<i class="option-icon"></i>
								Never
							</label>
						</div>
					</div>
					<p class="help-block help-block-margins">We will still email you about reminders
						you've set or issues with your account.</p>

					<div class="divider divider-gap">
						<div class="divider-content">
							<div class="divider-title">
								<small>Notifications</small>
							</div>
							<div class="divider-separator">
								<hr/>
							</div>
						</div>
					</div>

					<h4>Notify me when...</h4>
					<label class="option">
						<input name="notify_contract_changes" type="checkbox" value="1"<?php if ($oMember->notify_contract_changes): ?> checked<?php endif; ?> />
						<i class="option-icon"></i>
						Someone changes the details of one of my contracts
					</label>
					<label class="option">
						<input name="notify_add_comment" type="checkbox" value="1"<?php if ($oMember->notify_add_comment): ?> checked<?php endif; ?> />
						<i class="option-icon"></i>
						New comments are added to one of my contracts
					</label>
					<label class="option">
						<input name="notify_board_change" type="checkbox" value="1"<?php if ($oMember->notify_board_change): ?> checked<?php endif; ?> />
						<i class="option-icon"></i>
						Boards are created or deleted
					</label>
					<label class="option">
						<input name="notify_contract_status_change" type="checkbox" value="1"<?php if ($oMember->notify_contract_status_change): ?> checked<?php endif; ?> />
						<i class="option-icon"></i>
						Contracts are created, archived, or deleted
					</label>
					<label class="option">
						<input name="notify_contract_ending" type="checkbox" value="1"<?php if ($oMember->notify_contract_ending): ?> checked<?php endif; ?> />
						<i class="option-icon"></i>
						Contracts are nearing their end date
					</label>
					<p class="help-block help-block-margins">Remember that you can mute notifications for a single contract on the contract's detail page.</p>
					<div>
						<a href="/welcome" class="btn btn-text btn-lg">Cancel</a>
						<button type="submit" class="btn btn-primary btn-lg">Save Changes</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="layout-panel-header">
		<div class="title">
			<div class="title-content">
				<div class="title-name">
					<h3>Contact Preferences</h3>
				</div>
			</div>
		</div>
	</div>
</div>