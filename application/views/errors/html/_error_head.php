<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="google-site-verification" content="9rlgDzILXRHw8hTN1rbJoxJsErtT28eNZ8oXbTMld6Y" />
		<?php /*<link rel="shortcut icon" href="http://app.seoalarms.com/favicon.ico" type="image/x-icon">*/ ?>
		<title>Contract Hound | <?php echo $sError; ?></title>
		<link href="/assets/css/bootstrap.min.css" rel="stylesheet">
		<link href="/assets/font-awesome/css/font-awesome.css" rel="stylesheet">
		<link href="/assets/css/animate.css" rel="stylesheet">
		<link href="/assets/css/style.css" rel="stylesheet">
		<script>
			var BASE_URL = 'https://www.contracthound.com/';
		</script>
	</head>
	<body>
		<div id="wrapper">
			<nav class="navbar-default navbar-static-side" role="navigation">
				<div class="sidebar-collapse">
					<ul class="nav" id="side-menu">
						<li class="nav-header">
							<div class="dropdown profile-element">
								<span class="block m-t-xs page-title"> <strong class="font-bold">Contract Hound</strong></span>
							</div>
						</li>
						<li><a href="/members/settings"><i class="fa fa-bars"></i> <span class="nav-label">Settings</span></a></li>
					</ul>
				</div>
			</nav>
			<div id="page-wrapper" class="gray-bg">
				<div class="row border-bottom">
					<nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
						<ul class="nav navbar-top-links navbar-right">
							<li>
								<span class="m-r-sm text-muted welcome-message">Welcome to Contract Hound</span>
							</li>
							<li>
								<a href="http://serverhealthhub.zendesk.com" target="_blank">
									<i class="fa fa-life-ring"></i> Support
								</a>
							</li>
							<?php if (empty($_SESSION['member_id'])): ?>
							<li>
								<a href="/members/login">
									<i class="fa fa-sign-in"></i> Sign in
								</a>
							</li>
							<li>
								<a href="/members/register">
									<i class="fa fa-users"></i> Sign Up
								</a>
							</li>
							<?php else: ?>
							<li>
								<a href="/members/logout">
									<i class="fa fa-sign-out"></i> Sign out
								</a>
							</li>
							<?php endif; ?>
						</ul>
					</nav>
				</div>
