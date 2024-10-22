		<?php if ($this->session->flashdata('success')): ?>
		<script>
			var notifications = new Array(
				[{ title: 'Success: ', message: '<?php echo $this->session->flashdata('success'); ?>' },{ delay: 7000, type: 'success' }]
			);
		</script>
		<?php elseif ($this->session->flashdata('info')): ?>
		<script>
			var notifications = new Array(
				[{ title: 'Info: ', message: '<?php echo $this->session->flashdata('info'); ?>' },{ delay: 0, type: 'info' }]
			);
		</script>
		<?php elseif ($this->session->flashdata('error')): ?>
		<script>
			var notifications = new Array(
				[{ title: 'Error: ', message: '<?php echo $this->session->flashdata('error'); ?>' },{ delay: 7000, type: 'danger' }]
			);
		</script>
		<?php elseif ($this->session->flashdata('warning')): ?>
		<script>
			var notifications = new Array(
				[{ title: 'Warning: ', message: '<?php echo $this->session->flashdata('warning'); ?>' },{ delay: 7000, type: 'warning' }]
			);
		</script>
		<?php endif; ?>

		<?php $this->load->view('layout/analytics_snippets', ['intercom' => 1, 'google' => 1]); ?>
	</body>
</html>
