<?php
  /*
    Plugin Name: Cozimo
    Version: 0.5
    Plugin URI: http://www.cozimo.com/wordpress/
    Description: Real-time Media Collablogging for the masses!
    Author: Juan Pablo Di Lelle, Gijsbert de Haan @ Cozimo
    Author URI: http://www.cozimo.com/

    Copyright (c) 2008 Cozimo Solutions

    This program is distributed in the hope that it will be useful, under the MIT License.
    See http://www.opensource.org/licenses/mit-license.php for more details.

    Includes the amazing <swfobject> javascript library for embedding Flash content.
    http://code.google.com/p/swfobject/
    <swfobject> (previously SWFFix) is the latest and greatest in flash embedding technology,
    spawning from the shared efforts of giants Geoff Stearns, Michael Williams, and Bobby van der Sluis.
    Check it out!

    The initial version of this PHP file was inspired from the super
    flash video player plugin by Joshua Eldridge.
    http://wordpress.org/extend/plugins/flash-video-player/
    Joshua rocks!

  */


$mediaID = 0;
$siteURL = get_option('siteurl');

function Cozimo_Parse($content) {
  $content = preg_replace_callback("/\[cozimo ([^]]*)\/\]/i", "Cozimo_Render", $content);
  return $content;
}

function Cozimo_Render($matches) {

  if ( is_feed() ) {
    return "[Powered by Cozimo!]";
  }

  global $mediaID, $siteURL;

  $output = '';
  $matches[1] = str_replace(array('&#8221;','&#8243;'), '', $matches[1]);
  preg_match_all('/(\w*)=(.*?) /i', $matches[1], $attributes);
  $arguments = array();

  foreach ( (array) $attributes[1] as $key => $value ) {
    $arguments[$value] = $attributes[2][$key];
  }

  if ( !array_key_exists('filename', $arguments) ) {
    return '<div style="background-color:#f99; padding:10px;">Error: Required parameter "filename" is missing!</div>';
    exit;
  }

  $options = get_option('CozimoSettings');

  /* Override default option values */
  if ( array_key_exists('width', $arguments) ) {
    $options[0][1]['value'] = $arguments['width'];
  }
  $playerWidth = $options[0][1]['value'];

  if ( array_key_exists('height', $arguments) ) {
    $options[0][2]['value'] = $arguments['height'];
  }
  $playerHeight = $options[0][2]['value'];

  if ( array_key_exists('bgcolor', $arguments) ) {
    $options[0][3]['value'] = $arguments['bgcolor'];
  }
  $bgcolor = $options[0][3]['value'];
  
  if ( array_key_exists('type', $arguments) ) {
    $options[0][4]['value'] = $arguments['type'];
  }
  $mimetype = $options[0][4]['value'];
	
  if(strpos($arguments['filename'], 'http://') !== false || strpos($arguments['filename'], 'rtmp://') !== false) {
    // FIXME what's the freakin' PHP way of doing this shit automatically?
    $arguments['filename'] = strip_tags($arguments['filename']);
    $arguments['filename'] = str_replace('&#038;','&',$arguments['filename']);
    $arguments['filename'] = str_replace('&amp;','&',$arguments['filename']);
    $arguments['filename'] = str_replace('&#63;','?',$arguments['filename']);
    $arguments['filename'] = str_replace('&#61;','=',$arguments['filename']);
    $arguments['filename'] = str_replace('&#8211;','--',$arguments['filename']);
    $arguments['filename'] = str_replace('&#45;','-',$arguments['filename']);
  } else {
    $arguments['filename'] = $siteURL . '/' . $arguments['filename'];
  }
  $arguments['filename'] = urlencode($arguments['filename']);

  $output .= '<script type="text/javascript">' . "\n";
  $output .= 'var flashvars = {};' . "\n";
  $output .= 'var params = {};' . "\n";
  $output .= 'var attributes = {};' . "\n";

  $output .= 'flashvars.roles="guest";' . "\n";
  $accessMode = 'RO';
  if ( is_user_logged_in() ) {
    global $user_login;
    global $user_identity;
    global $user_level;
    get_currentuserinfo();
    $userID = $user_login;
    $userName = $user_identity;
    $output .= 'flashvars.userID="'.$userID.'";' . "\n";
    $output .= 'flashvars.userName="'.$userName.'";' . "\n";
    if ( $user_level == "10" ) {
      $accessMode = 'RW';
    }
  }
  else {
    $publicTagging = $options[2][2]['value'];
    if ( $publicTagging == 'true') {
      $accessMode = 'RW';
    }
  }
  $output .= 'flashvars.accessMode="'.$accessMode.'";' . "\n";
  $contentURL = $arguments['filename'];
  $output .= 'flashvars.contentURL="'.$contentURL.'";' . "\n";
  $output .= 'flashvars.mimetype="'.$mimetype.'";' . "\n";
  $permalink = get_permalink();
  $output .= 'flashvars.sessionID="wordpress.'.$permalink.'.'.$contentURL.'";' . "\n";

  $borderColor = $options[1][1]['value'];
  $linkColor = $options[1][2]['value'];
  $fontColor = $options[1][3]['value'];
  $fontFamily = $options[1][4]['value'];
  $frameColor = $options[1][5]['value'];

  $output .= 'flashvars.borderColor="'.$borderColor.'";' . "\n";
  $output .= 'flashvars.frameColor="'.$frameColor.'";' . "\n";
  $output .= 'flashvars.linkColor="'.$linkColor.'";' . "\n";
  $output .= 'flashvars.fontColor="'.$fontColor.'";' . "\n";
  $output .= 'flashvars.fontFamily="'.$fontFamily.'";' . "\n";
  $output .= 'flashvars.tlAlignment=1;' . "\n";

  $cozimoID = 'cozimo_'.$mediaID;
  $output .= 'flashvars.iceID="'.$cozimoID.'";' . "\n";

  $output .= 'attributes.id="fo_'.$cozimoID.'";' . "\n";
  $output .= 'attributes.name="fo_'.$cozimoID.'";' . "\n";
  $output .= 'attributes.styleclass="cozimoPlayer";' . "\n";

  $output .= 'params.menu="false";' . "\n";
  $output .= 'params.allowfullscreen="true";' . "\n";
  $output .= 'params.bgcolor="'.$bgcolor.'";' . "\n";

  $swfpath = $siteURL . '/wp-content/plugins/cozimo/cozimo.swf';
  $expressinstallpath = $options[2][1]['value'];
  $output .= 'swfobject.embedSWF("'.$swfpath.'", "'.$cozimoID.'", "'.$playerWidth.'", "'.$playerHeight.'", "9.0.0", "'.$expressinstallpath.'", flashvars, params, attributes);' . "\n";
  $output .= '</script>' . "\n\n";

  $output .= '<span id="'.$cozimoID.'" class="cozimoPlayer">' . "\n";
  $output .= '<small><font size="2" face="courier">Loading Real-Time Interactive Collaboration Environment...</font></small>' . "\n";
  $output .= '</span>' . "\n";

  $output .= '<span class="cozimoControls">' . "\n";
  $output .= '<span class="iceInterface"' . "\n";
  $output .= '      rel="'.$cozimoID.'"' . "\n";
  $output .= '      id="'.$cozimoID.'.iceToggleTools"' . "\n";
  $output .= '      title="Click to toggle the Tools panel ON and OFF"' . "\n";
  $output .= '      >Tools</span>' . "\n";
  $output .= '<span class="iceInterface"' . "\n";
  $output .= '      rel="'.$cozimoID.'"' . "\n";
  $output .= '      id="'.$cozimoID.'.iceToggleChat"' . "\n";
  $output .= '      title="Click to toggle the Chat panel ON and OFF"' . "\n";
  $output .= '      >Chat</span>' . "\n";
  $output .= '<span class="iceInterface"' . "\n";
  $output .= '      rel="'.$cozimoID.'"' . "\n";
  $output .= '      id="'.$cozimoID.'.iceToggleCollaborators"' . "\n";
  $output .= '      title="Click to toggle the Collaborators panel ON and OFF"' . "\n";
  $output .= '      >Collaborators</span>' . "\n";

  $output .= '<span class="iceInterface"' . "\n";
  $output .= '      rel="'.$cozimoID.'"' . "\n";
  $output .= '      id="'.$cozimoID.'.iceClearAll"' . "\n";
  $output .= '      title="Click to remove all your markups"' . "\n";
  $output .= '      >Clear</span>' . "\n";

  // Managers in WP have a user level of 10.
  if ( $user_level == "10" ) {
    $output .= '<span class="iceInterface"' . "\n";
    $output .= '      rel="'.$cozimoID.'"' . "\n";
    $output .= '      id="'.$cozimoID.'.iceResetAll"' . "\n";
    $output .= '      title="Click to remove all the markups"' . "\n";
    $output .= '      >Reset</span>' . "\n";
  }

  $output .= '<span class="iceInterface invisible"' . "\n";
  $output .= '      rel="'.$cozimoID.'"' . "\n";
  $output .= '      id="'.$cozimoID.'.iceTogglePresenterMode"' . "\n";
  $output .= '      title="Click to turn Sync mode on or off, or to take over sync"' . "\n";
  $output .= '      >Sync: OFF</span>' . "\n";

  $output .= '</span>' . "\n";

  $logoURL = $siteURL . '/wp-content/plugins/cozimo/poweredByCozimo.png';
  $output .= '<a href="http://www.cozimo.com/"><img style="border: 0; margin:auto; display: block; padding: 20px;" alt="Powered by Cozimo" src="'.$logoURL.'"/></a>' . "\n";

  $mediaID++;
  return $output;
}

