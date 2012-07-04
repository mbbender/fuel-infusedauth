<?php echo $fieldset->build(); ?>
<button type="submit" value="Reset" name="btnSubmit" title="Reset your password" class="btn btn-success">Reset</button>
| <a href="/auth/login">Login</a>
</form>

<?php if($providers): ?>
<h2>Or login with</h2>
<?php foreach($providers as $provider=>$config): ?>
    <?php if(!empty($config['id']) || !empty($config['key'])) echo Html::anchor('auth/session/'.$provider,ucfirst($provider),array('class'=>'btn btn-primary btn-large')) ?>&nbsp;
    <?php endforeach ?>
<?php endif ?>