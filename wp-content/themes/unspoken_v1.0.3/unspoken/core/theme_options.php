<?php

// Admin styles & scripts
add_action( 'admin_init', 'unspoken_admin_init' );
function unspoken_admin_init() {
   wp_register_style( 'unspoken_admin_css', get_bloginfo( 'template_url' ) . '/core/css/theme_options.css' );
   wp_register_script( 'unspoken_admin_js', get_bloginfo( 'template_url' ) . '/core/js/theme_options.js' );
}

function unspoken_admin_styles() {
   wp_enqueue_style('postbox');
   wp_enqueue_style('media-upload');
   wp_enqueue_style('thickbox');
   wp_enqueue_style( 'unspoken_admin_css' );
}
function unspoken_admin_js() {
   wp_enqueue_script('media-upload');
   wp_enqueue_script('thickbox');
   wp_enqueue_script( 'unspoken_admin_js' );
}

// Add admin pages
function unspoken_options_page() {
    add_theme_page( 'Theme Options', 'Theme Options', 'edit_theme_options', basename(__FILE__), 'unspoken_options' );
    add_action( 'admin_print_styles', 'unspoken_admin_styles' );
    add_action( 'admin_enqueue_scripts', 'unspoken_admin_js' );
}
add_action('admin_menu', 'unspoken_options_page');

/***** Options page *****/

