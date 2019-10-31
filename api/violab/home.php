<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php echo get_bloginfo('name'); ?></title>
	<?php wp_head(); ?>
	<?php
		$theme = wp_get_theme();
	?>	
</head>
<body>
	<img class="header-logo" src="<?php echo $theme->get_screenshot(); ?>" alt="<?php echo get_bloginfo('name'); ?>" />
	<?php wp_footer(); ?>
</body>
</html>