<?php
/**
 * @package smiling_video
 */
/*
Plugin Name: Smiling Video
Plugin URI: http://www.smiling.video
Description: Smiling.Video offers a video player and gallery with a wide catalogue of premium videos proposed by leading Content Providers of all sectors: news, sport, movie and tv series, cinema, etc
Videos are continuously updated in many languages: italian, english, spanish, french
Version: 1.2.0
Author: Smiling.video
Author URI: http://smiling.video/
License: GPLv2 or later
Text Domain: smiling-video
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}
add_action( 'admin_notices', 'smiling_video_notice' );
add_action( 'admin_menu', 'smiling_video_menu' );
add_action( 'admin_enqueue_scripts', 'smiling_video_add_css' );
add_action( 'admin_enqueue_scripts', 'smiling_video_add_js' );
add_option( 'smiling_video_user', '');
add_option( 'smiling_video_password', '');
add_option( 'smiling_video_publishmode', '');
add_action( 'media_buttons', 'smiling_video_media_button');
add_action( 'wp_ajax_smiling_video_action', 'smiling_video_action' );
add_action( 'wp_ajax_smiling_video_gallery', 'smiling_video_gallery' );
add_action( 'wp_enqueue_scripts', 'smiling_video_enqueue_style' );
add_action( 'wp_enqueue_scripts', 'smiling_video_enqueue_script' );
add_shortcode( 'smiling_video', 'smiling_video_shortcode' );
add_action( 'save_post', 'smiling_video_autopublish' );
add_action( 'admin_init', 'child_plugin_has_parent_plugin' );

function child_plugin_has_parent_plugin() {
    if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
        add_action( 'admin_notices', 'child_plugin_notice' );

        deactivate_plugins( plugin_basename( __FILE__ ) ); 

        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
}

function child_plugin_notice(){
    ?><div class="error"><p>Sorry, but Smiling.Video requires Classic Editor to be installed and active.</p></div><?php
}

function smiling_video_menu() {
    add_menu_page( 'Smiling Video', 'Smiling Video', 'manage_options', 'smiling_video_home', 'smiling_video_home_page','dashicons-video-alt3');
}

function smiling_video_activate(){
    register_uninstall_hook( __FILE__, 'smiling_video_uninstall' );
}

register_activation_hook( __FILE__, 'smiling_video_activate' );
function smiling_video_uninstall(){
    delete_option( 'smiling_video_user', '');
    delete_option( 'smiling_video_password', ''); 
    delete_option( 'smiling_video_publishmode', '');
}

function smiling_video_home_page() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

        // See if the user has posted us some information
        // If they did, this hidden field will be set to 'Y'
        if( ! empty( $_POST ) && isset($_POST[ 'smiling_video_hidden' ]) && sanitize_text_field($_POST[ 'smiling_video_hidden' ]) == 'Y' ) {
            //if nonce is okay we can process user data
            if ( check_admin_referer( 'smiling_video_usersettings', 'smiling_video_usersettings_nonce' ) ) {
                // Read their posted value
                $opt_user = sanitize_text_field($_POST[ 'smiling_video_user' ]);
                $opt_pass = sanitize_text_field($_POST[ 'smiling_video_password' ]);
                $opt_publishmode = sanitize_text_field($_POST[ 'smiling_video_publishmode' ]);
                // Save the posted value in the database
                update_option( 'smiling_video_user', trim($opt_user) );
                update_option( 'smiling_video_password', trim($opt_pass) );
                
                if($opt_publishmode != 'Automatica' && $opt_publishmode != 'Manuale'){
                    $opt_publishmode = 'Manuale';  
                }
                update_option( 'smiling_video_publishmode', $opt_publishmode );

                // Put a "settings saved" message on the screens
                echo "<div class='updated'><p><strong> Configurazione salvata. </strong></p></div>";
            }
        }
        $user = get_option('smiling_video_user');
        $pass = get_option('smiling_video_password');
        $autopublish = get_option('smiling_video_publishmode');
        $def = new stdClass();
        if($autopublish == 'Automatica'){
            $def->auto = 'selected';
            $def->manu = '';
        }  
        else{
            $def->auto = '';
            $def->manu = 'selected';
        }

	    echo '<div class="wrap"><form name="form1" method="post" action="">';
        echo wp_nonce_field( 'smiling_video_usersettings', 'smiling_video_usersettings_nonce' );  
	    echo '<input type="hidden" name="smiling_video_hidden" value="Y">';
	    echo '<div><img id="logo_smiling_video"  onclick="window.open(\'http://smiling.video\')" src="'.plugins_url('logo.png', __FILE__).'"></div>';
	    echo '<h2>Benvenuto in Smiling.Video</h2>';
        echo '<p>Smiling.Video è la soluzione ideale per gli editori che cercano contenuti video di qualità da utilizzare liberamente. Smiling.Video mette a disposizione migliaia di video organizzati in varie categorie editoriali: cronaca, entertainment, cinema, sport, musica e tanto altro.</p>';
        echo '<h2>Configurazione iniziale Smiling.Video</h2>';
	    echo '<label>Per poter utilizzare il nostro Plugin è necessario possedere un account sulla piattaforma Smiling.Video. Per registrarti gratuitamente <a target="_blank"href="http://platform.smiling.video">clicca qui</a>.<br>Se invece possiedi già un account Smiling.Video ti basterà inserire le tue credenziali negli spazi sottostanti:</label><br><br>';
	    echo '<label>Username (come da registrazione su Smiling.Video)</label><br><input type="text" name="smiling_video_user"  value="'.$user.'" size="30"><br>';
	    echo '<label>Password (come da registrazione su Smiling.Video)</label><br><input type="password" name="smiling_video_password"  value="'.$pass.'" size="30"><br>';
        echo '<p>Se incontri problemi o il plugin non funziona contattaci allo +39 02 89054160 oppure invia una mail a <a href="mailto:support@smiling.video">support@smiling.video</a>.</p>';
        echo '<h2>Modalità di pubblicazione</h2>';
        echo '<p>Ora ti basterà indicare la modalità di pubblicazione che intendi utilizzare.</p>';
        echo '<p>Se sceglierai <b>pubblicazione automatica</b> il nostro sistema inserirà automaticamente un video all\'interno di ogni articolo sulla base dell\'argomento trattato, oppure sulla base dei criteri editoriali da te precedentemente impostati sulla piattaforma <a target="_blank"href="http://platform.smiling.video">https://platform.smiling.video</a>.</p>';
	    echo '<p>In caso di scelta di <b>pubblicazione manuale</b> il sistema suggerirà dall\'editor del testo il singolo video da inserire nell\'articolo in corso di redazione in due modalità.</p>';
        echo '<p><b>1.</b> Una volta indicata la parola chiave nel titolo bisogna cliccare il pulsante "video suggeriti". A quel punto Smiling.Video propone una lista di titoli correlati al titolo.</p>';
        echo '<p><b>2.</b> Oppure scegliendo il pulsante "tutti i video", si attiva l\'intero catalogo video (in ordine cronologico) all\'interno del quale si può scegliere tra le migliaia di video presenti nel database.</p>';
        echo '<br><label>La modalità di pubblicazione (automatica/manuale) potrà essere modificata in qualsiasi momento. </label><br><select name="smiling_video_publishmode"><option '.$def->manu.' value="Manuale">Manuale</option><option '.$def->auto.' value="Automatica">Automatica</option></select><br>';
	    echo '<p>Hai bisogno di più informazioni o una spiegazione più dettagliata? Non esitare a contattarci allo +39 02 89054160 oppure invia una mail a <a href="mailto:support@smiling.video">support@smiling.video</a>.</p>';
        echo '<p class="submit"><input type="submit" class="button-primary" value="'.__('Save Changes').'"></p>';
	    echo '</form>';
        echo '</div>';
}

function smiling_video_notice(){
    if ( current_user_can( 'publish_posts' ) )  {
        
        // See if the user has posted us some information on smiling_video form
        // If they did, this hidden field will be set to 'Y'
        if( ! empty( $_POST ) && isset($_POST[ 'smiling_video_hidden' ]) && sanitize_text_field($_POST[ 'smiling_video_hidden' ]) == 'Y' ) {
            //if nonce is okay we can process user data
            if ( check_admin_referer( 'smiling_video_usersettings', 'smiling_video_usersettings_nonce' ) ) {
                $user = sanitize_text_field($_POST[ 'smiling_video_user' ]);
                $pass = sanitize_text_field($_POST[ 'smiling_video_password' ]);
            }
        }
        else{
            $user = get_option('smiling_video_user');
            $pass = get_option('smiling_video_password');
        }

        if($user=='' || $pass==''){
            echo '<div class="notice notice-warning">';
            if ( current_user_can( 'manage_options' ) )  {
                echo '<p>Smiling Video: configurazione incompleta. <a href="'.admin_url( "admin.php").'?page=smiling_video_home">Vai alle impostazioni per configurare.</a></p>';
            }
            else{
                echo '<p>Smiling Video: configurazione incompleta. Contatta l\' Amministratore del sito.</p>';
            }
            echo '</div>'; 
        }
        
    }
}

function smiling_video_media_button() {
    if ( current_user_can( 'publish_posts' ) )  {
        echo '<a href="#" id="smiling_video_add_media" data-action="smiling_video_action" style="border-color:#f30000;background-color:#f30000; color:white;" class="button"><span class="dashicons dashicons-video-alt3" style="margin-top: 3px;"></span> Video Suggeriti</a>';
        echo '<a href="#" id="smiling_video_videogallery"  data-action="smiling_video_gallery" class="button button-primary"><span class="dashicons dashicons-video-alt3" style="margin-top: 3px;"></span> Tutti i Video</a>';
    }
}

function smiling_video_add_css() {
    wp_register_style( 'smiling_video_style', plugins_url('css/smiling_video.css?smver=1.1.9', __FILE__));
    wp_register_style( 'smiling_video_csscore', plugins_url('css/video-js-5.19.2.css?smver=1.1.9', __FILE__));
    wp_enqueue_style('smiling_video_style');
    wp_enqueue_style('smiling_video_csscore');
}

function smiling_video_add_js() {
    wp_register_script( 'smiling_video_script', plugins_url('js/smiling_video.js?smver=1.1.9', __FILE__), array( 'jquery' ) );
    wp_register_script( 'smiling_video_script_4hls', plugins_url('js/video-js-5.19.2.js?smver=1.1.9', __FILE__));
    wp_register_script( 'smiling_video_script_hls_min', plugins_url('js/videojs-contrib-hls.min.js', __FILE__));
    wp_enqueue_script('smiling_video_script');
    wp_enqueue_script('smiling_video_script_4hls');
    wp_enqueue_script('smiling_video_script_hls_min');
    
    wp_localize_script( 'smiling_video_script', 'ajax_object',
            array(  'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'security' => wp_create_nonce( "smiling_video_action_security" )
            ) 
    );
}

function smiling_video_action(){
    if ( !current_user_can( 'publish_posts' ) )  {
        wp_die( __( 'You do not have sufficient publish_post permissions to access this page.' ) ); 
    }
    
    if(!check_ajax_referer( 'smiling_video_action_security', 'security', false )){ 
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    $pagetitle = sanitize_text_field($_POST['pagetitle']);
    $pagecontent = sanitize_text_field($_POST['pagecontent']);

    $get_videos =  smiling_video_get_video_suggest($pagetitle,$pagecontent,12);
    $configs = $get_videos['configs'];
    $videos = $get_videos['videos'];
    $risposta = $get_videos['risposta'];
    if ($risposta->code =='OK'){
        if ($videos == FALSE || sizeof($videos)<= 0){
            $videos = [];
        }
        smiling_video_render_grid_notpl($videos);
    }
    else{
        if ($risposta->message == 'Authentication failed'){
            echo '<div class="notice notice-warning">';
            if ( current_user_can( 'manage_options' ) )  {
                echo '<p>Smiling Video: configurazione incompleta. <a href="'.admin_url( "admin.php").'?page=smiling_video_home">Vai alle impostazioni per configurare.</a></p>';
            }
            else{
                echo '<p>Smiling Video: configurazione incompleta. Contatta l\' Amministratore del sito.</p>';
            }
            echo '</div>';
        }
        else {
             print 'Errore richiesta smiling_video';
        print "<br>".($risposta->message);
        }

    }
    wp_die(); // this is required to terminate immediately and return a proper response
}

function smiling_video_gallery(){ 
    if ( !current_user_can( 'publish_posts' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) ); 
    }
    
    if(!check_ajax_referer( 'smiling_video_action_security', 'security', false )){ 
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    /*Prepare Inputs*/
        /*categories*/
            $categs = array();
            foreach($_POST['categs'] as $val){
                if(trim($val) != ''){ $categs[] = absint($val);}
            }
            $categs_serialized = implode(",", $categs);
        /*source*/
            $source = '';
            if(trim($_POST['source']) != ''){
                $source = absint($_POST['source']);
            }
        /*language*/
            $lang = '';
            if(trim($_POST['lang']) != ''){
                $lang = sanitize_text_field($_POST['lang']);
            }
        /*keywords*/
            $text = sanitize_text_field($_POST['text']);

    /*Get filtered videos list*/
        $get_videos =  smiling_video_get_video_list($categs_serialized,$source,$lang,$text); 
        $videos = $get_videos['videos'];
        $risposta = $get_videos['risposta'];
    if ($risposta->code =='OK'){
        if ($videos == FALSE || sizeof($videos)<= 0){
            $videos = [];
        }
        
        smiling_video_render_grid_list($videos,$categs,$source,$lang,$text);
    }
    else{
        if ($risposta->message == 'Authentication failed'){
        echo '<div class="notice notice-warning">';
        if ( current_user_can( 'manage_options' ) )  {
            echo '<p>Smiling Video: configurazione incompleta. <a href="'.admin_url( "admin.php").'?page=smiling_video_home">Vai alle impostazioni per configurare.</a></p>';
        }
        else{
            echo '<p>Smiling Video: configurazione incompleta. Contatta l\' Amministratore del sito.</p>';
        }echo '</div>';
        }
        else {
             print 'Errore richiesta smiling_video';
        print "<br>".($risposta->message);
        }
    }
    wp_die(); // this is required to terminate immediately and return a proper response
}

