<?php
    function regScripts() {
wp_enqueue_script('vendor', get_template_directory_uri()."/assets/js/vendor.min.js",'','', false);
wp_enqueue_script('all', get_template_directory_uri()."/assets/js/all.min.js",'','', false);
wp_enqueue_script('analytics', get_template_directory_uri()."/assets/js/analytics.js",'','', false);
wp_enqueue_script('fbevents', get_template_directory_uri()."/assets/js/fbevents.js",'','', false);
wp_enqueue_script('update', get_template_directory_uri()."/assets/js/update.min.js",'','', false);
wp_enqueue_script('ytc', get_template_directory_uri()."/assets/js/ytc.js",'','', false);
wp_enqueue_script('owl', get_template_directory_uri()."/assets/js/owl.carousel.js",'','', false);
wp_enqueue_style('fonts', get_stylesheet_directory_uri().'/assets/css/fonts.css', array(), '', 'all');
wp_enqueue_style('socicon', 'https://d1azc1qln24ryf.cloudfront.net/114779/Socicon/style-cf.css?u8vidh', array(), '', 'all');
wp_enqueue_style('style', get_stylesheet_directory_uri().'/style.css', array(), '', 'all');
wp_enqueue_style('vendors', get_stylesheet_directory_uri().'/assets/css/vendor.css', array(), '', 'all');
wp_enqueue_style('custom', get_stylesheet_directory_uri().'/assets/css/custom.css', array(), '', 'all');
wp_enqueue_style('owl-default', get_stylesheet_directory_uri().'/assets/css/owl.carousel.css', array(), '', 'all');
wp_enqueue_style('owl-theme', get_stylesheet_directory_uri().'/assets/css/owl.theme.default.min.css', array(), '', 'all');
wp_enqueue_style('animate', get_stylesheet_directory_uri().'/assets/css/animate.css', array(), '', 'all');
$custom_css = '
.guia_da_limpeza_logo .logo {
background: url('.get_stylesheet_directory_uri().'/assets/imgs/logo_guia_da_limpeza.png);
}
.guia_da_limpeza .search_wrapper{
background: url('.get_stylesheet_directory_uri().'/assets/imgs/guia_limpeza_bg.jpg) top center no-repeat;
}
';
wp_add_inline_style( 'style', $custom_css );
    }

function enqueue_my_scripts() {

wp_enqueue_script('jsmask',"https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js",'','', false);

}
add_action( 'admin_enqueue_scripts', 'enqueue_my_scripts' );

function hookScript($hook) {
    echo "
    <script>
        $(document).ready(function () {
            $('#acf-field_5d61585543e94').mask('#.##0,00', {reverse: true});
        });
    </script>
    ";
}

add_action('admin_enqueue_scripts', 'hookScript');