<?php
/*
 Plugin Name: BuddyForms CPT-onomies
 Plugin URI: http://buddyforms.com/downloads/CPT-onomies/
 Description: BuddyForms CPT-onomies Using Custom Post Types as Taxonomies
 Version: 1.0.2
 Author: Luca69Cr, svenl77
 License: GPLv2 or later
 Network: false

 *****************************************************************************
 *
 * This script is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 ****************************************************************************
 */

add_action('buddyforms_update_post_meta',10, 2);

function buddyforms_cpt_onomies($customfield, $post_id){
    global $cpt_onomy;

    if( $customfield['type'] == 'Taxonomy' ){

        $taxonomy = get_taxonomy($customfield['taxonomy']);

        if (isset($taxonomy->hierarchical) && $taxonomy->hierarchical == true)  {

            if(isset($_POST[ $customfield['slug'] ]))
                $tax_item = $_POST[ $customfield['slug'] ];

            if($tax_item[0] == -1 && !empty($customfield['taxonomy_default'])){
                //$taxonomy_default = explode(',', $customfield['taxonomy_default'][0]);
                foreach($customfield['taxonomy_default'] as $key => $tax){
                    $tax_item[$key] = $tax;
                }
            }

            if ($cpt_onomy){ $cpt_onomy->wp_set_post_terms( $post_id, $tax_item, $customfield['taxonomy'], false );}
            else{ wp_set_post_terms( $post_id, $tax_item, $customfield['taxonomy'], false );}

        } else {

            $slug = Array();

            if(isset($_POST[ $customfield['slug'] ])) {
                $postCategories = $_POST[ $customfield['slug'] ];

                foreach ( $postCategories as $postCategory ) {

                    if ($cpt_onomy){$term = $cpt_onomy->get_term_by('id', $postCategory, $customfield['taxonomy']);}
                    else {$term = get_term_by('id', $postCategory, $customfield['taxonomy']);}

                    $slug[] = $term->slug;
                }
            }

            if ($cpt_onomy){$cpt_onomy->wp_set_post_terms( $post_id, $slug, $customfield['taxonomy'], false );}
            else{wp_set_post_terms( $post_id, $slug, $customfield['taxonomy'], false );}

        }

        if( isset( $_POST[$customfield['slug'].'_creat_new_tax']) && !empty($_POST[$customfield['slug'].'_creat_new_tax'] ) && !($cpt_onomy)){
            $creat_new_tax =  explode(',',$_POST[$customfield['slug'].'_creat_new_tax']);
            if(is_array($creat_new_tax)){
                foreach($creat_new_tax as $key => $new_tax){
                    $wp_insert_term = wp_insert_term($new_tax,$customfield['taxonomy']);
                    wp_set_post_terms( $post_id, $wp_insert_term, $customfield['taxonomy'], true );
                }
            }

        }
    }
}