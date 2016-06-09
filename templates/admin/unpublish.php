<?php
global $post;
$unpubDate = get_post_meta($post->ID, 'unpublish-date', true);
$action = get_post_meta($post->ID, 'unpublish-action', true) ? get_post_meta($post->ID, 'unpublish-action', true) : 'trash';
?>
<div class="misc-pub-section unpublish-pub-section">
    <span id="unpublish-timestamp">
        <?php _e('Unpublish', 'municipio'); ?>
        <b></b>
    </span>

    <a href="#edit_unpublish-timestamp" class="edit-unpublish-timestamp hide-if-no-js"><span aria-hidden="true"><?php _e('Edit'); ?></span> <span class="screen-reader-text"><?php _e('Edit unpublish date and time'); ?></span></a>

    <fieldset id="unpublish-timestampdiv" style="padding-top: 5px;" class="hide-if-js">
        <div id="unpublish-action">
            <input type="radio" name="unpublish-action" value="trash" id="unpublish-action-trash" <?php checked('trash', $action); ?>>
            <label for="unpublish-action-trash"><?php _e('Trash'); ?></label>

            <input type="radio" name="unpublish-action" value="draft" id="unpublish-action-draft" <?php checked('draft', $action); ?>>
            <label for="unpublish-action-draft"><?php _e('Draft'); ?></label>
        </div>
        <div id="unpublish-timestamp-datepicker" class="municipio-admin-datepicker"></div>
        <div class="timestamp-wrap">
            <label>
                <span class="screen-reader-text"><?php _e('Month'); ?></span>
                <select name="unpublish-mm" id="unpublish-mm" placeholder="<?php _e('Month'); ?>">
                    <option data-text="Jan" value="01" <?php selected('01', isset($unpubDate['mm']) ? $unpubDate['mm'] : null); ?>>01-Jan</option>
                    <option data-text="Feb" value="02" <?php selected('02', isset($unpubDate['mm']) ? $unpubDate['mm'] : null); ?>>02-Feb</option>
                    <option data-text="Mar" value="03" <?php selected('03', isset($unpubDate['mm']) ? $unpubDate['mm'] : null); ?>>03-Mar</option>
                    <option data-text="Apr" value="04" <?php selected('04', isset($unpubDate['mm']) ? $unpubDate['mm'] : null); ?>>04-Apr</option>
                    <option data-text="Maj" value="05" <?php selected('05', isset($unpubDate['mm']) ? $unpubDate['mm'] : null); ?>>05-Maj</option>
                    <option data-text="Jun" value="06" <?php selected('06', isset($unpubDate['mm']) ? $unpubDate['mm'] : null); ?>>06-Jun</option>
                    <option data-text="Jul" value="07" <?php selected('07', isset($unpubDate['mm']) ? $unpubDate['mm'] : null); ?>>07-Jul</option>
                    <option data-text="Aug" value="08" <?php selected('08', isset($unpubDate['mm']) ? $unpubDate['mm'] : null); ?>>08-Aug</option>
                    <option data-text="Sep" value="09" <?php selected('09', isset($unpubDate['mm']) ? $unpubDate['mm'] : null); ?>>09-Sep</option>
                    <option data-text="Okt" value="10" <?php selected('10', isset($unpubDate['mm']) ? $unpubDate['mm'] : null); ?>>10-Okt</option>
                    <option data-text="Nov" value="11" <?php selected('11', isset($unpubDate['mm']) ? $unpubDate['mm'] : null); ?>>11-Nov</option>
                    <option data-text="Dec" value="12" <?php selected('12', isset($unpubDate['mm']) ? $unpubDate['mm'] : null); ?>>12-Dec</option>
                </select>
            </label>
            <label><span class="screen-reader-text"><?php _e('Day'); ?></span><input type="text" autocomplete="off" maxlength="2" size="2" name="unpublish-jj" id="unpublish-jj" value="<?php echo isset($unpubDate['jj']) ? $unpubDate['jj'] : ''; ?>"></label>
            ,
            <label><span class="screen-reader-text"><?php _e('Year'); ?></span><input type="text" autocomplete="off" maxlength="4" size="4" name="unpublish-aa" id="unpublish-aa" value="<?php echo isset($unpubDate['aa']) ? $unpubDate['aa'] : ''; ?>"></label>
            @
            <label><span class="screen-reader-text"><?php _e('Hour'); ?></span><input type="text" autocomplete="off" maxlength="2" size="2" name="unpublish-hh" id="unpublish-hh" value="<?php echo isset($unpubDate['hh']) ? $unpubDate['hh'] : gmdate('H', current_time('timestamp')); ?>"></label>
            :
            <label><span class="screen-reader-text"><?php _e('Minute'); ?></span><input type="text" autocomplete="off" maxlength="2" size="2" name="unpublish-mn" id="unpublish-mn" value="<?php echo isset($unpubDate['mn']) ? $unpubDate['mn'] : gmdate('i', current_time('timestamp')); ?>"></label>
        </div>

        <input type="hidden" id="unpublish-active" name="unpublish-active" value="<?php echo is_array($unpubDate) ? 'true' : 'false'; ?>">

        <p>
            <a href="#edit_timestamp" class="save-unpublish-timestamp hide-if-no-js button"><?php _e('OK'); ?></a>
            <a href="#edit_timestamp" class="cancel-unpublish-timestamp hide-if-no-js button-cancel"><?php _e('Cancel'); ?></a>
        </p>
    </fieldset>
</div>
