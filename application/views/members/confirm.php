<div class="wrapper wrapper-content">
	<div class="row">
		<div class="col-md-6 col-md-offset-3 col-sm-12">
			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<h5>Confirm Email</h5>
				</div>
				<div class="ibox-content">
					<div class="row">
						<div class="col-sm-12">
							<?php echo form_open('members/confirm/'.$iMemberId,'method="post" role="form"') ?>
								<p>To verify your account, please check your e-mail inbox that you registered to your Contract Hound account.
									You should receive an email with a confirmation token. Copy and paste the code into the information box below:</p>
								<div class="form-group"><label>Confirmation Token</label> <input name="cfmtk" type="text" placeholder="Confirmation Token" class="form-control"></div>
								<p>If you did not receive an email, try refreshing your inbox or check your SPAM folder as the confirmation message should be delivered shortly after sign-up.</p>
								<p>Please contact support (support@contracthound.com) if you did not receive the confirmation e-mail.</p>
								<div>
									<button class="btn btn-sm btn-primary pull-right m-t-n-xs" type="submit"><strong>Submit</strong></button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
