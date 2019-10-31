<?php
function customizer( $wp_customize ) {
$wp_customize->add_panel( 'customization', array(
'priority'       => 2,
'title'          => __('Customização')
));
$wp_customize->add_section( 'general' , array(
'title'       => __( 'General' ),
'panel' => 'customization',
'priority'    => 10
));
$wp_customize->add_section( 'header' , array(
'title'       => __( 'Header' ),
'panel' => 'customization',
'priority'    => 1
));
$wp_customize->add_setting( 'logo' );
$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'logo', array(
'label'    => __( 'Logo' ),
'section'  => 'header',
'settings' => 'logo'
)));
$wp_customize->add_section( 'artist_fields' , array(
'title'       => __( 'Artist' ),
'panel' => 'customization',
'priority'    => 1
));
$artist_fields = array(
array(
'label' => 'Artist ID',
'name' => 'artist_id'
),
array(
'label' => 'Album ID',
'name' => 'album_id'
),
);
if(!empty($artist_fields)){
$wp_customize->add_section( 'artist_fields' , array(
'title'       => __( 'Artist Info' ),
'panel' => 'customization',
'priority'    => 0
));
foreach ($artist_fields as $key => $value) {
$wp_customize->add_setting($value['name']);
$wp_customize->add_control($value['name'],  array(
'label' => $value['label'],
'section' => 'artist_fields',
'type' => 'text',
'settings' => $value['name']
));
}
}
$social_networks = array(
array(
'title' => 'Facebok',
'slug' => 'facebook'
),
array(
'title' => 'Youtube',
'slug' => 'youtube'
),
array(
'title' => 'Instagram',
'slug' => 'instagram'
),
array(
'title' => 'Spotify',
'slug' => 'spotify'
),
);
if(!empty($social_networks)){
$wp_customize->add_section( 'social_networks' , array(
'title'       => __( 'Social Networks' ),
'panel' => 'customization',
'priority'    => 0
));
foreach ($social_networks as $key => $value) {
$wp_customize->add_setting($value['slug']);
$wp_customize->add_control($value['slug'],  array(
'label' => $value['title'],
'section' => 'social_networks',
'type' => 'text',
'settings' => $value['slug']
));
}
}
}