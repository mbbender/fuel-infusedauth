<?php echo $fieldset; ?>

<button type="submit" value="Register" name="btnSubmit" title="Login using email address and password" class="btn btn-success">Register</button>
| <a href="/auth/login">Login</a>
| <a href="/auth/reset">Forgot your password?</a>


</form>

<?php if($providers): ?>
    <h2>Or login with</h2>
    <?php foreach($providers as $provider=>$config): ?>
        <?php if(!empty($config['id']) || !empty($config['key'])) echo Html::anchor('auth/session/'.$provider,ucfirst($provider),array('class'=>'btn btn-primary btn-large')) ?>&nbsp;
    <?php endforeach ?>
<?php endif ?>