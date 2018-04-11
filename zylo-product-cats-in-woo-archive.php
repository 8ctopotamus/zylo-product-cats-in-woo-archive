<?php
/**
 * Plugin Name: Product Categories in Woo Archive
 * Description: Display product categories/subcategories as separate lists in woocommerce product archive pages
 * Version: 1.0
 * Author:      Zylo, LLC
 * Author URI:  https://zylocod.es
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
**/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


// styles + scritps
function zylo_prod_cats_register_all_scripts_styles() {
	wp_register_style( 'zylo-prod-cats', plugins_url( 'css/zylo-prod-cats.css',  __FILE__ ));
  wp_enqueue_style('zylo-prod-cats');
}
add_action( 'wp_loaded', 'zylo_prod_cats_register_all_scripts_styles' );


function is_subcategory($cat_id = null) {
  if (is_tax('product_cat')) {
    if (empty($cat_id)) {
      $cat_id = get_queried_object_id();
    }
    $cat = get_term(get_queried_object_id(), 'product_cat');
    if (empty($cat->parent)){
      return false;
    } else {
      return true;
    }
  }
  return false;
}


// generate nav
function zylo_product_subcategories( $args = array() ) {
  global $wp;
  $catId = get_queried_object_id();
  $currentCatName = get_queried_object()->name;

  if (is_subcategory($catId)) {
    $parentID = get_queried_object()->parent;
  } else {
    $parentID = $catId;
  }

  $args = array(
    'taxonomy' => 'product_cat',
    'parent' => $parentID,
    'hierarchical' => 1,
    'hide_empty' => false
  );

  $subcats = get_terms($args);

  if ($subcats) {
    echo '<ul class="zylo-prod-cats-nav">';
    foreach ($subcats as $cat) {
      // determine active class
      if ($currentCatName == $cat->name) {
        $activeClass="active";
      } else {
        $activeClass = '';
      }
      echo '<li>';
      echo '<a href="'. home_url($wp->request . '/' . $cat->slug) .'" class="'.$activeClass.'">' . $cat->name . '</a>';
      echo '</li>';
    }
    echo '</ul>';
  }
}
add_action( 'woocommerce_before_main_content', 'zylo_product_subcategories', 50 );
