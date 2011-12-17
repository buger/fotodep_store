<?php $this->noscript_notice() ?>
<p style="float:left;"> 
<?php printf(__('Language of this %s', 'sitepress'), strtolower($wp_post_types[$post->post_type]->labels->singular_name)); ?>&nbsp;
<select name="icl_post_language" id="icl_post_language">
<?php foreach($active_languages as $lang):?>
<?php if(isset($translations[$lang['code']]->element_id) && $translations[$lang['code']]->element_id != $post->ID) continue ?>
<option value="<?php echo $lang['code'] ?>" <?php if($selected_language==$lang['code']): ?>selected="selected"<?php endif;?>><?php echo $lang['display_name'] ?>&nbsp;</option>
<?php endforeach; ?>
</select> 

<input type="hidden" name="icl_trid" value="<?php echo $trid ?>" />

<?php /*<input type="hidden" name="icl_is_page" value="<?php echo $is_page ?>" /> */?>

</p>

<div id="translation_of_wrap">
    <?php if( ($selected_language != $default_language || (isset($_GET['lang']) && $_GET['lang']!=$default_language)) && 'all' != $this->get_current_language() ): ?>
        <div style="clear:both;font-size:1px">&nbsp;</div>
        
        <p style="float:left;">
        <?php echo __('This is a translation of', 'sitepress') ?>&nbsp;
        <select name="icl_translation_of" id="icl_translation_of"<?php if((empty($_GET['action']) || $_GET['action'] != 'edit') && $trid) echo ' disabled="disabled"';?>>
            <?php if($source_language == null || $source_language == $default_language): ?>
                <?php if($trid): ?>
                    <option value="none"><?php echo __('--None--', 'sitepress') ?></option>                    
                    <?php
                        //get source
                        $src_language_id = $wpdb->get_var("SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE trid={$trid} AND language_code='{$default_language}'");                        
                        if(!$src_language_id) {
                            // select the first id found for this trid
                            $src_language_id = $wpdb->get_var("SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE trid={$trid}");
                        }                                                      
                        if($src_language_id && $src_language_id != $post->ID) {
                            $src_language_title = $wpdb->get_var("SELECT post_title FROM {$wpdb->prefix}posts WHERE ID = {$src_language_id}");                            
                        }
                    ?>
                    <?php if($src_language_title && !isset($_GET['icl_ajx'])): ?>
                        <option value="<?php echo $src_language_id ?>" selected="selected"><?php echo $src_language_title ?>&nbsp;</option>
                    <?php endif; ?>
                <?php else: ?>
                    <option value="none" selected="selected"><?php echo __('--None--', 'sitepress') ?></option>
                <?php endif; ?>
                
                <?php foreach($untranslated as $translation_of_id => $translation_of_title):?>
                    <?php //if (!empty($src_language_id) && $translation_of_id != $src_language_id): ?>
                        <option value="<?php echo $translation_of_id ?>"><?php echo $translation_of_title ?>&nbsp;</option>
                    <?php //endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <?php if($trid): ?>
                    <?php
                        // add the source language
                        $src_language_id = $wpdb->get_var("SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE trid={$trid} AND language_code='{$source_language}'");
                        if($src_language_id) {
                            $src_language_title = $wpdb->get_var("SELECT post_title FROM {$wpdb->prefix}posts WHERE ID = {$src_language_id}");
                        }
                    ?>
                    <?php if($src_language_title): ?>
                        <option value="<?php echo $src_language_id ?>" selected="selected"><?php echo $src_language_title ?></option>
                    <?php endif; ?>
                <?php else: ?>   
                    <option value="none" selected="selected"><?php echo __('--None--', 'sitepress') ?></option>
                <?php endif; ?>
            <?php endif; ?>
        </select>

        </p>
    <?php endif; ?>
</div><!--//translation_of_wrap--><?php // don't delete this html comment ?>

<div style="clear:both;font-size:1px">&nbsp;</div>

