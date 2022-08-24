<?php

if ( ! defined('ABSPATH') ) {
    die('Direct access not permitted.');
}

function mc_citacion_shortcode( $atts = null, $content = null, $tag = null ) {
    $post_id = get_the_ID();
    $atributes = shortcode_atts([  'post_id' => '0',  ], $atts);
    if($atributes['post_id']){
        $post_id = intval($atributes['post_id']);
    }
    $text = get_post_meta($post_id, 'mc_citacion_metabox', true);
    echo $text;
}
add_shortcode( 'mc-citacion', 'mc_citacion_shortcode' );






/*  SECTION PROCESS URLS */
function process_contents(){
    $post_ids = get_posts(array(
        'fields'          => 'ids', // Only get post IDs
        'posts_per_page'  => -1
    ));
    $validations = [];
    foreach ($post_ids as $key => $val) {
        $urls_found = [];
        $post_proccessed = get_post_meta($val, 'mc_processed', true);
        //$post_proccessed = 0;
        if(!$post_proccessed){
            // $post_content = sanitize_text_field(get_post($val)->post_content);
            $post_content = json_encode(get_post($val)->post_content);
            // echo "<script>console.log('Debug Objects: " . $post_content . "' );</script>";
            
            $pos_ini = 0;
            while( ($pos_ini = strpos($post_content, '<a href=', $pos_ini)) !== false ) {
                // echo "<script>console.log('POSICION INICIO: ".$pos_ini."' );</script>";
                $pos_ini = $pos_ini + 9;
                $pos_end = strpos($post_content, '>', $pos_ini);
                $pos_end = $pos_end-3;
                // echo "<script>console.log('POSICION FINAL: ".$pos_end."' );</script>";
                $url_found = substr($post_content, $pos_ini+1, ($pos_end-($pos_ini)));
                // echo "<script>console.log('URL: ".$url_found."' );</script>";
                $pos_ini = $pos_end+1;                
                array_push($urls_found, array(
                    'post_id' => $val,
                    'url' => $url_found
                ));
            }
            
            /*
                1: enlace inseguro
                2: protocolo no especificado
                3: enlace malformado
                4: enlace status incorrecto
            */
            foreach ($urls_found as $key_url => $val_url) {
                $url_to_validate = preg_replace('/\\\\/', '', $val_url['url']);
                echo "<script>console.log('URL: ".$val_url['post_id']."' );</script>";
                echo "<script>console.log('URL: ".$val_url['url']."' );</script>";
                
                // Validation 3
                if(!(filter_var($url_to_validate, FILTER_VALIDATE_URL))){
                    $error = array(
                        'post_id' => get_permalink( $val_url['post_id'] ),
                        'error_type' => __("Enlace malformado", "mc-citacion"),
                        'url' => $url_to_validate
                    );
                    array_push($validations, $error);
                }
                // Validation 2
                else if(strpos($url_to_validate, "https") !== 0){
                    $error = array(
                        'post_id' => get_permalink( $val_url['post_id'] ),
                        'error_type' => __("Protocolo no especificado", "mc-citacion"),
                        'url' => $url_to_validate
                    );
                    array_push($validations, $error);
                }
                // Validation 1
                else if(strpos($url_to_validate, "http") !== 0){
                    $error = array(
                        'post_id' => get_permalink( $val_url['post_id'] ),
                        'error_type' => __("Enlace inseguro", "mc-citacion"),
                        'url' => $url_to_validate
                    );
                    array_push($validations, $error);
                }
                else{
                    // Validation 4
                    $handle = curl_init($url_to_validate);
                    curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
                    $response = curl_exec($handle);
                    /* Check for 404 (url not found). */
                    $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
                    if(strpos($httpCode, "40") === 0 || strpos($httpCode, "50") === 0){
                        /* Handle 40* or 50* here. */
                        $error = array(
                            'post_id' => get_permalink( $val_url['post_id'] ),
                            'error_type' => __("Enlace Status incorrecto: ".$httpCode, "mc-citacion"),
                            'url' => $url_to_validate
                        );
                        array_push($validations, $error);
                    }
                    curl_close($handle);
                }

                if($error){
                    $validation_id = wp_insert_post(array(
                        'post_title'=>'random',
                        'post_type'=>'validation', 
                        'post_content'=>'demo text',
                        'post_status' => 'publish'
                    ));
                    add_post_meta($validation_id, 'url', $error['url']);
                    add_post_meta($validation_id, 'error_type', $error['error_type']);
                    add_post_meta($validation_id, 'post_id', $error['post_id']);
                    //update_post_meta($val, 'mc_validation_error', $error);
                }
            }
            update_post_meta($val, 'mc_processed', true);
        }
    }
    return $validations;
}

function mc_citacion_button_url(){
    ob_start();
    if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'){
        $retorno = process_contents();
        if($retorno)
            $count_retorno = count($retorno);
        else
            $count_retorno = 0;
        ?>
        <div class="row">
            <button class="btn btn-primary" title="AA" style="cursor:pointer;">Resultado: <?php echo $count_retorno; ?></button>
        </div>
        <?php
    }
    else{
        ?>
        <form action="" method="post" class="row">
            <button class="btn btn-primary" title="<?php echo __('Process Contents','mc-citacion'); ?>" style="cursor:pointer;"><?php echo __('Process Contents','mc-citacion'); ?></button>
        </form>
        <?php
    }
    return ob_get_clean();
}
add_shortcode( 'mc-citacion-button-url', 'mc_citacion_button_url' );
/* SECTION PROCESS URLS */