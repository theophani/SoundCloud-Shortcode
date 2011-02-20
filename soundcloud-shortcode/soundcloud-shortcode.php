<?php
/*
Plugin Name: SoundCloud Shortcode
Plugin URI: http://www.soundcloud.com
Description: SoundCloud Shortcode. Usage in your posts: [soundcloud]http://soundcloud.com/TRACK_PERMALINK[/soundcloud] . Works also with set or group instead of track. You can provide optional parameters height/width/params as follows [soundcloud height="82" params="auto_play=true"]http....
Version: 1.2
Author: Johannes Wagener <johannes@soundcloud.com>
Author URI: http://johannes.wagener.cc
*/

/*
SoundCloud Shortcode (Wordpress Plugin)
Copyright (C) 2009 Johannes Wagener
Options support added by Tiffany Conroy <tif@tif.ca>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

add_filter( "pre_kses", "soundcloud_reverse_shortcode" );
function soundcloud_reverse_shortcode_preg_replace_callback( $a ) {
       $pattern = '/([a-zA-Z0-9\-_%=&]*)&?url=([^&]+)&?([a-zA-Z0-9\-_%&=]*)/';
       preg_match( $pattern, str_replace( "&amp;", "&", $a[3] ), $params );
       return '[soundcloud width="' . esc_attr( $a[1] ) . '" height="' .
              esc_attr( $a[2] ) . '" params="' . esc_attr( $params[1] . $params[3] ) .
              '" url="' . urldecode( $params[2] ) . '"]';
}
function soundcloud_reverse_shortcode( $content ) {
       $pattern = '/<object.*width="([\d]+%?)".*height="([\d]+%?)".*src="http:\/\/.*soundcloud\.com\/player.swf\?(.*)".*<\/object>( <span[^>]*>.*<\/span>|)/U';
       $pattern_ent = htmlspecialchars( $pattern, ENT_NOQUOTES );
       if ( preg_match( $pattern_ent, $content ) ) {
              return preg_replace_callback( $pattern_ent, 'soundcloud_reverse_shortcode_preg_replace_callback', $content );
       } else {
              return preg_replace_callback( $pattern, 'soundcloud_reverse_shortcode_preg_replace_callback', $content );
       }
}

add_shortcode( "soundcloud", "soundcloud_shortcode" );
function soundcloud_shortcode( $atts, $url='' ) {
       if ( empty( $url ) ) {
              extract(shortcode_atts( array(
                     'url' => '',
                     'params' => soundcloud_build_params_string(),
                     'height' => '',
                     'width'  => ''
              ), $atts ) );
       } else {
              extract(shortcode_atts(array(
                     'params' => soundcloud_build_params_string(),
                     'height' => '',
                     'width'  => ''
              ), $atts ) );
       }

       $encoded_url = urlencode( $url );

       if ( $url = parse_url( $url ) ){
              $splitted_url = split( "/", $url['path'] );
              $media_type = $splitted_url[count($splitted_url) - 2];

              if ( $height == '' ) {
                      if ( $media_type == "groups" || $media_type == "sets" ){
                             $height = get_option('soundcloud_player_height_multi');
                             if (!$height || $height == '') $height = '255';
                      } else {
                             $height = get_option('soundcloud_player_height', '81');
                             if (!$height || $height == '') $height = '81';
                      }
              }

              if ( $width == '' ) {
                      $width = get_option('soundcloud_player_width');
                      if (!$width || $width == '') $width =  '100%';
              }

              $player_params = "url=$encoded_url&g=1&$params";

              preg_match('/(.+\.)?(((staging|sandbox)-)?soundcloud\.com)/', $url['host'], $matches);
              $player_host = "player." . $matches[2];

              return "<object height=\"" . esc_attr( $height ) . "\" width=\"" .
                     esc_attr( $width ) . "\"><param name=\"movie\" value=\"http://" .
                     esc_attr( $player_host ) . "/player.swf?" . esc_attr( $player_params ) .
                     "\"></param><param name=\"allowscriptaccess\"
                     value=\"always\"></param><embed allowscriptaccess=\"always\" height=\"" .
                     esc_attr( $height ) . "\" src=\"http://" . esc_attr( $player_host ) .
                     "/player.swf?" . esc_attr( $player_params ) .
                     "\" type=\"application/x-shockwave-flash\" width=\"" .
                     esc_attr( $width ) . "\"> </embed> " .
                     "<a href='http://soundcloud.com" . $url['path'] .
                     "' target='_blank'>Listen on SoundCloud</a>" .
                     " </object>";
      }
}


// Add settings link on plugin page
function soundcloud_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=soundcloud-shortcode">Settings</a>';
  array_unshift($links, $settings_link);
  return $links; 
}
add_filter("plugin_action_links_".plugin_basename(__FILE__), 'soundcloud_settings_link' );

add_action('admin_menu', 'soundcloud_shortcode_options_menu');

function soundcloud_shortcode_options_menu() {
       add_options_page('SoundCloud Options', 'SoundCloud', 'manage_options', 'soundcloud-shortcode', 'soundcloud_shortcode_options');
       add_action( 'admin_init', 'register_soundcloud_settings' );
}

function register_soundcloud_settings() {
       // register our settings
       register_setting( 'soundcloud-settings', 'soundcloud_player_height' );
       register_setting( 'soundcloud-settings', 'soundcloud_player_height_multi' );
       register_setting( 'soundcloud-settings', 'soundcloud_player_width ' );
       register_setting( 'soundcloud-settings', 'soundcloud_auto_play' );
       register_setting( 'soundcloud-settings', 'soundcloud_show_comments ' );
       register_setting( 'soundcloud-settings', 'soundcloud_color' );
       register_setting( 'soundcloud-settings', 'soundcloud_theme_color' );
}

function soundcloud_build_params_string() {
       $params  = '';
       $params .=  'auto_play='     . get_option( 'soundcloud_auto_play', '' );
       $params .= '&show_comments=' . get_option( 'soundcloud_show_comments ', '' );
       $params .= '&color='         . get_option( 'soundcloud_color', '' );
       $params .= '&theme_color='   . get_option( 'soundcloud_theme_color', '' );
       return $params;
}

function soundcloud_shortcode_options() {
       if (!current_user_can('manage_options'))  {
              wp_die( __('You do not have sufficient permissions to access this page.') );
       }

?>
<div class="wrap">
<h2>SoundCloud Shortcode Default Settings</h2>
<p>These settings will become the new defaults used by the SoundCloud Shortcode throughout your blog.</p>
<p>You can always override these settings on a per-shortcode basis. Setting the 'params' attribute in a shortcode overrides all these defaults combined.</p>

<form method="post" action="options.php">
    <?php settings_fields( 'soundcloud-settings' ); ?>
    <table class="form-table">

        <tr valign="top">
        <th scope="row">Player Height for Tracks</th>
        <td>
          <input type="text" name="soundcloud_player_height" value="<?php echo get_option('soundcloud_player_height'); ?>" /> (no unit, or %)<br />
          Leave blank to use the default, 81 (pixels).
        </td>
        </tr>

        <tr valign="top">
        <th scope="row">Player Height for Groups/Sets</th>
        <td>
          <input type="text" name="soundcloud_player_height_multi" value="<?php echo get_option('soundcloud_player_height_multi'); ?>" /> (no unit, or %)<br />
          Leave blank to use the default, 225 (pixels).
        </td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Player Width</th>
        <td>
          <input type="text" name="soundcloud_player_width" value="<?php echo get_option('soundcloud_player_width'); ?>" /> (no unit, or %)<br />
          Leave blank to use the default, 100%.
        </td>
        </tr>


        <tr valign="top">
        <th scope="row">Current Default 'params'</th>
        <td>
            <?php echo soundcloud_build_params_string(); ?>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row">Auto Play</th>
        <td>
             <label for="auto_play_none"  style="margin-right: 1em;"><input type="radio" id="auto_play_none"  name="soundcloud_auto_play" value=""      <?php if (get_option('soundcloud_auto_play') == '')      echo 'checked'; ?> />Default</label>
             <label for="auto_play_true"  style="margin-right: 1em;"><input type="radio" id="auto_play_true"  name="soundcloud_auto_play" value="true"  <?php if (get_option('soundcloud_auto_play') == 'true')  echo 'checked'; ?> />True</label>
             <label for="auto_play_false" style="margin-right: 1em;"><input type="radio" id="auto_play_false" name="soundcloud_auto_play" value="false" <?php if (get_option('soundcloud_auto_play') == 'false') echo 'checked'; ?> />False</label>
        </td>
        </tr>

        <tr valign="top">
        <th scope="row">Show Comments</th>
        <td>
             <label for="show_comments_none"  style="margin-right: 1em;"><input type="radio" id="show_comments_none"  name="soundcloud_show_comments" value=""      <?php if (get_option('soundcloud_show_comments') == '')      echo 'checked'; ?> />Default</label>
             <label for="show_comments_true"  style="margin-right: 1em;"><input type="radio" id="show_comments_true"  name="soundcloud_show_comments" value="true"  <?php if (get_option('soundcloud_show_comments') == 'true')  echo 'checked'; ?> />True</label>
             <label for="show_comments_false" style="margin-right: 1em;"><input type="radio" id="show_comments_false" name="soundcloud_show_comments" value="false" <?php if (get_option('soundcloud_show_comments') == 'false') echo 'checked'; ?> />False</label>
        </tr>

        <tr valign="top">
        <th scope="row">Color</th>
        <td>
          <input type="text" name="soundcloud_color" value="<?php echo get_option('soundcloud_color'); ?>" /> (color hex code e.g. ff6699)<br />
          Defines the color to paint the play button, waveform and selections.
        </td>
        </tr>

        <tr valign="top">
        <th scope="row">Theme Color</th>
        <td>
          <input type="text" name="soundcloud_theme_color" value="<?php echo get_option('soundcloud_theme_color'); ?>" /> (color hex code e.g. ff6699)<br />
          Defines the background color of the player.
        </td>
        </tr>

    </table>

    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>
<?php

}

?>