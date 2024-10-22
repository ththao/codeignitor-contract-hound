	<!-- CH Analytics Snippets -->
  <?php if (isset($intercom) && $intercom): ?>
    <?php if (!empty($iCurrentlyLoggedInMemberId)): ?>
  		<script>
  			window.intercomSettings = {
  				app_id: '<?= $_ENV['INTERCOM_APP_ID']  ?>',
  				user_id: <?= $this->session->userdata('member_id'); ?>,
  				email: "<?php echud($this->session->userdata('member_email')); ?>",
  				created_at: <?= strtotime($this->session->userdata('member_create_date')); ?>
  			};
  		</script>
    <?php endif; ?>

		<script>(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/<?= $_ENV['INTERCOM_APP_ID'] ?>';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})()</script>
	<?php endif; ?>

	<?php if (isset($google) && $google): ?>
		<!-- Google Tag Manager -->
		<noscript><iframe src="//www.googletagmanager.com/ns.html?id=<?= $_ENV['GOOGLE_TAG_MANAGEMENT_ID'] ?>"
		height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
		j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
		'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','<?= $_ENV['GOOGLE_TAG_MANAGEMENT_ID'] ?>');</script>
		<!-- End Google Tag Manager -->
	<?php endif; ?>