function unspoken_options() {
    if ( isset( $_POST['update_options'] ) ) { unspoken_options_update(); }  //check options update
	?>
    <div class="wrap unspoken">
        <div id="icon-options-general" class="icon32"><br /></div>
		<h2><?php _e('Unspoken Theme Options - w p l o c k e r . c o m', 'unspoken'); ?></h2>

        <form method="post" action="">
            <fieldset>
                <input type="hidden" name="update_options" value="true"/>

                <div id="poststuff" class="metabox-holder">
                    <div class="meta-box-sortables">

                        <!-- General -->
                        <div class="postbox">
                            <div class="handlediv" title="<?php _e('Click to toggle'); ?>">
                                <br/>
                            </div>
                            <h3 class="hndle"><span><?php _e('General Options', 'unspoken'); ?></span></h3>

                            <div class="inside">

                                <h3 class="first"><?php _e('Posts view', 'unspoken'); ?></h3>
                                <table class="form-table">
                                    <tr>
                                        <th scope="row"><label><?php _e('Choose your default post view:', 'unspoken'); ?></label></th>
                                        <td>
                                            <input type="radio" class="radio" name="unspoken_view_type" value="0" checked="checked" /> <label><?php _e('List', 'unspoken'); ?></label><br />
                                            <input type="radio" class="radio" name="unspoken_view_type" value="1" <?php echo (get_option('unspoken_view_type')) ? 'checked="checked"' : ''; ?>/> <label><?php _e('Grid', 'unspoken'); ?></label>
                                        </td>
                                    </tr>
                                </table>

                                <h3><?php _e('Post Info', 'unspoken'); ?></h3>
                                <table class="form-table">
                                    <tr>
                                        <th scope="row"><?php _e('Choose info to display:', 'unspoken'); ?></th>
                                        <td>
                                            <input type="checkbox" class="checkbox" id="unspoken_postedon_date" name="unspoken_postedon_date" <?php echo (get_option('unspoken_postedon_date')) ? 'checked="checked"' : ''; ?> /> <label for="unspoken_postedon_date"><?php _e('Date', 'unspoken'); ?></label><br />
                                            <input type="checkbox" class="checkbox" id="unspoken_postedon_cat" name="unspoken_postedon_cat" <?php echo (get_option('unspoken_postedon_cat')) ? 'checked="checked"' : ''; ?> /> <label for="unspoken_postedon_cat"><?php _e('Categories', 'unspoken'); ?></label><br />
                                            <input type="checkbox" class="checkbox" id="unspoken_postedon_comm" name="unspoken_postedon_comm" <?php echo (get_option('unspoken_postedon_comm')) ? 'checked="checked"' : ''; ?> /> <label for="unspoken_postedon_comm"><?php _e('Number of comments', 'unspoken'); ?></label><br />
                                            <input type="checkbox" class="checkbox" id="unspoken_postedon_author" name="unspoken_postedon_author" <?php echo (get_option('unspoken_postedon_author')) ? 'checked="checked"' : ''; ?> /> <label for="unspoken_postedon_author"><?php _e('Author', 'unspoken'); ?></label>
                                        </td>
                                    </tr>
                                </table>

                                <h3><?php _e('Page Navigation', 'unspoken'); ?></h3>
                                <table class="form-table">
                                    <tr>
                                        <th scope="row"><label><?php _e('Choose page navigation type:', 'unspoken'); ?></label></th>
                                        <td>
                                            <input class="radio" type="radio" name="unspoken_pagination_mode" value="0" <?php echo (get_option('unspoken_pagination_mode') == 0) ? 'checked="checked"' : ''; ?>/> <label><?php _e('Standard', 'unspoken'); ?> </label><br />
                                            <input class="radio" type="radio" name="unspoken_pagination_mode" value="1" <?php echo (get_option('unspoken_pagination_mode') == 1) ? 'checked="checked"' : ''; ?>/> <label><?php _e('Standard + WP Page-Navi support', 'unspoken'); ?></label><br />
                                            <input class="radio" type="radio" name="unspoken_pagination_mode" value="2" <?php echo (get_option('unspoken_pagination_mode') == 2) ? 'checked="checked"' : ''; ?>/> <label><?php _e('AJAX-load', 'unspoken'); ?></label><br />
                                            <input class="radio" type="radio" name="unspoken_pagination_mode" value="3" <?php echo (get_option('unspoken_pagination_mode') == 3) ? 'checked="checked"' : ''; ?>/> <label><?php _e('InfiniteScroll', 'unspoken'); ?></label>
                                        </td>
                                    </tr>
                                </table>

                                <h3><?php _e('Highlights Slider Settings', 'unspoken'); ?></h3>
                                <table class="form-table">
                                    <tr>
                                        <th scope="row"><label for="unspoken_slider_excl"><?php _e('Exclude slider posts from other modules:', 'unspoken'); ?></label></th>
                                        <td>
                                            <input type="checkbox" id="unspoken_slider_excl" class="checkbox" name="unspoken_slider_excl" <?php echo (get_option('unspoken_slider_excl')) ? 'checked="checked"' : ''; ?>/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label for="unspoken_slider_auto"><?php _e('Scroll slider automatically:', 'unspoken'); ?></label></th>
                                        <td>
                                            <input type="checkbox" id="unspoken_slider_auto" class="checkbox" name="unspoken_slider_auto" <?php echo (get_option('unspoken_slider_auto')) ? 'checked="checked"' : ''; ?>/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label for="unspoken_slider_delay"><?php _e('Scroll delay (ms):', 'unspoken'); ?></label></th>
                                        <td>
                                            <input type="text" size="5" id="unspoken_slider_delay" name="unspoken_slider_delay" value="<?php echo (get_option('unspoken_slider_delay') > 0) ? get_option('unspoken_slider_delay') : 7000 ; ?>"/>
                                        </td>
                                    </tr>
                                </table>

                                <h3><?php _e('Footer Text', 'unspoken'); ?></h3>
                                <table class="form-table">
                                    <tr>
                                        <th scope="row"><?php _e('Insert text here:'); ?></th>
                                        <td>
                                            <label for="unspoken_footer_text"><input type="text" name="unspoken_footer_text" id="unspoken_footer_text" size="76" value="<?php echo get_option('unspoken_footer_text'); ?>"/></label><br/>
                                        </td>
                                    </tr>
                                </table>

                                <h3><?php _e('Google Analytics', 'unspoken'); ?></h3>
                                <table class="form-table">
                                    <tr>
                                        <th scope="row"><label for="unspoken_ga"><?php _e('Insert Analytics or any other statistics system code:'); ?></label></th>
                                        <td><textarea name="unspoken_ga" id="unspoken_ga" cols="48" rows="10"><?php echo get_option('unspoken_ga'); ?></textarea><br/><span class="description"><?php _e('It will be inserted in the bottom of the page before closing </body> tag.', 'unspoken'); ?></span></td>
                                    </tr>
                                </table>

                                <p><input type="submit" value="<?php _e('Save Changes', 'unspoken'); ?>" class="button button-primary"/></p>
                            </div>
                        </div>
                        <!-- /General -->

                        <!-- Styling -->
                        <div class="postbox">
                            <div class="handlediv" title="<?php _e('Click to toggle'); ?>">
                                <br/>
                            </div>
                            <h3 class="hndle"><span><?php _e('Styling Options', 'unspoken'); ?></span></h3>

                            <div class="inside">

                                <h3 class="first"><?php _e('Skins', 'unspoken'); ?></h3>
                                <table class="form-table">
                                    <tr>
                                        <th scope="row"><?php _e('Select a skin:'); ?></th>
                                        <td>
                                            <select name="unspoken_skin" id="unspoken_skin">
                                                <option <?php if (get_option('unspoken_skin') == 'default') { echo 'selected="selected"'; } ?> value="default"><?php _e('Default', 'unspoken'); ?></option>
                                                <option <?php if (get_option('unspoken_skin') == 'black') { echo 'selected="selected"'; } ?> value="black"><?php _e('Black', 'unspoken'); ?></option>
                                                <option <?php if (get_option('unspoken_skin') == 'newsum') { echo 'selected="selected"'; } ?> value="newsum"><?php _e('Newsum', 'unspoken'); ?></option>
                                                <option <?php if (get_option('unspoken_skin') == 'brockman') { echo 'selected="selected"'; } ?> value="brockman"><?php _e('Brockman', 'unspoken'); ?></option>
                                                <option <?php if (get_option('unspoken_skin') == 'oldpaper') { echo 'selected="selected"'; } ?> value="oldpaper"><?php _e('Old Paper', 'unspoken'); ?></option>
                                                <option <?php if (get_option('unspoken_skin') == 'nobby') { echo 'selected="selected"'; } ?> value="nobby"><?php _e('Nobby', 'unspoken'); ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <h3><?php _e('Main Font', 'unspoken'); ?></h3>
                                <table class="form-table">
                                    <tr>
                                        <th scope="row"><?php _e('Select a font:'); ?></th>
                                        <td>
                                            <select name="unspoken_font" id="unspoken_font">
                                                <option <?php if (get_option('unspoken_font') == 'arial') { echo 'selected="selected"'; } ?> value="arial"><?php _e('Arial', 'unspoken'); ?></option>
                                                <option <?php if (get_option('unspoken_font') == 'helvetica') { echo 'selected="selected"'; } ?> value="helvetica"><?php _e('Helvetica', 'unspoken'); ?></option>
                                                <option <?php if (get_option('unspoken_font') == 'times') { echo 'selected="selected"'; } ?> value="times"><?php _e('Times New Roman', 'unspoken'); ?></option>
                                                <option <?php if (get_option('unspoken_font') == 'courier') { echo 'selected="selected"'; } ?> value="courier"><?php _e('Courier New', 'unspoken'); ?></option>
                                                <option <?php if (get_option('unspoken_font') == 'verdana') { echo 'selected="selected"'; } ?> value="verdana"><?php _e('Verdana', 'unspoken'); ?></option>
                                                <option <?php if (get_option('unspoken_font') == 'georgia') { echo 'selected="selected"'; } ?> value="georgia"><?php _e('Georgia', 'unspoken'); ?></option>
                                                <option <?php if (get_option('unspoken_font') == 'trebuchet') { echo 'selected="selected"'; } ?> value="trebuchet"><?php _e('Trebuchet MS', 'unspoken'); ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <h3><?php _e('Logo', 'unspoken'); ?></h3>
                                <script type="text/javascript">
                                    (function($) {
                                        $(function() {
                                            $('#unspoken_logo_top_button, #unspoken_logo_bottom_button').click(function() {
                                                formfield = $(this).prev().attr('name');
                                                tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
                                                return false;
                                            });

                                            window.send_to_editor = function(html) {
                                                imgurl = $('img', html).attr('src');
                                                $('#' + formfield).val(imgurl);
                                                tb_remove();
                                            }
                                        })
                                    })(jQuery)
                                </script>
                                <table class="form-table">
                                    <tr>
                                        <th scope="row"><?php _e('Top logo URL:'); ?></th>
                                        <td>
                                            <label for="unspoken_logo_top"><input type="text" name="unspoken_logo_top" id="unspoken_logo_top" size="76" value="<?php echo get_option('unspoken_logo_top'); ?>"/> <input id="unspoken_logo_top_button" class="button" type="button" value="Upload" /></label><br/>
                                            <?php _e( 'Max width 405 px', 'unspoken' ); ?>
                                            <br/>
                                            <?php
                                                if ( get_option('unspoken_logo_top') ) :
                                                $size = getimagesize(get_option('unspoken_logo_top'));
                                            ?>
                                                <p><img src="<?php echo get_option('unspoken_logo_top'); ?>" <?php echo $size[3]; ?> alt=""/></p>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php _e('Bottom logo URL'); ?></th>
                                        <td>
                                            <label for="unspoken_logo_bottom"><input type="text" name="unspoken_logo_bottom" id="unspoken_logo_bottom" size="76" value="<?php echo get_option('unspoken_logo_bottom'); ?>"/> <input id="unspoken_logo_bottom_button" class="button" type="button" value="Upload" /></label><br />
                                            <?php _e('Max width 202 px', 'unspoken'); ?>
                                            <br/>
                                            <?php
                                                if ( get_option('unspoken_logo_bottom') ) :
                                                $size = getimagesize(get_option('unspoken_logo_bottom'));
                                            ?>
                                                <p><img src="<?php echo get_option('unspoken_logo_bottom'); ?>" <?php echo $size[3]; ?> alt=""/></p>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php _e('Text logo:'); ?></th>
                                        <td>
                                            <label for="unspoken_logo_text"><input type="text" name="unspoken_logo_text" id="unspoken_logo_text" size="76" value="<?php echo get_option('unspoken_logo_text'); ?>"/></label><br /><span
                                                class="description"><?php _e('Text logo will be displayed both on top and on the bottom.', 'unspoken'); ?></span>
                                        </td>
                                    </tr>
                                </table>

                                <h3><?php _e('Favicon'); ?></h3>
                                <table class="form-table">
                                    <tr>
                                        <th scope="row"><label for="unspoken_favicon"><?php _e('Insert your icon URL here:'); ?></label></th>
                                        <td><input type="text" name="unspoken_favicon" id="unspoken_favicon" size="76" value="<?php echo get_option('unspoken_favicon'); ?>"/></td>
                                    </tr>
                                </table>

                                <h3><?php _e('Your Styles', 'unspoken'); ?></h3>
                                <table class="form-table">
                                    <tr>
                                        <th scope="row"><label for="unspoken_styles"><?php _e('Insert your styles here. They are of top priority against the template styles.'); ?></label></th>
                                        <td><textarea name="unspoken_styles" id="unspoken_styles" cols="48" rows="15"><?php echo get_option('unspoken_styles'); ?></textarea></td>
                                    </tr>
                                </table>

                                <p><input type="submit" value="<?php _e('Save Changes', 'unspoken'); ?>" class="button button-primary"/></p>
                            </div>
                        </div>
                        <!-- /Styling -->

                        <!-- Single -->
                        <div class="postbox">
                            <div class="handlediv" title="<?php _e('Click to toggle'); ?>">
                                <br/>
                            </div>
                            <h3 class="hndle"><span><?php _e('Single Page Options', 'unspoken'); ?></span></h3>

                            <div class="inside">

                                <h3 class="first"><?php _e('Author Info Module', 'unspoken'); ?></h3>
                                <table class="form-table">
                                    <tr>
                                        <th scope="row"><?php _e('Switch off', 'unspoken'); ?></th>
                                        <td>
                                            <input type="checkbox" class="checkbox" id="unspoken_author" name="unspoken_author" <?php echo (get_option('unspoken_author')) ? 'checked="checked"' : ''; ?> />
                                        </td>
                                    </tr>
                                </table>

                                <h3><?php _e('Related Posts', 'unspoken'); ?></h3>
                                <table class="form-table">
                                    <tr>
                                        <th scope="row"><?php _e('Choose related posts type:', 'unspoken'); ?></th>
                                        <td>
                                            <input type="checkbox" class="checkbox" id="unspoken_rel_type1" name="unspoken_rel_type1" <?php echo (get_option('unspoken_rel_type1')) ? 'checked="checked"' : ''; ?> /> <label for="unspoken_rel_type1"><?php _e('Block of three posts with image thumbnails below it', 'unspoken'); ?></label><br />
                                            <input type="checkbox" class="checkbox" id="unspoken_rel_type2" name="unspoken_rel_type2" <?php echo (get_option('unspoken_rel_type2')) ? 'checked="checked"' : ''; ?> /> <label for="unspoken_rel_type2"><?php _e('List of five posts on left-side column ', 'unspoken'); ?></label>
                                        </td>
                                    </tr>
                                </table>

                                <h3><?php _e('Social Services', 'unspoken'); ?></h3>
                                <table class="form-table">
                                    <tr>
                                        <th scope="row"><?php _e('Choose module type:', 'unspoken'); ?></th>
                                        <td>
                                            <input type="checkbox" class="checkbox" id="unspoken_share_type1" name="unspoken_share_type1" <?php echo (get_option('unspoken_share_type1')) ? 'checked="checked"' : ''; ?> /> <label for="unspoken_share_type1"><?php _e('Block of five popular social networks + RSS on the left side of the post', 'unspoken'); ?></label><br />
                                            <input type="checkbox" class="checkbox" id="unspoken_share_type2" name="unspoken_share_type2" <?php echo (get_option('unspoken_share_type2')) ? 'checked="checked"' : ''; ?>/> <label for="unspoken_share_type2"><?php _e('Floating block of share buttons on the left side of the post', 'unspoken'); ?></label>
                                        </td>
                                    </tr>
                                </table>

                                <h3><?php _e('Facebook Like', 'unspoken'); ?></h3>
                                <table class="form-table">
                                    <tr>
                                        <th scope="row"><?php _e('Display Facebook “Like” button in the end of each post:', 'unspoken'); ?></th>
                                        <td>
                                            <input type="checkbox" class="checkbox" id="unspoken_like" name="unspoken_like" <?php echo (get_option('unspoken_like')) ? 'checked="checked"' : ''; ?> />
                                        </td>
                                    </tr>
                                </table>

                                <h3><?php _e('Advertising area in the left column of a post', 'unspoken'); ?></h3>
                                <table class="form-table">
                                    <tr>
                                        <th scope="row"><label for="unspoken_sidebar_ad"><?php _e('Insert code<br />(max width 140 px):'); ?></label></th>
                                        <td><textarea name="unspoken_sidebar_ad" id="unspoken_sidebar_ad" cols="48" rows="5"><?php echo get_option('unspoken_sidebar_ad'); ?></textarea></td>
                                    </tr>
                                </table>

                                <p><input type="submit" value="<?php _e('Save Changes', 'unspoken'); ?>" class="button button-primary"/></p>
                            </div>
                        </div>
                        <!-- /Single -->

                        <!-- Templates -->
                        <div class="postbox">
                            <div class="handlediv" title="<?php _e('Click to toggle'); ?>">
                                <br/>
                            </div>
                            <h3 class="hndle"><span><?php _e('Templates Options', 'unspoken'); ?></span></h3>

                            <div class="inside">

                                <h3 class="first"><?php _e('Magazine Template', 'unspoken'); ?></h3>
                                <table class="form-table">
                                    <tr>
                                        <th scope="row"><label for="unspoken_mag_use"><?php _e('Use magazine template:', 'unspoken'); ?></label></th>
                                        <td>
                                            <input type="checkbox" id="unspoken_mag_use" class="checkbox" name="unspoken_mag_use" <?php echo (get_option('unspoken_mag_use')) ? 'checked="checked"' : ''; ?>/><br /><span
                                                class="description"><?php _e('If this option is on, there will be additional image of 620х380 size created for the slider.', 'unspoken'); ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label for="unspoken_mag_auto"><?php _e('Scroll the slider automatically:', 'unspoken'); ?></label></th>
                                        <td>
                                            <input type="checkbox" id="unspoken_mag_auto" class="checkbox" name="unspoken_mag_auto" <?php echo (get_option('unspoken_mag_auto')) ? 'checked="checked"' : ''; ?>/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label for="unspoken_mag_delay"><?php _e('Scroll delay (ms):', 'unspoken'); ?></label></th>
                                        <td>
                                            <input type="text" size="5" id="unspoken_mag_delay" name="unspoken_mag_delay" value="<?php echo (get_option('unspoken_mag_delay') > 0) ? get_option('unspoken_mag_delay') : 7000 ; ?>"/>
                                        </td>
                                    </tr>
                                </table>

                                <h3><?php _e('Contact form template', 'unspoken'); ?></h3>
                                <table class="form-table">
                                    <tr>
                                        <th scope="row"><?php _e('Recipient(s) Email address:', 'unspoken'); ?></th>
                                        <td>
                                            <label for="unspoken_cf_email"><input type="text" id="unspoken_cf_email" name="unspoken_cf_email" size="76" value="<?php echo (get_option('unspoken_cf_email')) ? get_option('unspoken_cf_email') : ''; ?>"/><br/>
                                            <?php _e('You can specify multiple emails delimited with comma', 'unspoken'); ?>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php _e('Subject prefix:', 'unspoken'); ?></th>
                                        <td>
                                            <label for="unspoken_cf_prefix"><input type="text" id="unspoken_cf_prefix" name="unspoken_cf_prefix" size="76" value="<?php echo (get_option('unspoken_cf_prefix')) ? get_option('unspoken_cf_prefix') : ''; ?>"/><br/>
                                            <?php _e('i.e. Message from my site:', 'unspoken'); ?>
                                            </label>
                                        </td>
                                    </tr>
                                </table>

                                <p><input type="submit" value="<?php _e('Save Changes', 'unspoken'); ?>" class="button button-primary"/></p>
                            </div>
                        </div>
                        <!-- /Templates -->    

                    </div>
                </div>

            </fieldset>
        </form>
    </div>
<?php
}

