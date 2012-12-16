<?php echo $fieldset->build(); ?>
<button type="submit" value="Login" name="btnSubmit" title="Login using email address and password" class="btn btn-success">Login</button>
| <a href="/auth/register">Register</a>
| <a href="/auth/reset">Forgot your password?</a>
</form>

<?php if(!empty($providers)): ?>
<h2>Or login with</h2>
<?php foreach($providers as $provider=>$config): ?>
    <?php if(!empty($config['id']) || !empty($config['key'])) echo Html::anchor('auth/session/'.$provider,ucfirst($provider),array('class'=>'btn btn-primary btn-large')) ?>&nbsp;
    <?php endforeach ?>
<?php endif ?>