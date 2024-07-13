<?php
/**
 * Plugin Name: Books and Chapters
 * Description: A nice project using books and chapters
 */

class BookAndChapter {
    
    public function __construct() {
        add_action( 'init', array( $this, 'init' ) );
    }
    public function init() {
        add_filter( 'the_content', array( $this, 'show_book_in_chapter_thumbnail' ) );
        add_filter( 'the_content', array( $this, 'show_book_in_chapter' ) );
        // add_filter( 'post_type_link', array( $this, 'chapter_cpt_slug_fix' ),1,2 );	
        add_filter( 'the_content', array( $this, 'show_relate_book_by_meta' ) );
        add_filter( 'the_content', array( $this, 'show_relate_book_by_taxsonomy' ) );

    }
    // function chapter_cpt_slug_fix($post_link, $chapter) {
    //     if(get_post_type($chapter)=='chapters'){
    //         $d = get_the_ID();
    //         $book_id = get_post_meta($d, 'book_id', true);
    //         $book = get_post($book_id);
    //         $post_link = str_replace('%book%', $book->post_name, $post_link);
    //     }
    //     return $post_link;
    // }
    public function show_book_in_chapter_thumbnail( $the_content ) {

        if(is_singular( 'chapters' )) {

            $book_id = get_post( get_post_meta( get_the_ID (), 'book_id', true ) );
            $image = get_the_post_thumbnail( $book_id, 'medium' );
            $the_content = " <p> $image </p> " . $the_content;

        }
        return $the_content;
    }
    public function show_relate_book_by_meta( $content ) {

        if(is_singular('books')){
            $book_id = get_the_ID();
            $genre = get_post_meta($book_id, 'genre', true);
            $args = [
                'post_type' => 'books',
                'post__not_in' => [$book_id],
                'meta_key'=>'genre',
                'meta_value'=>$genre
            ];
            $books = get_posts($args);
            
            if($books){
                $content .= '<h2>Related Books By Meta Field</h2>';
                $content .= '<ul>';
                foreach($books as $book){
                    $content .= '<li><a href="' . get_permalink($book->ID) . '">' . $book->post_title . '</a></li>';
                }
                $content .= '</ul>';
            }
        }
        return $content;
    }
    public function show_book_in_chapter( $content ) {
        if(is_singular( 'books' )) {

            $content .= '<h2>Book Content</h2>';
            $get_chapter = get_posts( array( 

                'post_type' => 'chapters', 
                'meta_query' => array(
                    array(
                        'key' => 'book_id',
                        'value' => get_the_ID(),
                        'compare' => '='
                    )
                ),
                'meta_key' => 'chapter_number',
                'orderby' => 'meta_value_num',
                'order' => 'ASC'



            ) );
            if($get_chapter){
                $content .= '<ul>';
                foreach($get_chapter as $chapter){
                    $content .= '<li><a href="' . get_permalink($chapter->ID) . '">' . $chapter->post_title . '</a></li>';
                }
                $content .= '</ul>';
            }else{
                $content .= '<b>No Chapter Found</b>';
            }
            

        }
        return $content;
    }
    public function show_relate_book_by_taxsonomy( $content ) {

        if(is_singular('books')){
            $book_id = get_the_ID();
            $genres = wp_get_post_terms($book_id, 'genre');
            $genre = $genres[0]->term_id;
            $args = [
                'post_type' => 'books',
                'post__not_in' => [$book_id],
                'tax_query' => [
                    [
                        'taxonomy' => 'genre',
                        'field' => 'term_id',
                        'terms' => $genre
                    ]
                ]
            ];
            $books = get_posts($args);
            
            if($books){
                $content .= '<h2>Related Books By Taxonomy</h2>';
                $content .= '<ul>';
                foreach($books as $book){
                    $content .= '<li><a href="' . get_permalink($book->ID) . '">' . $book->post_title . '</a></li>';
                }
                $content .= '</ul>';
            }
        }
        return $content;
        
    }
   


}
new BookAndChapter();






























 ?>