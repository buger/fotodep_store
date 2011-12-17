<?php
$prefix = 'usn_';

/***** Advanced post options *****/

$metabox_adv_options = array(
    'id' => 'advanced',
    'title' => __('Advanced Options', 'unspoken'),
    'page' => 'post',
    'context' => 'side',
    'priority' => 'low',
    'fields' => array(
        array(
            'name' => __('Show in Highlights', 'unspoken'),
            'id' => $prefix . 'highlight',
            'type' => 'checkbox'
        ),
        array(
            'name' => __('Show in Editor\'s Choice', 'unspoken'),
            'id' => $prefix . 'ec',
            'type' => 'checkbox'
        ),
        array(
            'name' => __('Show in magazine slider', 'unspoken'),
            'id' => $prefix . 'slider',
            'type' => 'checkbox'
        )
    )
);
add_action('admin_menu', 'unspoken_add_advbox');

// Add meta box
function unspoken_add_advbox() {
    global $metabox_adv_options;
    add_meta_box($metabox_adv_options['id'], $metabox_adv_options['title'], 'unspoken_show_advbox', $metabox_adv_options['page'], $metabox_adv_options['context'], $metabox_adv_options['priority']);
}

// Callback function to show fields in meta box
function unspoken_show_advbox() {
    global $metabox_adv_options, $post, $prefix;

    // Use nonce for verification
    echo '<input type="hidden" name="unspoken_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

    echo '<div class="widget-content">';

    foreach ($metabox_adv_options['fields'] as $field) {
        if ( $field['id'] == $prefix . 'slider' && !get_option('unspoken_mag_use') ) continue;
        // get current post meta data
        $meta = get_post_meta($post->ID, $field['id'], true);
        if ( $meta == 'on' ) $meta = 'checked = "checked"'; ?>

        <p><input class="checkbox" type="checkbox" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>" <?php echo $meta; ?> />&nbsp;
        <label for="<?php echo $field['id']; ?>"><?php echo $field['name']; ?></label></p>
        
    <?php
    }

    echo '</div>';
}

add_action('save_post', 'unspoken_save_advbox');

// Save data from meta box
function unspoken_save_advbox($post_id) {
    global $metabox_adv_options;

    if ( !isset( $_POST['unspoken_meta_box_nonce'] ) ) $_POST['unspoken_meta_box_nonce'] = wp_create_nonce(basename(__FILE__));

    // verify nonce
    if (!wp_verify_nonce($_POST['unspoken_meta_box_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // check permissions
    if ( !isset( $_POST['post_type'] ) ) $_POST['post_type'] = 'post';
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    } elseif (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    foreach ($metabox_adv_options['fields'] as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
        $new = ( isset( $_POST[$field['id']] ) ) ? $_POST[$field['id']] : 'off';

        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], $new);
        } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    }
}

/***** Video *****/

$metabox_video_options = array(
    'id' => 'video',
    'title' => __('Video Options', 'unspoken'),
    'page' => 'post',
    'context' => 'normal',
    'priority' => 'high',
    'fields' => array(
        array(
            'name' => __('Paste a link to video', 'unspoken'),
            'id' => $prefix . 'videolink',
            'type' => 'text'
        ),
        array(
            'name' => __('Show in Video', 'unspoken'),
            'id' => $prefix . 'show_in_video',
            'type' => 'checkbox'
        )
    )
);
add_action('admin_menu', 'unspoken_add_videobox');

// Add meta box
function unspoken_add_videobox() {
    global $metabox_video_options;
    add_meta_box($metabox_video_options['id'], $metabox_video_options['title'], 'unspoken_show_videobox', $metabox_video_options['page'], $metabox_video_options['context'], $metabox_video_options['priority']);
}

// Callback function to show fields in meta box
function unspoken_show_videobox() {
    global $metabox_video_options, $post;

    // Use nonce for verification
    echo '<input type="hidden" name="unspoken_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

    echo '<div class="widget-content">';
    //print_r($metabox_video_options);

    foreach ($metabox_video_options['fields'] as $field) {
        if ( $field['type'] == 'text' ) {
            $meta = get_post_meta($post->ID, $field['id'], true); ?>

            <p>
                <label for="<?php echo $field['id']; ?>"><?php echo $field['name']; ?>:</label>
                <input class="widefat" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>" value="<?php echo $meta; ?>" />
            </p>

        <?php
        }

        if ( $field['type'] == 'checkbox' ) {
            // get current post meta data
            $meta = get_post_meta($post->ID, $field['id'], true);
            if ( $meta == 'on' ) $meta = 'checked = "checked"'; ?>

            <p>
                <input class="checkbox" type="checkbox" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>" <?php echo $meta; ?> />&nbsp;
                <label for="<?php echo $field['id']; ?>"><?php echo $field['name']; ?></label>
            </p>

        <?php
        }
    }

    echo '</div>';
}

add_action('save_post', 'unspoken_save_videobox');

// Save data from meta box
function unspoken_save_videobox($post_id) {
    global $metabox_video_options;

    if ( !isset( $_POST['unspoken_meta_box_nonce'] ) ) $_POST['unspoken_meta_box_nonce'] = wp_create_nonce(basename(__FILE__));

    // verify nonce
    if (!wp_verify_nonce($_POST['unspoken_meta_box_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // check permissions
    if ( !isset( $_POST['post_type'] ) ) $_POST['post_type'] = 'post';
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    } elseif (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    foreach ($metabox_video_options['fields'] as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
        if ( $field['type'] == 'text' ) {
            $new = ( isset( $_POST[$field['id']] ) ) ? stripslashes_deep($_POST[$field['id']]) : '';
        }
        if ( $field['type'] == 'checkbox' ) {
            $new = ( isset( $_POST[$field['id']] ) ) ? $_POST[$field['id']] : 'off';
        }

        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], $new);
        } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    }
}
