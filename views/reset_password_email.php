<h1>Your password has been reset.</h1>
<strong>Your new password is: </strong><?php echo $new_password?>
<br/>
Login: <a href="<?php echo Uri::create('auth/login')?>"><?php echo Uri::create('auth/login')?></a>
<br/><br/><br/>
<em>Copyright &copy; <?php echo date('Y')?>&nbsp;<?php echo \Config::get('app.company','') ?>, All rights reserved.</em>
<br/>
<strong>Our mailing address is:</strong>
<br/>
<?php echo \Config::get('app.company','') ?>
<br />
<?php echo \Config::get('app.mailing_address',''); ?>