<?php

use Myrotvorets\WordPress\Classifier\Admin;

defined( 'ABSPATH' ) || die();
?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<form action="options.php" method="post">
	<?php
		settings_fields( Admin::OPTION_GROUP );
		do_settings_sections( Admin::OPTIONS_MENU_SLUG );
		submit_button();
	?>
	</form>
</div>
