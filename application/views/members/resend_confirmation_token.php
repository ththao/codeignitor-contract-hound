<div class="wrapper wrapper-content">
	<div class="row">
		<div class="col-md-6 col-md-offset-3 col-sm-12">
			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<h5>Resend Confirmation</h5>
				</div>
				<div class="ibox-content">
					<div class="row">
						<div class="col-sm-12">
							<?php echo form_open('members/resend_confirmation_token','method="post" role="form"') ?>
								<div class="form-group"><label>Email</label> <input name="email" type="text" placeholder="Email" class="form-control"></div>
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