function unspoken_options_update() {
    update_option('unspoken_slider_excl', isset($_POST['unspoken_slider_excl']) ? (bool) $_POST['unspoken_slider_excl'] : false);
    update_option('unspoken_slider_auto', isset($_POST['unspoken_slider_auto']) ? (bool) $_POST['unspoken_slider_auto'] : false);
    if ( isset($_POST['unspoken_slider_delay']) ) update_option('unspoken_slider_delay', ( trim($_POST['unspoken_slider_delay'] > 0 )) ? trim($_POST['unspoken_slider_delay']) : 7000 );
    update_option('unspoken_postedon_date', isset($_POST['unspoken_postedon_date']) ? (bool) $_POST['unspoken_postedon_date'] : false);
    update_option('unspoken_postedon_cat', isset($_POST['unspoken_postedon_cat']) ? (bool) $_POST['unspoken_postedon_cat'] : false);
    update_option('unspoken_postedon_comm', isset($_POST['unspoken_postedon_comm']) ? (bool) $_POST['unspoken_postedon_comm'] : false);
    update_option('unspoken_postedon_author', isset($_POST['unspoken_postedon_author']) ? (bool) $_POST['unspoken_postedon_author'] : false);
    update_option('unspoken_view_type', $_POST['unspoken_view_type']);
    update_option('unspoken_author', isset($_POST['unspoken_author']) ? (bool) $_POST['unspoken_author'] : false);
    update_option('unspoken_rel_type1', isset($_POST['unspoken_rel_type1']) ? (bool) $_POST['unspoken_rel_type1'] : false);
    update_option('unspoken_rel_type2', isset($_POST['unspoken_rel_type2']) ? (bool) $_POST['unspoken_rel_type2'] : false);
    update_option('unspoken_share_type1', isset($_POST['unspoken_share_type1']) ? (bool) $_POST['unspoken_share_type1'] : false);
    update_option('unspoken_share_type2', isset($_POST['unspoken_share_type2']) ? (bool) $_POST['unspoken_share_type2'] : false);
    update_option('unspoken_like', isset($_POST['unspoken_like']) ? (bool) $_POST['unspoken_like'] : false);
    update_option('unspoken_pagination_mode', $_POST['unspoken_pagination_mode']);
    update_option('unspoken_footer_text', strip_tags($_POST['unspoken_footer_text']));
    update_option('unspoken_sidebar_ad', stripslashes_deep($_POST['unspoken_sidebar_ad']));
    update_option('unspoken_ga', stripslashes_deep($_POST['unspoken_ga']));
    update_option('unspoken_skin', $_POST['unspoken_skin']);
    update_option('unspoken_font', $_POST['unspoken_font']);
    update_option('unspoken_logo_top', strip_tags(($_POST['unspoken_logo_top'])));
    update_option('unspoken_logo_bottom', strip_tags(($_POST['unspoken_logo_bottom'])));
    update_option('unspoken_logo_text', trim(strip_tags(($_POST['unspoken_logo_text']))));
    update_option('unspoken_favicon', strip_tags(($_POST['unspoken_favicon'])));
    update_option('unspoken_styles', strip_tags(($_POST['unspoken_styles'])));
    update_option('unspoken_mag_use', isset($_POST['unspoken_mag_use']) ? (bool) $_POST['unspoken_mag_use'] : false);
    update_option('unspoken_mag_auto', isset($_POST['unspoken_mag_auto']) ? (bool) $_POST['unspoken_mag_auto'] : false);
    if ( isset($_POST['unspoken_mag_delay']) ) update_option('unspoken_mag_delay', ( trim($_POST['unspoken_mag_delay'] > 0 )) ? trim($_POST['unspoken_mag_delay']) : 7000 );
    if ( isset($_POST['unspoken_cf_email']) ) update_option('unspoken_cf_email', trim($_POST['unspoken_cf_email']));
    if ( isset($_POST['unspoken_cf_prefix']) ) update_option('unspoken_cf_prefix', trim($_POST['unspoken_cf_prefix']));
}