<?php if(isset($_GET['action']) && $_GET['action'] == 'edit' && $trid): ?>       
    
    <?php do_action('icl_post_languages_options_before', $post->ID);?>

    <div id="icl_translate_options">
    <?php
        // count number of translated and un-translated pages.
        $translations_found = 0;
        $untranslated_found = 0;
        foreach($active_languages as $lang) {
            if($selected_language==$lang['code']) continue;
            if(isset($translations[$lang['code']]->element_id)) {
                $translations_found += 1;
            } else {
                $untranslated_found += 1;
            }
        }
    ?>
    
    <?php if($untranslated_found > 0 && (empty($iclTranslationManagement->settings['doc_translation_method']) || $iclTranslationManagement->settings['doc_translation_method'] != ICL_TM_TMETHOD_PRO)): ?>    
        <?php if($this->get_icl_translation_enabled()):?>
            <p style="clear:both;"><b><?php _e('or, translate manually:', 'sitepress'); ?> </b>
        <?php else: ?>
            <p style="clear:both;"><b><?php _e('Translate yourself', 'sitepress'); ?></b>
        <?php endif; ?>
        <table width="100%" class="icl_translations_table">
        <?php $oddev = 1; ?>
        <?php foreach($active_languages as $lang): if($selected_language==$lang['code']) continue; ?>        
        <tr <?php if($oddev < 0): ?>class="icl_odd_row"<?php endif; ?>>            
            <?php if(!isset($translations[$lang['code']]->element_id)):?>                
                <?php $oddev = $oddev*-1; ?>
                <td style="padding-left: 4px;"><?php echo $lang['display_name'] ?></td>
                <?php
                    $add_anchor =  __('add','sitepress');
                    $img = 'add_translation.png';
                    if(!empty($iclTranslationManagement->settings['doc_translation_method']) && $iclTranslationManagement->settings['doc_translation_method'] == ICL_TM_TMETHOD_EDITOR){
                            $job_id = $iclTranslationManagement->get_translation_job_id($trid, $lang['code']);
                            
                            $args = array('lang_from'=>$selected_language, 'lang_to'=>$lang['code'], 'job_id'=>@intval($job_id));
                            $current_user_is_translator = $iclTranslationManagement->is_translator(get_current_user_id(), $args);
                            
                            if($job_id){
                                $job_details = $iclTranslationManagement->get_translation_job($job_id);
                                
                                if($current_user_is_translator){
                                    if($job_details->status == ICL_TM_IN_PROGRESS){
                                        $add_anchor =  __('in progress','sitepress');
                                        $img = 'in-progress.png';
                                    }
                                }else{
                                    $tres = $wpdb->get_row($wpdb->prepare("
                                        SELECT s.* FROM {$wpdb->prefix}icl_translation_status s 
                                            JOIN {$wpdb->prefix}icl_translate_job j ON j.rid = s.rid
                                            WHERE job_id=%d", $job_id));
                                    if($tres->status == ICL_TM_IN_PROGRESS){
                                        $img = 'edit_translation_disabled.png';
                                        $add_anchor =  __('in progress (by a different translator)','sitepress');    
                                    }elseif($tres->status == ICL_TM_NOT_TRANSLATED || $tres->status == ICL_TM_WAITING_FOR_TRANSLATOR){
                                        $img = 'add_translation_disabled.png';
                                        $add_anchor = __('You are not the translator of this document','sitepress');
                                    }elseif($tres->status == ICL_TM_NEEDS_UPDATE || $tres->status == ICL_TM_COMPLETE){
                                        $img = 'edit_translation_disabled.png';
                                        $add_anchor = __('You are not the translator of this document','sitepress');
                                    }
                                    
                                }
                                if($current_user_is_translator){
                                    $add_link = admin_url('admin.php?page='.WPML_TM_FOLDER.'/menu/translations-queue.php&job_id='.$job_id);        
                                }else{
                                    $add_link = '#';
                                    $add_anchor =  __('in progress (by a different translator)','sitepress');    
                                }
                                
                            }else{
                                if($current_user_is_translator){
                                    $add_link = admin_url('admin.php?page='.WPML_TM_FOLDER.'/menu/translations-queue.php&icl_tm_action=create_job&iclpost[]='.
                                    $post->ID.'&translate_to['.$lang['code'].']=1&iclnonce=' . wp_create_nonce('pro-translation-icl'));
                                }else{
                                    $add_link = '#';
                                    $img = 'add_translation_disabled.png';
                                    $add_anchor = __('You are not the translator of this document','sitepress');
                                }
                            }                                                    
                    }else{     
                        $add_link = get_option('siteurl') . "/wp-admin/post-new.php?post_type={$post->post_type}&trid=" . 
                            $trid . "&lang=" . $lang['code'] . "&source_lang=" . $selected_language;    
                    }                                        
                ?>
                <td align="right"><a href="<?php echo $add_link?>" title="<?php echo esc_attr($add_anchor) ?>"><img  border="0" src="<?php 
                    echo ICL_PLUGIN_URL . '/res/img/' . $img ?>" alt="<?php echo esc_attr($add_anchor) ?>" width="16" height="16" /></a></td>
            <?php endif; ?>        
        </tr>
        <?php endforeach; ?>
        </table>
        </p>
    <?php endif; ?>
    <?php if($translations_found > 0): ?>    
        <p style="clear:both;">
            <b><?php _e('Translations', 'sitepress') ?></b> 
            (<a class="icl_toggle_show_translations" href="#" <?php if(empty($this->settings['show_translations_flag'])):?>style="display:none;"<?php endif;?>><?php _e('hide','sitepress')?></a><a class="icl_toggle_show_translations" href="#" <?php if(!empty($this->settings['show_translations_flag'])):?>style="display:none;"<?php endif;?>><?php _e('show','sitepress')?></a>)                
        <table width="100%" class="icl_translations_table" id="icl_translations_table" <?php if(empty($this->settings['show_translations_flag'])):?>style="display:none;"<?php endif;?>>        
        <?php $oddev = 1; ?>
        <?php foreach($active_languages as $lang): if($selected_language==$lang['code']) continue; ?>
        <tr <?php if($oddev < 0): ?>class="icl_odd_row"<?php endif; ?>>
            <?php if(isset($translations[$lang['code']]->element_id)):?>
                <?php 
                    $oddev = $oddev*-1;
                    $img = 'edit_translation.png';
                    $edit_anchor = __('edit','sitepress');
                    list($needs_update, $in_progress) = $wpdb->get_row($wpdb->prepare("
                        SELECT needs_update, status = ".ICL_TM_IN_PROGRESS." FROM {$wpdb->prefix}icl_translation_status s JOIN {$wpdb->prefix}icl_translations t ON t.translation_id = s.translation_id
                        WHERE t.trid = %d AND t.language_code = '%s'
                    ", $trid, $lang['code']), ARRAY_N);           
                    switch($iclTranslationManagement->settings['doc_translation_method']){
                        case ICL_TM_TMETHOD_EDITOR:
                            $job_id = $iclTranslationManagement->get_translation_job_id($trid, $lang['code']);
                            
                            $args = array('lang_from'=>$selected_language, 'lang_to'=>$lang['code'], 'job_id'=>@intval($job_id));
                            $current_user_is_translator = $iclTranslationManagement->is_translator(get_current_user_id(), $args);
                            
                            if($needs_update){
                                $img = 'needs-update.png';
                                $edit_anchor = __('Update translation','sitepress');
                                if($current_user_is_translator){
                                    $edit_link = admin_url('admin.php?page='.WPML_TM_FOLDER.'/menu/translations-queue.php&icl_tm_action=create_job&iclpost[]='.
                                    $post->ID.'&translate_to['.$lang['code'].']=1&iclnonce=' . wp_create_nonce('pro-translation-icl'));
                                }else{
                                    $edit_link = '#';
                                    $img = 'edit_translation_disabled.png';
                                    $edit_anchor = __('You are not the translator of this document','sitepress');
                                }
                            }else{
                                if($current_user_is_translator){
                                    $edit_link = admin_url('admin.php?page='.WPML_TM_FOLDER.'/menu/translations-queue.php&job_id='.$job_id);    
                                }else{
                                    $edit_link = '#';
                                    $img = 'edit_translation_disabled.png';
                                    $edit_anchor = __('You are not the translator of this document','sitepress');
                                }
                            }
                            break;                        
                        case ICL_TM_TMETHOD_PRO:
                            $job_id = $iclTranslationManagement->get_translation_job_id($trid, $lang['code']);
                            if($in_progress){
                                $img = 'in-progress.png';
                                $edit_link = '#';
                                $edit_anchor = __('Translation is in progress','sitepress');
                            }elseif($needs_update){
                                $img = 'needs-update.png';
                                $edit_anchor = __('Update translation','sitepress');
                                $qs = array();
                                if(!empty($_SERVER['QUERY_STRING']))
                                foreach($_exp = explode('&', $_SERVER['QUERY_STRING']) as $q=>$qv){
                                    $__exp = explode('=', $qv);
                                    $__exp[0] = preg_replace('#\[(.*)\]#', '', $__exp[0]);
                                    if(!in_array($__exp[0], array('icl_tm_action', 'translate_from', 'translate_to', 'iclpost', 'service', 'iclnonce'))){
                                        $qs[$q] = $qv;
                                    }
                                }                                
                                $edit_link = admin_url('post.php?'.join('&', $qs).'&icl_tm_action=send_jobs&translate_from='.$source_language
                                    .'&translate_to['.$lang['code'].']=1&iclpost[]='.$post->ID
                                    .'&service=icanlocalize&iclnonce=' . wp_create_nonce('pro-translation-icl')); 
                            }else{
                                $edit_link = admin_url('admin.php?page='.WPML_TM_FOLDER.'/menu/translations-queue.php&job_id='.$job_id);    
                            }
                            break;
                        default:
                            $edit_link = get_edit_post_link($translations[$lang['code']]->element_id);    
                    }                    
                ?>
                <td style="padding-left: 4px;"><?php echo $lang['display_name'] ?></td>
                <td align="right" ><a href="<?php echo $edit_link ?>" title="<?php echo esc_attr($edit_anchor) ?>"><img border="0" src="<?php 
                    echo ICL_PLUGIN_URL . '/res/img/' . $img ?>" alt="<?php echo esc_attr($edit_anchor) ?>" width="16" height="16" /></a>                    
                </td>
                
            <?php endif; ?>        
        </tr>
        <?php endforeach; ?>
        </table>
                
        
    <?php endif; ?>
    
    <br clear="all" style="line-height:1px;" />
    
    </div>
<?php endif; ?>

<?php do_action('icl_post_languages_options_after') ?>
