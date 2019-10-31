<?php 
	switch (pll_current_language('slug')) {
		case 'en':
			// code...
			$carreiras = get_page_by_path('Careers');
			$contato = get_page_by_path('Contact');
			break;
		case 'es':
			// code...
			$carreiras = get_page_by_path('Carreras');
			$contato = get_page_by_path('Contacto');	
			break;
		default:
			// code...
			$carreiras = get_page_by_path('Carreiras');
			$contato = get_page_by_path('Contato');
			break;
	}
	$shortcuts = array(
		array(
			'ID'=>$contato->ID,
			'post_title'=>$contato->post_title,
			'url'=>get_page_link($contato->ID)
		),								
		array(
			'ID'=>$carreiras->ID,
			'post_title'=>$carreiras->post_title,
			'url'=>get_page_link($carreiras->ID)
		)							
	);	
	
	$locale = get_locale();
	$page = get_post(get_the_id())->post_title;
	$title = get_bloginfo('name');

	if(!is_search() && !is_category() && !is_archive()){
		$title .= ' | '.$page;
	} elseif(is_search()) {
		$title .= ' | '.pll__('Resultados para "').' '.$_GET['s'].'"';
	} elseif(is_archive()) {
		$title .= ' | '.pll__('Arquivo').' - '.get_the_archive_title();
	} else {
		$title .= ' | '.get_queried_object()->name;
	}
?>