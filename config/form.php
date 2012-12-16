<?php

return array(
'prep_value'            => true,
'auto_id'               => true,
'auto_id_prefix'        => '',
'form_method'           => 'post',
'form_template'  		=> "{form_open}\n\t\t\n{fields}\n",
    'fieldset_template'     => "\n\t\t<tr><td colspan=\"2\">{open}\n{fields}\n\t\t{close}\n",
    'field_template'        => "\t\t<div class='row'><div class='span12'>\n\t\t\t{label}\n\t\t\t<div class='input'>{field}</div> {error_msg}\n\t\t</div></div>",
    'multi_field_template'  => "\t\t\n\t\t\t<div class='error'>{group_label}{required}</div>\n\t\t\t<div class='error'>{fields}\n\t\t\t\t{field} {label}<br />\n{fields}{description}\t\t\t{error_msg}\n\t\t\t</div>\n\t\t\n",
    'error_template'        => '<span>{error_msg}</span>',
    'required_mark'         => '*',
    'inline_errors'         => false,
    'error_class'           => 'validation_error',
    'help_text'             => '<span>{help_text}</span>',
);

