<div class="wrapper wrapper-content">
	<div class="row">
		<div class="col-md-6 col-md-offset-3 col-sm-12">
			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<h5>Reset Password: Step 2</h5>
				</div>
				<div class="ibox-content">
					<div class="row">
						<div class="col-sm-12">
							<p>Please enter the reset code sent via email.</p>
							<?php echo form_open('members/confirm_reset_password/'.$iMemberId,'method="post" role="form"') ?>
								<div class="form-group"><label>Reset Token</label> <input name="rsptk" type="text" placeholder="Reset Token" class="form-control"></div>
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