function smiling_video_render_grid_list($videos,$categs_in,$source_in,$lang_in,$text_in){

    $cats_obj = smiling_video_get_elements_from_endpoint("categoriesTree");
    $sources_obj = smiling_video_get_elements_from_endpoint("sourcesList");
    $langs_obj = smiling_video_get_elements_from_endpoint("languagesList");

    if ($cats_obj->result->code =='OK'){
        $cats_selecthtml = '<select id="categories" ><option value="">-Tutte-</option>';
        foreach($cats_obj->categories as $cat){
            $selected = in_array($cat->id, $categs_in) ? 'selected' : '';
            $cats_selecthtml .= '<option '.$selected.' value="'.$cat->id.'" style="font-weight:bold;text-transform:uppercase;">'.$cat->label.'</option>';
            foreach($cat->categories as $subcat){
                $selected = in_array($subcat->id, $categs_in) ? 'selected' : '';
                $cats_selecthtml .= '<option '.$selected.' value="'.$subcat->id.'">&nbsp;&nbsp;&bull;'.$subcat->label.'</option>';
            }
        }
        $cats_selecthtml .= '</select>';
    }
    if ($sources_obj->result->code =='OK'){
        $sources_selecthtml = '<select id="sources" ><option value="">-Tutte-</option>';
        foreach($sources_obj->sources as $source){
            $selected = ($source->id == $source_in) ? 'selected' : '';
            $sources_selecthtml .= '<option '.$selected.' value="'.$source->id.'" >'.$source->label.'</option>';
        }
        $sources_selecthtml .= '</select>';
    }
    if ($langs_obj->result->code =='OK'){
        $langs_selecthtml = '<select id="languages" ><option value="">-Tutte-</option>';
        foreach($langs_obj->languages as $lang){
            $selected = ($lang->code == $lang_in) ? 'selected' : '';
            $langs_selecthtml .= '<option '.$selected.' value="'.$lang->code.'" style="text-transform:capitalize;">'.$lang->label.'</option>';
        }
        $langs_selecthtml .= '</select>';
    }
    ?>
    <div class="container">
        <div class="row">
            <div class="padding015">
                <img onclick="window.open('http://smiling.video')" id="logo_smiling_video" src="<?=plugins_url('logo.png', __FILE__)?>">
            </div>
            <div class="padding015 search_box">
                
                <div  class="fleft ">
                    <label>Parole Chiave</label>
                    <input type="text" value="<?=$text_in?>" id="filtertext">
                </div>
                <div class="fleft padding010">
                    <label >Categorie</label>
                    <?=$cats_selecthtml?>
                </div>
                <div class="fleft padding10r">
                    <label >Fonte </label>
                    <?=$sources_selecthtml?>
                </div>
                <div class="fleft ">
                    <label >Lingua </label>
                    <?=$langs_selecthtml?>
                    <a href="" class="margin010 button" data-action="smiling_video_gallery" id="smiling_video_search">Ricerca</a> 
                    <br><br>
                </div>
            </div>
            
        </div>
        <div class="row">
        <?php
            foreach ($videos as $video):
                $id = ''; $class = ''; $type = 'video-mp4'; $script = '';
                $sourcetag = "<source src='".esc_url($video->videoUrl)."'>";
                if($video->videoType == 'hls'){
                    $id = "vid".$video->id;
                    $type = "video-hls";
                    $class = 'force-auto-dimension video-autowrapped'; //'video-js vjs-default-skin vjs-16-9';
                    $sourcetag = "<source src='".esc_url($video->videoUrl)."' type='application/x-mpegURL'>";
                }
        ?>
        <div class="single-box">
        <div class="content">
            <div class="video-box <?=$type?>">
                <div class="overlay-wrapper"></div>
                <div class="play"><span class="dashicons dashicons-video-alt3" ></span></div>
                 <video id="<?=$id?>" class="<?=$class?>" preload="none" poster="<?php echo esc_url($video->thumbnailUrl)?>">
                    <?=$sourcetag?>
                 </video>
            </div>
            <span class="smiling_video_data"><?=$video->uploadDate?></span>
            <h2 class="smiling_video_title"><?=$video->title?></h2>
            <textarea style="display:none;"><?php echo esc_textarea($video->snippet)?></textarea>
            <a href="#" class="button smiling_video_insert_snippet">Inserisci codice</a>
            <span id="_id" style="display:none;"><?=$video->id?></span> 
        </div>
        </div>

        <?php endforeach; ?>
            <?php if(sizeof($videos) <= 0){
                print "Nessun video trovato.";
            }?>
        </div>
        </div>
<?php
}
function smiling_video_render_grid_notpl($videos){

?>
    <div class="container">
        <div class="row">
            <div style="padding:0 15px;">
                <img onclick="window.open('http://smiling.video')" id="logo_smiling_video" src="<?=plugins_url('logo.png', __FILE__)?>">
            </div>
            <br>
        <?php
            foreach ($videos as $video):
                $id = ''; $class = ''; $type = 'video-mp4'; $script = '';
                $sourcetag = "<source src='".esc_url($video->videoUrl)."'>";
                if($video->videoType == 'hls'){
                    $id = "vid".$video->id;
                    $type = "video-hls";
                    $class = 'force-auto-dimension video-autowrapped'; //'video-js vjs-default-skin vjs-16-9';
                    $sourcetag = "<source src='".esc_url($video->videoUrl)."' type='application/x-mpegURL'>";
                }
        ?>
        <div class="single-box">
        <div class="content">
            <div class="video-box <?=$type?>">
                <div class="overlay-wrapper"></div>
                <div class="play"><span class="dashicons dashicons-video-alt3" ></span></div>
                 <video id="<?=$id?>" class="<?=$class?>" preload="none" poster="<?php echo esc_url($video->thumbnailUrl)?>">
                    <?=$sourcetag?>
                 </video>
            </div>
            <span class="smiling_video_data"><?=$video->uploadDate?></span>
            <h2 class="smiling_video_title"><?=$video->title?></h2>
            <textarea style="display:none;"><?php echo esc_textarea($video->snippet)?></textarea>
            <a href="#" class="button smiling_video_insert_snippet">Inserisci codice</a>
            <span id="_id" style="display:none;"><?=$video->id?></span>
        </div>
        </div>

        <?php endforeach; ?>
            <?php if(sizeof($videos) <= 0){
                print "Nessun video trovato.";
            }?>
        </div>
        </div>
<?php
}
function smiling_video_get_elements_from_endpoint($endpoint){
    $url = 'http://ctrlapi.videos.smiling.video/SmilingVideoCMS/boapi/v3_1/'.$endpoint.'.json';
    $user = get_option('smiling_video_user');
    $pass = get_option('smiling_video_password');

    $postdata = http_build_query(
        array(
            'Smiling-Api-Username' => $user,
            'Smiling-Api-Password' => $pass
        )
    );
    $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );
    $context  = stream_context_create($opts);

    $result = file_get_contents($url, false, $context);

    $obj = json_decode($result);
    return $obj;
}

