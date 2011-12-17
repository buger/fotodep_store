<?php
/*
Plugin Name: Advanced Excerpt
Plugin URI: http://sparepencil.com/code/advanced-excerpt/
Description: Several improvements over WP's default excerpt. The size of the excerpt can be limited using character or word count, and HTML markup is not removed.
Version: 3.1
Author: Bas van Doren
Author URI: http://sparepencil.com/

Copyright 2007 Bas van Doren

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if(!class_exists('AdvancedExcerpt')) :
class AdvancedExcerpt
{
    // Plugin configuration
    var $name;
    var $text_domain;
    var $mb;
    
    var $default_options;
    var $custom_options;
    
    // Tricky variable
    var $skip_next_call;
    
    // Reference arrays
    // Basic HTML tags (determines which tags are in the checklist)
    var $options_basic_tags = array(
        'a', 'abbr', 'acronym',
        'b', 'big', 'blockquote', 'br',
        'center', 'cite', 'code',
        'dd', 'del', 'div', 'dl', 'dt',
        'em',
        'form',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr',
        'i', 'img', 'ins',
        'li',
        'ol',
        'p', 'pre',
        'q',
        's', 'small', 'span', 'strike', 'strong', 'sub', 'sup',
        'table', 'td', 'th', 'tr',
        'u', 'ul'
    );
    
    // HTML tags allowed in <body>
    // <style> is <head>-only, but usage is often non-standard, so it's included here
    var $options_body_tags = array(
        'a', 'abbr', 'acronym', 'address', 'applet', 'area',
        'b', 'bdo', 'big', 'blockquote', 'br', 'button',
        'caption', 'center', 'cite', 'code', 'col', 'colgroup',
        'dd', 'del', 'dfn', 'dir', 'div', 'dl', 'dt',
        'em',
        'fieldset', 'font', 'form', 'frame', 'frameset',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr',
        'i', 'iframe', 'img', 'input', 'ins', 'isindex',
        'kbd',
        'label', 'legend', 'li',
        'map', 'menu',
        'noframes', 'noscript',
        'object', 'ol', 'optgroup', 'option',
        'p', 'param', 'pre',
        'q',
        's', 'samp', 'script', 'select', 'small', 'span', 'strike', 'strong', 'style', 'sub', 'sup',
        'table', 'tbody', 'td', 'textarea', 'tfoot', 'th', 'thead', 'tr', 'tt',
        'u', 'ul',
        'var'
    );
    
    // (not used) HTML tags which may have content that should not be considered actual text
    // TODO: Implement a way to remove tag + content if tag is not allowed (low priority)
    var $non_text_tags = array(
        'applet', 'noframes', 'noscript', 'object', 'select', 'script', 'style'
    );
    

    function AdvancedExcerpt()
    {
        $this->name = strtolower(get_class($this));
        $this->text_domain = $this->name;
        $this->skip_next_call = false;
        $this->charset = get_bloginfo('charset');
        
        $this->load_options();
        
        // Carefully support multibyte languages
        if(extension_loaded('mbstring') && function_exists('mb_list_encodings'))
            $this->mb = in_array($this->charset, mb_list_encodings());
        
        load_plugin_textdomain($this->text_domain, PLUGINDIR . '/advanced-excerpt/');
        
        register_activation_hook(__FILE__, array(&$this, 'install'));
        
        //register_deactivation_hook(__FILE__, array(&$this, 'uninstall'));
        
        add_action('admin_menu', array(&$this, 'add_pages'));
        
        // Replace the default filter (see /wp-includes/default-filters.php)
        remove_filter('get_the_excerpt', 'wp_trim_excerpt');
        add_filter('get_the_excerpt', array(&$this, 'filter'));
    }
    
    function __construct()
    {
        self::AdvancedExcerpt();
    }
    
    function filter($text)
    {
        // Merge custom parameters
        if(is_array($this->custom_options))
            $r = array_merge($this->default_options, $this->custom_options);
        else
            $r = $this->default_options;
        
        extract($r, EXTR_SKIP);
        
        // Only make the excerpt if it does not exist or 'No Custom Excerpt' is set to true
        if('' == $text || $no_custom)
        {
            // Get the full content and filter it
            $text = get_the_content('');
            if(1 == $no_shortcode)
                $text = strip_shortcodes($text);
            $text = apply_filters('the_content', $text);
            
            // From the default wp_trim_excerpt():
            // Some kind of precaution against malformed CDATA in RSS feeds I suppose
            $text = str_replace(']]>', ']]&gt;', $text);
            
            // Strip HTML if allow-all is not set
            if(!in_array('_all', $allowed_tags))
            {
                if(count($allowed_tags) > 0)
                    $tag_string = '<' . implode('><', $allowed_tags) . '>';
                else
                    $tag_string = '';
                $text = strip_tags($text, $tag_string);
            }
            
            if(1 == $use_words)
            {
                // Words
                
                // Skip if text is already within limit
                if($length >= count(preg_split('/[\s]+/', strip_tags($text))))
                    return $text;
                
                // Split on whitespace and start counting (for real)
                $text_bits = preg_split('/([\s]+)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                $in_tag = false;
                $n_words = 0;
                $text = '';
                foreach($text_bits as $chunk)
                {
                    if(!in_tag || strpos($chunk, '>') !== false)
                        $in_tag = (strrpos($chunk, '>') < strrpos($chunk, '<'));
                    
                    // Whitespace outside tags is word separator
                    if(!$in_tag && '' == trim($chunk))
                        $n_words++;
                    
                    if($n_words >= $length && !$in_tag)
                        break;
                    
                    $text .= $chunk;
                }
            }
            else
            {
                // Characters
                
                // Count characters, not whitespace, not those belonging to HTML tags
                if($length >= $this->strlen(preg_replace('/[\s]+/', '', strip_tags($text))))
                    return $text;
                
                $in_tag = false;
                $n_chars = 0;
                for($i = 0; $n_chars < $length || $in_tag; $i++)
                {
                    $char = $this->substr($text, $i, 1);
                    // Is the character worth counting (ie. not part of an HTML tag)
                    if($char == '<')
                    {
                        $in_tag = true;
                        if(($pos = strpos($text, '>', $i)) !== false)
                        {
                            $i = $pos - 1;
                            continue;
                        }
                    }
                    elseif($char == '>')
                        $in_tag = false;
                    elseif(!$in_tag && '' != trim($char))
                        $n_chars++;
                    
                    // Prevent eternal loops (this could happen with incomplete HTML tags)
                    if($i >= $this->strlen($text) - 1)
                        break;
                }
                $text = $this->substr($text, 0, $i);
            }
            
            $text = trim(force_balance_tags($text));
            
            // New filter in WP2.9, seems unnecessary for now
            //$ellipsis = apply_filters('excerpt_more', $ellipsis);
            
            // Read more
            if(1 == $add_link)
            {
                $ellipsis = $ellipsis . sprintf(' <a href="%s" class="read_more">%s</a>', get_permalink(), $read_more);
            }
            
            // Adding the ellipsis
            if(($pos = strpos($text, '</p>', strlen($text) - 7)) !== false)
            {
                // Stay inside the last paragraph (if it's in the last 6 characters)
                $text = substr_replace($text, $ellipsis, $pos, 0);
            }
            else
            {
                // If <p> is an allowed tag,
                // wrap the ellipsis for consistency with excerpt markup
                if(in_array('_all', $allowed_tags) || in_array('p', $allowed_tags))
                    $ellipsis = '<p>' . $ellipsis . '</p>';
                
                $text = $text . $ellipsis;
            }
        }
        
        return $text;
    }
    
    function install()
    {
        add_option($this->name . '_length', 40);
        add_option($this->name . '_use_words', 1);
        add_option($this->name . '_no_custom', 0);
        add_option($this->name . '_no_shortcode', 1);
        add_option($this->name . '_ellipsis', '&hellip;');
        add_option($this->name . '_read_more', 'Read the rest');
        add_option($this->name . '_add_link', 0);
        add_option($this->name . '_allowed_tags', $this->options_basic_tags);
        
        //$this->load_options();
    }
    
    function uninstall()
    {
        // Nothing to do (note: deactivation hook is also disabled)
    }
    
    function load_options()
    {
        $this->default_options = array(
            'length' => get_option($this->name . '_length'),
            'use_words' => get_option($this->name . '_use_words'),
            'no_custom' => get_option($this->name . '_no_custom'),
            'no_shortcode' => get_option($this->name . '_no_shortcode'),
            'ellipsis' => get_option($this->name . '_ellipsis'),
            'read_more' => get_option($this->name . '_read_more'),
            'add_link' => get_option($this->name . '_add_link'),
            'allowed_tags' => get_option($this->name . '_allowed_tags')
        );
    }
    
    
    function update_options()
    {
        $length = (int) $_POST[$this->name . '_length'];
        $use_words = ('on' == $_POST[$this->name . '_use_words']) ? 1 : 0 ;
        $no_custom = ('on' == $_POST[$this->name . '_no_custom']) ? 1 : 0 ;
        $no_shortcode = ('on' == $_POST[$this->name . '_no_shortcode']) ? 1 : 0 ;
        $add_link = ('on' == $_POST[$this->name . '_add_link']) ? 1 : 0 ;
        
        $ellipsis = (get_magic_quotes_gpc() == 1) ? stripslashes($_POST[$this->name . '_ellipsis']) : $_POST[$this->name . '_ellipsis'];
        $read_more = (get_magic_quotes_gpc() == 1) ? stripslashes($_POST[$this->name . '_read_more']) : $_POST[$this->name . '_read_more'];
        
        $allowed_tags = array_unique((array) $_POST[$this->name . '_allowed_tags']);
        
        update_option($this->name . '_length', $length);
        update_option($this->name . '_use_words', $use_words);
        update_option($this->name . '_no_custom', $no_custom);
        update_option($this->name . '_no_shortcode', $no_shortcode);
        update_option($this->name . '_ellipsis', $ellipsis);
        update_option($this->name . '_read_more', $read_more);
        update_option($this->name . '_add_link', $add_link);
        update_option($this->name . '_allowed_tags', $allowed_tags);
        
        $this->load_options();
    ?>
        <div id="message" class="updated fade"><p>Options saved.</p></div>
    <?php
    }
    
    function page_options()
    {
        if ('POST' == $_SERVER['REQUEST_METHOD'])
        {
            check_admin_referer($this->name . '_update_options');
            $this->update_options();
        }
        
        extract($this->default_options, EXTR_SKIP);

        // HTML entities for textbox
        $ellipsis = htmlentities($ellipsis);
        $read_more = htmlentities($read_more);
        
        // Basic tags + enabled tags
        $tag_list = $this->set_union($this->options_basic_tags, $allowed_tags);
        sort($tag_list);
        $tag_cols = 5;
?>
<div class="wrap">
    <div id="icon-options-general" class="icon32"><br /></div>
    <h2><?php _e("Advanced Excerpt Options", $this->text_domain); ?></h2>
    <form method="post" action="">
    <?php
        if ( function_exists('wp_nonce_field') )
            wp_nonce_field($this->name . '_update_options'); ?>

        <table class="form-table">
            <tr valign="top">
                <th scope="row"><label for="<?php echo $this->name ?>_length"><?php _e("Excerpt Length:", $this->text_domain); ?></label></th>
                <td>
                    <input name="<?php echo $this->name ?>_length" type="text" id="<?php echo $this->name ?>_length" value="<?php echo $length; ?>" size="2" />
                    <input name="<?php echo $this->name ?>_use_words" type="checkbox" id="<?php echo $this->name ?>_use_words" value="on" <?php echo (1 == $use_words) ? 'checked="checked" ': ''; ?>/> <?php _e("Use words?", $this->text_domain); ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="<?php echo $this->name ?>_ellipsis"><?php _e("Ellipsis:", $this->text_domain); ?></label></th>
                <td>
                    <input name="<?php echo $this->name ?>_ellipsis" type="text" id="<?php echo $this->name ?>_ellipsis" value="<?php echo $ellipsis; ?>" size="5" /> <?php _e('(use <a href="http://www.w3schools.com/tags/ref_entities.asp">HTML entities</a>)', $this->text_domain); ?>
                    <br />
                    <?php _e("Will substitute the part of the post that is omitted in the excerpt.", $this->text_domain); ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="<?php echo $this->name ?>_read_more"><?php _e("&lsquo;Read-more&rsquo; Text:", $this->text_domain); ?></label></th>
                <td>
                    <input name="<?php echo $this->name ?>_read_more" type="text" id="<?php echo $this->name ?>_read_more" value="<?php echo $read_more; ?>" />
                    <input name="<?php echo $this->name ?>_add_link" type="checkbox" id="<?php echo $this->name ?>_add_link" value="on" <?php echo (1 == $add_link) ? 'checked="checked" ': ''; ?>/> <?php _e("Add link to excerpt", $this->text_domain); ?> 
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="<?php echo $this->name ?>_no_custom"><?php _e("No Custom Excerpts:", $this->text_domain); ?></label></th>
                <td>
                    <input name="<?php echo $this->name ?>_no_custom" type="checkbox" id="<?php echo $this->name ?>_no_custom" value="on" <?php echo (1 == $no_custom) ? 'checked="checked" ': ''; ?>/>
                    <?php  _e("Generate excerpts even if a post has a custom excerpt attached.", $this->text_domain); ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="<?php echo $this->name ?>_no_shortcode"><?php _e("Strip Shortcodes:", $this->text_domain); ?></label></th>
                <td>
                    <input name="<?php echo $this->name ?>_no_shortcode" type="checkbox" id="<?php echo $this->name ?>_no_shortcode" value="on" <?php echo (1 == $no_shortcode) ? 'checked="checked" ': ''; ?>/>
                    <?php  _e("Remove shortcodes from the excerpt. <em>(recommended)</em>", $this->text_domain); ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e("Keep Markup:", $this->text_domain); ?></th>
                <td>
                    <table id="<?php echo $this->name ?>_tags_table">
                        <tr>
                            <td colspan="<?php echo $tag_cols; ?>">
    <input name="<?php echo $this->name ?>_allowed_tags[]" type="checkbox" value="_all" <?php echo (in_array('_all', $allowed_tags)) ? 'checked="checked" ': ''; ?>/>
    <?php  _e("Don't remove any markup", $this->text_domain); ?>
                            </td>
                        </tr>
<?php
        $i = 0;
        foreach($tag_list as $tag) :
            if($tag == '_all') continue;
            if(0 == $i % $tag_cols) : ?>
                        <tr>
<?php
            endif;
            $i++;
?>
                            <td>
    <input name="<?php echo $this->name ?>_allowed_tags[]" type="checkbox" value="<?php echo $tag; ?>" <?php echo (in_array($tag, $allowed_tags)) ? 'checked="checked" ': ''; ?>/>
    <code><?php echo $tag; ?></code>
                            </td>
<?php
            if(0 == $i % $tag_cols) : $i = 0; ?>
                        </tr>
<?php
        endif;
        endforeach;
        if(0 != $i % $tag_cols) : ?><td colspan="<?php echo ($tag_cols - $i); ?>">&nbsp;</td></tr><?php endif;?>
                    </table>
                    <a href="" id="<?php echo $this->name ?>_select_all">Select all</a> / <a href="" id="<?php echo $this->name ?>_select_none">Select none</a><br />
                    More tags:
                    <select name="<?php echo $this->name ?>_more_tags" id="<?php echo $this->name ?>_more_tags">
<?php
        foreach($this->options_body_tags as $tag) :
?>
                        <option value="<?php echo $tag; ?>"><?php echo $tag; ?></option>
<?php
        endforeach;
?>
                    </select>
                    <input type="button" name="<?php echo $this->name ?>_add_tag" id="<?php echo $this->name ?>_add_tag" class="button" value="Add tag" />
                </td>
            </tr>
        </table>
        <p class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php _e("Save Changes", $this->text_domain); ?>" /></p>
    </form>
</div>
<?php
    }
    
    function page_script()
    {
        wp_enqueue_script($this->name . '_script', WP_PLUGIN_URL . '/advanced-excerpt/advanced-excerpt.js', array('jquery'));
    }
    
    function add_pages()
    {
        $options_page = add_options_page(__("Advanced Excerpt Options", $this->text_domain), __("Excerpt", $this->text_domain), 'manage_options', 'options-' . $this->name, array(&$this, 'page_options'));
        
        // Scripts
        add_action('admin_print_scripts-' . $options_page, array(&$this, 'page_script'));
    }
    
    // Careful multibyte support (fallback to normal functions if not available)
    
    function substr($str, $start, $length = null)
    {
        $length = (is_null($length)) ? $this->strlen($str) : $length;
        if($this->mb)
            return mb_substr($str, $start, $length, $this->charset);
        else
            return substr($str, $start, $length);
    }
    
    function strlen($str)
    {
        if($this->mb)
            return mb_strlen($str, $this->charset);
        else
            return strlen($str);
    }
    
    // Some utility functions
    
    function set_complement($a, $b)
    {
        $c = array_diff($a, $b);
        return array_unique($c);
    }
    
    function set_union($a, $b)
    {
        $c = array_merge($a, $b);
        return array_unique($c);
    }
}

$advancedexcerpt = new AdvancedExcerpt();

// Do not use outside the Loop!
function the_advanced_excerpt($args = '', $get = true)
{
    global $advancedexcerpt;
    
    $r = wp_parse_args($args);
    
    if(isset($r['ellipsis']))
        $r['ellipsis'] = urldecode($r['ellipsis']);
    
    // TODO: Switch to 'allowed_tags' (compatibility code)
    if(isset($r['allow_tags']))
    {
        $r['allowed_tags'] = $r['allow_tags'];
        unset($r['allow_tags']);
    }
    
    if(isset($r['allowed_tags']))
        $r['allowed_tags'] = preg_split('/[\s,]+/', $r['allow_tags']);
    
    if(isset($r['exclude_tags']))
    {
        $r['exclude_tags'] = preg_split('/[\s,]+/', $r['exclude_tags']);
        // {all_tags} - {exclude_tags}
        $r['allowed_tags'] = $advancedexcerpt->set_complement($advancedexcerpt->options_body_tags, $r['exclude_tags']);
        unset($r['exclude_tags']);
    }
    
    // Set custom options (discard after use)
    $advancedexcerpt->custom_options = $r;
    if($get)
        echo get_the_excerpt();
    else
        the_excerpt();
    $advancedexcerpt->custom_options = null;
}
endif;