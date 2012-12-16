<?php if (isset($current_user)){
    $icon = '<i class="icon-user icon-white"></i>';
    if(isset($profile_fields['avatar'])) $icon = '<img src="'.$profile_fields['avatar'].'" class="avatar-tiny"/>';
    ?>
<ul class="nav pull-right">

    <li class="dropdown" >
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <?php echo $icon.' '.$profile_fields['full_name'] ?>
            <b class="caret"></b>
        </a>
        <ul class="dropdown-menu">
            <li><?php echo Html::anchor('me/profile','Profile')?></li>
            <li><?php echo Html::anchor('me/settings','Settings')?></li>
            <li><?php echo Html::anchor('auth/logout','Logout')?></li>
        </ul>
    </li>
</ul>
<?php }?>