function smiling_video_get_video_list($categs,$source,$lang,$text){
    $url = 'http://ctrlapi.videos.smiling.video/SmilingVideoCMS/boapi/v3_1/videoList.json';
    $user = get_option('smiling_video_user');
    $pass = get_option('smiling_video_password');
    
    $postdata = http_build_query(
        array(
            'Smiling-Api-Username' => $user,
            'Smiling-Api-Password' => $pass,
            'filterCateg' => $categs,
            'source' => $source,
            'lang' => $lang,
            'filterText' => $text
        )
    );
    $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );
    $context  = stream_context_create($opts);

    $result = file_get_contents($url, false, $context);

    $obj = json_decode($result);
    $lista['risposta'] = $obj->result;
    $lista['configs'] = $obj->configs;
    $lista['videos'] = $obj->videos;
    
    return $lista;

}
function smiling_video_get_video_suggest($pagetitle,$pagecontent,$maxresult){
    $url = 'http://ctrlapi.videos.smiling.video/SmilingVideoCMS/boapi/v3_1/videoSuggest.json';
    $user = get_option('smiling_video_user');
    $pass = get_option('smiling_video_password');

    $postdata = http_build_query(
        array(
            'Smiling-Api-Username' => $user,
            'Smiling-Api-Password' => $pass,
            'pageContent' => $pagecontent,
            'pageTitle' => $pagetitle,
            'maxResults' => $maxresult
        )
    );
    $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );
    $context  = stream_context_create($opts);

    $result = file_get_contents($url, false, $context);

    $obj = json_decode($result);
    $lista['risposta'] = $obj->result;
    $lista['configs'] = $obj->configs;
    $lista['videos'] = $obj->videos;
    return $lista;

}
function smiling_video_get_videorandom($pagetitle,$pagecontent){
    $url = 'http://ctrlapi.videos.smiling.video/SmilingVideoCMS/boapi/v3_1/oneRandomVideo.json';
    $user = get_option('smiling_video_user');
    $pass = get_option('smiling_video_password');

    $postdata = http_build_query(
        array(
            'Smiling-Api-Username' => $user,
            'Smiling-Api-Password' => $pass
        )
    );
    $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );
    $context  = stream_context_create($opts);

    $result = file_get_contents($url, false, $context);

    $obj = json_decode($result);
    $lista['risposta'] = $obj->result;
    $lista['configs'] = $obj->configs;
    $lista['videos'] = $obj->videos;
    return $lista;

}

