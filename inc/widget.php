<?php

define('DLSL_VERSION_2', '2');
define('DLSL_VERSION_3', '3');

class Digitallylux_StylstLooks_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'dlst_digitallylux_widget',
            __( 'Stylst Looks Viewer' ),
            array( 'description' => __( 'Display the Stylst Looks Viewer' ) )
        );
    }

    function form( $instance ) {
        if ( $instance ) {
            $title = esc_attr( $instance['title'] );
        }
        else {
            $title = __( 'Shop now!' );
        }
?>

        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
        </p>

<?php
    }

    function update( $new_instance, $old_instance ) {
        $instance['title'] = strip_tags( $new_instance['title'] );
        return $instance;
    }

    function widget( $args, $instance ) {
        echo $args['before_widget'];

        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'];
            echo esc_html( $instance['title'] );
            echo $args['after_title'];
        }

        dlst_digitallylux_slider_widget();

        echo $args['after_widget'];
    }
}

function dlst_digitallylux_slider_widget()
{
    $api_host = "bluesalt";

    if ( !$api_host )
    {
        return;
    }

    if (is_single()) {
        $post_id = get_the_ID();
    }
    else {
        $post_id = null;
    }

    try {
        $json = dlst_wphttp_fetch_json(dlst_build_url(null, 'post_id', $post_id, $api_host, DLSL_VERSION_3), $api_host);
    }
    catch(Exception $e) {
        return '';
    }

    $html = '';

    $options = get_option('dlst_digitallylux_options');
    if (!empty($json['slide']))
    {

        $s = $json['slide'];

        $image_tag = '<a style="width:100%;height:250px;display:block;background:url(' . $s['image'] . ') no-repeat center center; background-size: cover;" href="' . $json['slide']['image_link'] . '" rel="nofollow" target="_blank"></a>';
        $title_tag = '<a class="slider-title" href="'. $s['link'] .'" target="_blank">'. $s['title'] .'</a>';
        $comment_tag = '<span class="slider-comment">'. $s['comment'] .'</span>';
        $links_tag = '';

        if (count($s['links']) > 0)
        {
            foreach ($s['links'] as $link)
            {
                if ($link['anchor'] == "" )
                {
                    $links_tag .= '<div><a href="'. $link['url'] .'" target="_blank" class="slider-comment" title="'. $link['title'] .'">'. $link['title'] .'</a></div>';
                }
                else
                {
                    $links_tag .= '<div>' . $link['anchor_prefix'] . ' <a href="'. $link['url'] .'" target="_blank" class="slider-comment" title="'. $link['anchor'] .'">'. $link['anchor'] . "</a> " . $link['anchor_suffix'] . '</a></div>';
                }
            }
        }

        $brand_name = "SHOPBOP";
        $html .= '<div class="slider-container1">' .
                        '<div class="dlst_title_wrap"><a href="http://www.stylst.com" rel="nofollow" target="_blank"><img style="max-width:100%; display: block; height: auto; padding-bottom: 5px; margin: 0 auto;" class="brand-logo" src="' . DLSL_PLUGIN_URL . 'css/stylst.png" alt="ShopBop" /></a></div>' .
                        $image_tag .
                        '<div class="overlay-wrapper">' .
                            $links_tag .
                        '</div>' .
                 '</div>';


    }

    echo $html;
    // echo '<img class="sb_pixel_img" src="http://shopbop.sp1.convertro.com/view/vt/v1/shopbop/1/cvo.gif?cvosrc=sponsored%20bloggers.' . $options["name"] . '.sb-km" />';
}

function dlst_digitallylux_register_widget() {
    register_widget('Digitallylux_StylstLooks_Widget');
}

add_action( 'widgets_init', 'dlst_digitallylux_register_widget' );

function dlst_build_url($site_id, $key, $value, $api_host, $version = '1')
{
    if (dlst_in_development())
    {
        $slider_url = 'http://localhost:4567';
    }
    else
    {
        $slider_url = "https://{$api_host}.stylst.com";
    }

    if (!isset($version)) {
        $version = '1';
    }

    if (!isset($site_id)) {
        $site_url = site_url();
        $url = $slider_url."/links{$version}.json?site_url={$site_url}";
    }
    else {
        $url = $slider_url."/links{$version}.json?site_id=".$site_id;
    }

    if (!is_front_page()) {
        if (isset($value)) {
            $url .= '&'.$key.'='.urlencode($value);
            if ($key == 'post_id') {
                $post_date = get_post_time();
                $url .= '&post_date='.urlencode($post_date);
            }
        }
    }

    if (dlst_in_development())
    {
        print "-- REQUEST {$url}";
    }

    return $url;
}

function dlst_wphttp_fetch_json($url, $api_host) {
    $ch = curl_init();

    $args = array(
        'headers' => array(
            'timeout' => '10',
            'Authorization' => 'Basic ' . base64_encode( "admin:blue$@lt17" )
        )
    );
    $response = wp_remote_get( $url, $args );
    $body = wp_remote_retrieve_body( $response );

    if ($body == false) {
        $m = curl_error($ch);
        throw new Exception($m);
    }
    curl_close($ch);
    return json_decode($body, true);
}

function dlst_call_api($url, $method, $data, $auth_key) {
    $ch = curl_init();
    $args = array(
        'method' => $method,
        'headers' => array(
            'timeout' => '10',
            'Content-Type' => 'application/json',
            'Authorization' => $auth_key
        ),
        'body' => $data
    );

    try {
      if ($method == "GET") {
        $response = wp_remote_get( $url, $args );
      } else if ($method == "POST") {
        $response = wp_remote_post( $url, $args );
      }

      if ( is_wp_error( $response ) ) {
         $error_message = $response->get_error_message();
         echo "Something went wrong: $error_message";
         return $error_message;
      }

      $body = wp_remote_retrieve_body( $response );

      if ($body == false) {
          $m = curl_error($ch);
          throw new Exception($m);
      }
    } catch (Exception $e) {
      return $e;
    }

    curl_close($ch);
    return json_decode($body, true);
}

?>
