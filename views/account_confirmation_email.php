<h1>Account activation required.</h1>
<h2>To activate your SociableFundraiser account click on the account activation link below.</h2>
<strong>Account activation link: </strong><?php echo $validation_link?>
<br/><br/><br/>
<em>Copyright &copy; <?php echo date('Y')?>&nbsp;<?php echo \Config::get('app.company','') ?>, All rights reserved.</em>
<br/>
<strong>Our mailing address is:</strong>
<br/>
<?php echo \Config::get('app.company','') ?>
<br />
<?php echo \Config::get('app.mailing_address',''); ?>