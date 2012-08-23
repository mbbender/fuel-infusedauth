<?php

return array(
'prep_value'            => true,
'auto_id'               => true,
'auto_id_prefix'        => '',
'form_method'           => 'post',
'form_template'  		=> "{form_open}\n\t\t\n{fields}\n",
    'fieldset_template'     => "\n\t\t<tr><td colspan=\"2\">{open}\n{fields}\n\t\t{close}\n",
    'field_template'        => "<div class='row'> <div class='span12'>{label} {error_msg}<div class='input'>{field}</div> </div></div>",
    'multi_field_template'  => "<div class='error'>{group_label}{required}</div><div class='error'>{fields} {field} {label}<br />\n{fields}{description} {error_msg}</div>",
    'error_template'        => '<div class="alert alert-error">{error_msg}</div>',
    'required_mark'         => '*',
    'inline_errors'         => true,
    'error_class'           => 'alert alert-error',
    'help_text'             => '<span>{help_text}</span>',
);

