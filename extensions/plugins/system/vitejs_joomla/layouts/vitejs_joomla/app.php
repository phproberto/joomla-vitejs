<?php
defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;

$config = [
    "basePath" => JPATH_SITE . "/media/vitejs_joomla",
    "baseUrl"  => Uri::root(true) . "/media/vitejs_joomla",
];
?>
<div id="app"></div>
@vite("src/main.js", <?php echo json_encode($config); ?>)
