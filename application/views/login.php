<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="">
		<meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
		<meta name="generator" content="Jekyll v3.8.5">
		<title>Dashboard · AWS</title>

		<!-- Bootstrap core CSS -->
		<link rel="stylesheet" href="<?= base_url('assets/vendors/Bootstrap/bootstrap.css') ?>">

		<!-- Custom styles for this template -->
		<!-- <link href="<?= base_url('assets/vendors/Bootstrap/signin.css') ?>" rel="stylesheet"> -->
	</head>
	<body>
		<div class="container">
			<div class="row" style="margin-top: 100px;">
				<div class="col-6 d-flex align-items-center">
					<img src="<?= base_url('assets/images/Logo/large-logo.png') ?>" alt="" style="width: 80%; margin: 0 10%;">
				</div>
				<div class="col-6 d-flex align-items-center">
					<form class="p-4" action="<?= base_url('authenticate') ?>" method="POST" style="width: 80%; margin: 0 auto; background-color: #f5f5f5; border: 1px solid #ced4da;">
						<h1 class="h3 mb-3 font-weight-normal">Dashboard Sign In</h1>
						<div class="form-group">
						<label for="input3">Email Address</label>
							<input type="email" class="form-control" id="input3" name="email" required="required">
						</div>
						<div class="form-group">
							<label for="input6">Password</label>
							<div style="position: relative;">
								<input type="password" class="form-control" id="input6" name="password" required="required" style="padding-right: 40px;">
								<button type="button" id="togglePassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: transparent; cursor: pointer; padding: 5px; color: #6c757d;">
									<svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
										<path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
										<path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
									</svg>
								</button>
							</div>
						</div>
						<button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
						<p><?= $this->session->flashdata('err_msg') ?></p>
					</form>
				</div>
			</div>
			<div class="row text-center">
				<div class="col">
					<p class="mt-5 mb-3 text-muted">© 2017-<?= date('Y') ?></p>
				</div>
			</div>
		</div>

		<script>
			// Password visibility toggle
			const togglePassword = document.getElementById('togglePassword');
			const passwordInput = document.getElementById('input6');
			const eyeIcon = document.getElementById('eyeIcon');

			togglePassword.addEventListener('click', function() {
				// Toggle password visibility
				const type = passwordInput.type === 'password' ? 'text' : 'password';
				passwordInput.type = type;

				// Toggle eye icon (eye vs eye-slash)
				if (type === 'text') {
					eyeIcon.innerHTML = '<path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/><path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/>';
				} else {
					eyeIcon.innerHTML = '<path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>';
				}
			});
		</script>
	</body>
</html>