function Cozimo_AddOptionsPage() {
  add_options_page('Cozimo', 'Cozimo', '8', 'cozimo.php', 'Cozimo_Options');
}

function Cozimo_Options() {
  $message = '';
  $g = array(0=>"Basic", 1=>"Appearance", 2=>"Advanced");

  $options = get_option('CozimoSettings');

  // Handle POST request.
  if ($_POST) {
    for($i=0; $i<count($options);$i++) {
      foreach( (array) $options[$i] as $key=>$value) {
        // Handle Checkboxes that don't send a value in the POST.
        if($value['type'] == 'cb' && !isset($_POST[$options[$i][$key]['name']])) {
          $options[$i][$key]['value'] = 'false';
        }
        if($value['type'] == 'cb' && isset($_POST[$options[$i][$key]['name']])) {
          $options[$i][$key]['value'] = 'true';
        }
        // Handle all other changed values.
        if(isset($_POST[$options[$i][$key]['name']]) && $value['type'] != 'cb') {
          $options[$i][$key]['value'] = $_POST[$options[$i][$key]['name']];
        }
      }
    }
    update_option('CozimoSettings', $options);
    $message = '<div class="updated"><p><strong>Options saved.</strong></p></div>';	
  }

  // Render the options page.
  echo '<div class="wrap">';
  echo '<h2>Cozimo Options</h2>';
  echo $message;
  echo '<form method="post" action="options-general.php?page=cozimo.php">';
  echo '<p class="submit"><input type="submit" method="post" value="Update Options &raquo;"></p>';

  echo "<p>Welcome to the Cozimo Plugin options panel.";
  echo "<p>Here you can set the default values for your site, such as size, colors and fonts.</p>";
  echo "<p>To reset the default values, simply deactivate and reactivate the Cozimo Plugin.</p>";
  echo "<p>Thank you for using Cozimo.</p><br/>";

  foreach( (array) $options as $key=>$value) {
    echo '<fieldset class="options">';
    echo '<legend>' . $g[$key] . '</legend>';
    echo '<table class="optiontable">';
    foreach( (array) $value as $setting) {
      echo '<tr><th scope="row">' . $setting['display'] . '</th><td>';
      if($setting['type'] == 'tx') {
        echo '<input type="text" name="' . $setting['name'] . '" value="' . $setting['value'] . '" />';
      } elseif ($setting['type'] == 'cb') {
        echo '<input type="checkbox" class="check" name="' . $setting['name'] . '" ';
        if($setting['value'] == 'true') {
          echo 'checked="checked"';
        }
        echo ' />';
      }
      echo '</td>';
      if ($setting['blurb']) {
	echo '<td>';
	echo $setting['blurb'];
	echo '</td>';
      }
      echo '</tr>';
    }
    echo '</table>';
    echo '</fieldset>';
  }

  echo '<p class="submit"><input type="submit" method="post" value="Update Options &raquo;"></p>';
  echo '</form>';
  global $siteURL;
  echo '<div><a href="http://www.cozimo.com/" alt="Cozimo"><img style="display: block;border:0;padding:20px;" src="'.$siteURL.'/wp-content/plugins/cozimo/poweredByCozimo.gif"/></a></div>';
  echo '</div>';
}

