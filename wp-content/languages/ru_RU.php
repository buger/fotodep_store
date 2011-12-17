<?php
function ru_extend_menu() { ?>
  <style type="text/css">.pressthis a { width: 135px;}</style>
<?php }

function change_update_url($options) {
if (isset($options->updates) && is_array($options->updates))
foreach ( $options->updates as $key => $value ) {
if ($value != '')
{
$options->updates[$key] = (object)
str_replace('http://ru.wordpress.org/',
'http://mywordpress.ru/download/', (array) $value); 
}
}
        return $options;
}
add_filter('pre_update_option_update_core', 'change_update_url');
add_action('admin_head', 'ru_extend_menu');
?>