//[smiling_video id="0000"][/smiling_video] //id not used
function smiling_video_shortcode( $atts, $content = null ) {
    return $content;
}

function smiling_video_enqueue_style() {
	wp_enqueue_style( 'smiling_video_csscore', plugins_url('css/video-js-5.19.2.css?smver=1.1.9', __FILE__), false );
	wp_enqueue_style( 'smiling_video_cssima', plugins_url('css/videojs.ima.css?smver=1.1.9', __FILE__), false );
	wp_enqueue_style( 'smiling_video_cssskin', plugins_url('css/skin-smiling-player-multicp-2.0.css?smver=1.1.9', __FILE__), false );
	wp_enqueue_style( 'smiling_video_cssgoogleapisfont', plugins_url('css/Quicksand.css', __FILE__), false );
}

function smiling_video_enqueue_script() {
    wp_enqueue_script( 'smiling_video_jscommon', plugins_url('js/sm-common-func.js?smver=1.1.9', __FILE__), false );
}

function smiling_video_autopublish($post_id){
    // If this is a revision, get real post ID
//	if ( $parent_id = wp_is_post_revision( $post_id ) ){ 
//		$post_id = $parent_id;
//        }
    $autopublish = get_option('smiling_video_publishmode');
    if($autopublish == 'Automatica'){
        $post_title = get_post_field('post_title', $post_id);
        $post_content = get_post_field('post_content', $post_id);
        
        if( !has_shortcode( $post_content, 'smiling_video' ) && trim($post_title) != '' ) {
	// The content has a [smiling_video] short code, so this check returned true.
            $addtopost =  '';
            $get_videos = smiling_video_get_videorandom($post_title,$post_content); 
            $videos = $get_videos['videos'];
            $risposta = $get_videos['risposta'];
            if ($risposta->code =='OK'){
                if ($videos == FALSE || sizeof($videos)<= 0){
                    $videos = [];
                }
                foreach($videos as $video){
                    $addtopost = '<br>[smiling_video id="'.$video->id.'"]<br>'.$video->snippet.'[/smiling_video]';
                    break;
                }
            }
            // unhook this function so it doesn't loop infinitely 
            remove_action( 'save_post', 'smiling_video_autopublish' );
            // update the post, which calls save_post again
            wp_update_post( array(  'ID' => $post_id, 
                                    'post_content' => $post_content.$addtopost,
                            ) );
            // re-hook this function
            add_action( 'save_post', 'smiling_video_autopublish' );
        }
    }
}