<div class="wrapper wrapper-content">
	<div class="row">
		<div class="col-md-offset-1 col-sm-5">
			<p class="signin-copy">Stuff goes here</p>
		</div>
		<div class="col-sm-5">
			<div class="ibox-content">
				<h3 class="m-t-none m-b">Sign In!</h3>
				<?php echo form_open('members/login','method="post" role="form"') ?>
					<div class="form-group"><label>Email</label> <input name="email" type="text" placeholder="Enter email" class="form-control"></div>
					<div class="form-group"><label>Password</label> <input name="password" type="password" placeholder="Password" class="form-control"></div>
					<div>
						<a class="btn btn-sm btn-success" href="<?php echo site_url('members/request_reset_password'); ?>" class="btn">Reset Password</a>
						<a class="btn btn-sm btn-success" href="<?php echo site_url('members/register'); ?>" class="btn">Sign Up!</a>
						<button class="btn btn-sm btn-primary pull-right" type="submit"><strong>Log in</strong></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
