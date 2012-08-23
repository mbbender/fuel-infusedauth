<?php echo $registration_fieldset; ?>

<button type="submit" value="Register" name="btnSubmit" title="Login using email address and password" class="btn btn-success">Register</button>
| <a href="login">Login</a>
| <a href="reset">Forgot your password?</a>


</form>

<?php if($providers): ?>
    <h2>Or login with</h2>
    <?php foreach($providers as $provider=>$config): ?>
        <?php if(!empty($config['id']) || !empty($config['key'])):?>
            <a href='session/<?php echo $provider?>' class='btn btn-primary btn-large'><?php echo ucfirst($provider)?>&nbsp;
        <?php endif;?>
    <?php endforeach ?>
<?php endif ?>