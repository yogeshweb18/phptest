<?php
/**
 * Script for remove anchor tag from post description
 * @author Yogesh
 * 
 * @since 2022-09-16
 * 
 */
ini_set('max_execution_time',0);
 class remove_anchor_tag extends WP_CLI_Command {

 	public function search_post(){

            $categories = get_categories();
            $cat_array=array();
            foreach($categories as $category) {
                       
                echo "\n ======================".$category->name."==============================";
                $query = new WP_Query( array( 'category_name' => $category->name ) );
                    $post_id =array(); 
                    if ( $query->have_posts() ) {
                       while ( $query->have_posts() ) {
                            $query->the_post();
                            $post_id[] =get_the_ID();
                        }   
                        $content = get_the_content($post_id);
                        $dom = DOMDocument::loadHTML( $content );
                        $data = $dom->getElementsByTagName('a');
                        $record = $data->length;
                        if($record > 0){ 
                            //print_r($post_id);      // ***********Display all post id of Cat  //612304;
                            for($i=0 ; $i < count($post_id); $i++){ 
                                echo "\n ------------------------".$category->name.'###'.$post_id[$i]."------------------------------";
                            $content_post = get_post($post_id[$i]);
                            $content_one = $content_post->post_content;
                            preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $content_one, $result);
                            //print_r($result[0]);   // ***********  All anchor tag of one post id
                            $result_href= $result['href'];
                            $total =count($result_href);
                                $link =array();
                                $p=array();
                                for($j=0 ; $j < $total; $j++){ 
                                    $link[]= $result_href[$j];

                                    if(!empty($link[$j])){
                                    $headers = get_headers($link[$j], 1);
                                    }

                                    if ($headers[0] =='HTTP/1.0 404 Not Found'){
                                    $p[]=$result[0][$j];
                                    } 
                                }
                                //print_r($p);  // *********** Removing anchor tag of one post id
                                $string_one= str_replace($p,'',$content_one);
                                $post_insert = array('ID' => $post_id[$i],
                                                     'post_content'  => $string_one);
                                $post_insert_id = wp_update_post( $post_insert );
                                echo "\n Inserted WP post ID : id-> $post_insert_id \n";
                                die();  // *********** for one post

                            } 
                             //die();  // *********** for one Cat
                            
                        }
                        
                    }


            }
           

    }
    
 }     
WP_CLI::add_command( 'remove_anchor_tag', 'remove_anchor_tag' );        

