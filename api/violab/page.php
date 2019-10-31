<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php echo get_bloginfo('name'); ?></title>
	<?php wp_head(); ?>
</head>
<body>
	<?php if ( have_posts () ) : while (have_posts()) : the_post(); ?>
		<?php the_content(); ?>
	<?php endwhile;
	endif; ?>		
</body>
</html>