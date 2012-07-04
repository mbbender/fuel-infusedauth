
<div class="hero-unit">
    <h1>Password reset successful</h1>
    <p id="message">An email has been sent to <?php echo $user->email ?> with your new password.</p>
    <p>
        <?php echo Html::anchor('auth/login','Login', array('class'=>'btn btn-primary btn-large')); ?>
    </p>
</div>
