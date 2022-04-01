<?php

/////////////// REGISTRAR TAXONAMIA INIT ///////////////
function al_in_registrar_taxonamia()
{
    register_taxonomy( 
        'paises',
        'destinos',
        array(
            'labels'       => array('name' => 'Países'),
            'hierarchical' => true,

        )
    );
}
add_action( 'init', 'al_in_registrar_taxonamia' );

/////////////// POST CUSTOMIZADO INIT ///////////////
function al_in_adicionar_post_customizado()
{
    register_post_type( 'destinos', 
        array(
            'labels'        => array( 'name' => 'Destinos'), 
            'public'        => true,
            'menu_position' => 0,
            'supports'      => array('title', 'editor', 'thumbnail'),
            'menu_icon'     => 'dashicons-admin-site'
        )
    );
}
add_action( 'init', 'al_in_adicionar_post_customizado' );

/////////////// INICIANDO FUNCIONALOIDADES DO TEMA /////////////
function al_in_adicionar_recursos_tema()
{
    add_theme_support( 'custom-logo' );
    add_theme_support( 'post-thumbnails' );
}

add_action( 'after_setup_theme', 'al_in_adicionar_recursos_tema' );

/////////////// REGISTRAR MENU INIT ///////////////
function al_in_registrar_menu()
{
    register_nav_menu( 'menu-navegacao', 'Menu Navegação' );
}
add_action( 'init', 'al_in_registrar_menu' );

////////////// REGISTRAR UM NOPO POST TYPE ///////////////
function al_in_adicionar_post_customizado_banner()
{
    register_post_type( 
        'banners',
        array(
            'label'         => 'Banner',
            'public'        => true,
            'menu_position' => 1,
            'menu_icon'     => 'dashicons-format-image',
            'supports'      => array('title', 'thumbnail')
        )
    );
}
add_action( 'init', 'al_in_adicionar_post_customizado_banner' );

////////////// REGISTRAR METABOX ////////////////
function al_in_registrar_metabox()
{
    add_meta_box(
        'al_in_metabox',
        'Texto para Home',
        'al_in_callback',
        'banners'
    );
}
add_action( 'add_meta_boxes', 'al_in_registrar_metabox' );

//////////////// FUNÇÃO CALLBACK PARA META BOX //////////////
function al_in_callback($post)
{

    $texto_home_1 = get_post_meta($post->ID,'_texto_home_1', true);
    $texto_home_2 = get_post_meta($post->ID,'_texto_home_2', true);
    ?>

        <label for="texto_home_1">Texto 1</label>
        <input type="text" name="texto_home_1" style="width: 100%" value="<?= $texto_home_1 ?>"/>
        <br>
        <br>
        <label for="texto_home_2">Texto 2</label>
        <input type="text" name="texto_home_2" style="width: 100%" value="<?= $texto_home_2 ?>"/>

    <?php
}

////////////// SALVAR DADOS META BOX /////////////
function al_in_salvar_dados_metabox($post_id){
    foreach( $_POST as $key=>$value){
        if($key !== 'texto_home_1' && $key !== 'texto_home_2'){
            continue;
        }

        update_post_meta(
              $post_id,
            '_'. $key,
            $_POST[$key]
        );
    }
}
add_action( 'save_post', 'al_in_salvar_dados_metabox' );

////////////// PEGAR TEXTOS QUE SERÃO UTILIZADOS NA HOME ////////////
function pegandoTextoParaBanner()
{
    $args = array(
        'post_type'      => 'banners',
        'post_status'    => 'publish',
        'posts_per_page' => 1
    );

    $query = new WP_Query( $args );
    if($query->have_posts()):
        while($query->have_posts()): $query->the_post();
            $texto1 = get_post_meta(get_the_ID(), '_texto_home_1', true);
            $texto2 = get_post_meta(get_the_ID(), '_texto_home_2', true);
            
            return array(
                'texto_1' => $texto1,
                'texto_2' => $texto2 
            );
        endwhile;
    endif;
}

/////////////// ADICIONAR SCRIPT NO RODAPÉ //////////////
function al_in_adicionar_scripts()
{
    $textoBanner = pegandoTextoParaBanner();

    if(is_front_page()){
        wp_enqueue_script('typed-js', get_template_directory_uri(). '/assets/js/typed.min.js', array(), false, true);
        wp_enqueue_script('texto-banner-js', get_template_directory_uri(). '/assets/js/texto-banner.js', array('typed-js'), false, true);
        wp_localize_script('texto-banner-js', 'data', $textoBanner);
    }
}
add_action( 'wp_enqueue_scripts', 'al_in_adicionar_scripts' );