function Cozimo_Head() {
  global $siteURL;
  $swfobjectPath = $siteURL . '/wp-content/plugins/cozimo/swfobject.js';
  echo '<script type="text/javascript" src="' . $swfobjectPath . '"></script>' . "\n";
  $jsPath = $siteURL . '/wp-content/plugins/cozimo/cozimo.js';
  echo '<script type="text/javascript" src="' . $jsPath . '"></script>' . "\n";
  $cssPath = $siteURL . '/wp-content/plugins/cozimo/cozimo.css';
  echo '<link rel="stylesheet" href="'.$cssPath.'" type="text/css" media="screen" />';
}

add_action('wp_head', 'Cozimo_Head');

function Cozimo_Footer() {
  global $siteURL;
  $buttonJsPath = $siteURL . '/wp-content/plugins/cozimo/cozimo-button.js';
  echo '<script type="text/javascript" src="' . $buttonJsPath . '"></script>' . "\n";
}

add_action('admin_footer', 'Cozimo_Footer');

function Cozimo_LoadDefaults() {
  global $siteURL;
  $f = array();
	
  // Basic Settings.

  $f[0][1]['name'] = 'width';
  $f[0][1]['display'] = 'Player Width';
  $f[0][1]['blurb'] = 'Defines the width of the Cozimo player. You can use pixels, em\'s or percent values. This option can be overriden per instance by using the "width" attribute in the Cozimo tag.';
  $f[0][1]['type'] = 'tx';
  $f[0][1]['value'] = '100%';

  $f[0][2]['name'] = 'height';
  $f[0][2]['display'] = 'Player Height';
  $f[0][2]['blurb'] = 'Defines the height of the Cozimo player. You can use pixels, em\'s or percent values. This option can be overriden per instance by using the "height" attribute in the Cozimo tag.';
  $f[0][2]['type'] = 'tx';
  $f[0][2]['value'] = '500px';

  $f[0][3]['name'] = 'backcolor';
  $f[0][3]['display'] = 'Background Color';
  $f[0][3]['blurb'] = 'Defines the background color of the Cozimo player. Accepts HEX values, such as #FF0000.';
  $f[0][3]['type'] = 'tx';
  $f[0][3]['value'] = '#FFFFFF';

  $f[0][4]['name'] = 'mimetype';
  $f[0][4]['display'] = 'Content Type';
  $f[0][4]['blurb'] = 'Defines the default content-type. Accepts mime-types such as "image/jpg" or "video/flv". This option can be overriden per instance by using the "type" attribute in the Cozimo tag.';
  $f[0][4]['type'] = 'tx';
  $f[0][4]['value'] = '';

  // Appearance.

  $f[1][1]['name'] = 'borderColor';
  $f[1][1]['display'] = 'Border Color';
  $f[1][1]['blurb'] = 'Defines the border color of panels.';
  $f[1][1]['type'] = 'tx';
  $f[1][1]['value'] = '#DDDDDD';

  $f[1][2]['name'] = 'linkColor';
  $f[1][2]['display'] = 'Link Color';
  $f[1][2]['blurb'] = 'Defines the color of links and active components. Accepts HEX values, such as #DD0021.';
  $f[1][2]['type'] = 'tx';
  $f[1][2]['value'] = '#ee6c0b';

  $f[1][3]['name'] = 'fontColor';
  $f[1][3]['display'] = 'Font Color';
  $f[1][3]['blurb'] = 'Defines the font\'s color. Accepts HEX values, such as #AA45CC.';
  $f[1][3]['type'] = 'tx';
  $f[1][3]['value'] = '#000000';

  $f[1][4]['name'] = 'fontFamily';
  $f[1][4]['display'] = 'Font Family';
  $f[1][4]['blurb'] = 'Defines the font\'s family. Values are case sensitive, so use "Courrier", and not "courier".';
  $f[1][4]['type'] = 'tx';
  $f[1][4]['value'] = 'Arial, sans-serif';

  $f[1][5]['name'] = 'frameColor';
  $f[1][5]['display'] = 'Frame Color';
  $f[1][5]['blurb'] = 'Defines the color of the content frame within the player.';
  $f[1][5]['type'] = 'tx';
  $f[1][5]['value'] = '#FFFFFF';

  // Advanced Settings.

  $f[2][1]['name'] = 'expressInstallLocation';
  $f[2][1]['display'] = 'Express Install Location';
  $f[2][1]['blurb'] = 'Defines the path to an alternate SWF file used by the Express Install mechanism. Don\'t change this unless you know what you are doing.';
  $f[2][1]['type'] = 'tx';
  $f[2][1]['value'] = $siteURL . '/wp-content/plugins/cozimo/expressInstall.swf';

  $f[2][2]['name'] = 'publicTagging';
  $f[2][2]['display'] = 'Public Tagging';
  $f[2][2]['blurb'] = 'Defines whether viewers can leave notes and markup content. Managers can always leave notes.';
  $f[2][2]['type'] = 'cb';
  $f[2][2]['value'] = 'true';

  return $f;
}

function Cozimo_Activate() {
  update_option('CozimoSettings', Cozimo_LoadDefaults());
}

register_activation_hook(__FILE__,'Cozimo_Activate');

function Cozimo_Deactivate() {
  delete_option('CozimoSettings');
}

register_deactivation_hook(__FILE__,'Cozimo_Deactivate');

add_filter('the_content', 'Cozimo_Parse');

add_action('admin_menu', 'Cozimo_AddOptionsPage');

?>
