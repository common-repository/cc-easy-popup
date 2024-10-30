<?php
class Callbacks {
    
    protected $options = array();
    
    /** 
     * Get the settings option array and print one of its values
     */
    function input_text_creator($params = array()) {
        $params['class'] = isset($params['class']) ? $params['class'] : 'regular-text';
        $params['desc'] = isset($params['desc']) ? $params['desc'] : '';
        printf(
            '<input type="text" id="%s" name="CC_newsletter_options[%s]" %s value="%s" class="%s" %s/>',
            $params['id'], $params['id'],
            $params['desc'] ? 'aria-describedby="'.$params['id'].'-description"' : '',
            isset( $this->options[$params['id']] ) ? esc_attr( $this->options[$params['id']]) : '', $params['class'],
            isset( $params['validation-rule'] ) ? 'data-validation-rule="'.$params['validation-rule'].'"' : ''
        );
        if ($params['desc']) {
            printf("<p class='description' id='%s'>%s</p>", $params['id'].'-description', $params['desc']);
        }
    }
    /** 
     * Get the settings option array and print one of its values
     */
    function input_chooseimage_creator($params = array()) {
        $params['class'] = isset($params['class']) ? $params['class'] : 'media-input';
        $params['desc'] = isset($params['desc']) ? $params['desc'] : '';
        printf(
            '<input type="text" id="%s" name="CC_newsletter_options[%s]" %s value="%s" class="%s" %s/><button class="media-button" type="button">Select image</button>',
            $params['id'], $params['id'],
            $params['desc'] ? 'aria-describedby="'.$params['id'].'-description"' : '',
            isset( $this->options[$params['id']] ) ? esc_attr( $this->options[$params['id']]) : '', $params['class'],
            isset( $params['validation-rule'] ) ? 'data-validation-rule="'.$params['validation-rule'].'"' : ''
        );
        if ($params['desc']) {
            printf("<p class='description' id='%s'>%s</p>", $params['id'].'-description', $params['desc']);
        }
    }

    /** 
     * Get the settings option array and print one of its values
     */
    function input_color_creator($params = array()) {
        $params['class'] = isset($params['class']) ? $params['class'] : 'regular-text';
        $params['desc'] = isset($params['desc']) ? $params['desc'] : '';
        printf(
            '<input type="color" id="%s" name="CC_newsletter_options[%s]" %s value="%s" class="%s" %s/>',
            $params['id'], $params['id'],
            $params['desc'] ? 'aria-describedby="'.$params['id'].'-description"' : '',
            isset( $this->options[$params['id']] ) ? esc_attr( $this->options[$params['id']]) : '', $params['class'],
            isset( $params['validation-rule'] ) ? 'data-validation-rule="'.$params['validation-rule'].'"' : ''
        );
        if ($params['desc']) {
            printf("<p class='description' id='%s'>%s</p>", $params['id'].'-description', $params['desc']);
        }
    }

    /** 
     * Get the settings option array and print one of its values
     */
    function input_checkbox_creator($params = array()) {
        //$params['label'] = isset($params['label']) ? $params['label'] : 'Label';
        $params['l_abel'] = isset($params['_label']) ? $params['_label'] : '_Label';
        $params['desc'] = isset($params['desc']) ? $params['desc'] : '';
        $params['value'] = isset($params['value']) ? $params['value'] : '';
        printf(
            '<label for="%s"><input name="CC_newsletter_options[%s]" type="checkbox" id="%s" %s %s value="%s" style="margin-right:10px;" %s>%s</label>',
            $params['id'], $params['id'], $params['id'],
            (isset($this->options[$params['id']]) && $this->options[$params['id']] == $params['value']) ? 'checked' : '',
            $params['desc'] ? 'aria-describedby="'.$params['id'].'-description"' : '',
            $params['value'],
            isset( $params['validation-rule'] ) ? 'data-validation-rule="'.$params['validation-rule'].'"' : '',
            $params['_label']
        );
        if ($params['desc']) {
            printf("<p class='description' id='%s'>%s</p>", $params['id'].'-description', $params['desc']);
        }
    }

    /** 
     * Get the settings option array and print one of its values
     */
    function input_select_creator($params = array()) {
        $params['empty'] = isset($params['empty']) ? $params['empty'] : '-- SELECT --';
        if (isset($params['options']) && is_array($params['options'])) {
            $params['desc'] = isset($params['desc']) ? $params['desc'] : '';
            $selected = isset( $params['selected'] ) ? $params['selected'] : '';
            $selected = isset( $this->options[$params['id']] ) ? esc_attr( $this->options[$params['id']]) : $selected;
            printf('<select name="CC_newsletter_options[%s]" %s id="%s" %s>',
                $params['id'],
                $params['desc'] ? 'aria-describedby="'.$params['id'].'-description"' : '',
                $params['id'],
                isset( $params['validation-rule'] ) ? 'data-validation-rule="'.$params['validation-rule'].'"' : ''
            );
            echo '<option value="">'.$params['empty'].'</option>';
            foreach ($params['options'] as $key => $value) { 
                if ($key == $selected) {
                    echo '<option selected="selected" value="'.$key.'">'.$value.'</option>';
                }
                else {
                    echo '<option value="'.$key.'">'.$value.'</option>';
                }
            }
            echo '</select>';
            if ($params['desc']) {
                printf("<p class='description' id='%s'>%s</p>", $params['id'].'-description', $params['desc']);
            }
        }
        else {
            echo 'It requires select options!';
        }
    }

    /**
     * [input_editor_creator description]
     * @param  array  $params [description]
     * @return [type]         [description]
     */
    function input_editor_creator($params = array()) {
        $args = array("textarea_name" => "CC_newsletter_options[".$params['id']."]", "media_buttons"=>false);
        $editor_id = str_replace(array('[', ']', '_', '-'), '', $args['textarea_name']);
        $content = isset($this->options[$params['id']]) ? $this->options[$params['id']] : 'Let them know what kind of content they will receive.';
        wp_editor( $content, $editor_id, $args );
        $params['desc'] = isset($params['desc']) ? $params['desc'] : '';
        if ($params['desc']) {
            printf("<p class='description' id='%s'>%s</p>", $params['id'].'-description', $params['desc']);
        }
    }

    /**
     * [input_textarea_creator description]
     * @param  array  $params [description]
     * @return [type]         [description]
     */
    function input_textarea_creator($params = array()){
        $value = isset($this->options[$params['id']]) ? $this->options[$params['id']] : '';
        $params['desc'] = isset($params['desc']) ? $params['desc'] : '';
        echo "<textarea id='{$params['id']}' name='CC_newsletter_options[{$params['id']}]' rows='7' cols='50' ";
        echo $params['desc'] ? 'aria-describedby="'.$params['id'].'-description"' : '';
        echo " type='textarea'>{$value}</textarea>";
        if ($params['desc']) {
            printf("<p class='description' id='%s'>%s</p>", $params['id'].'-description', $params['desc']);
        }
    }

    /**
     * settings page callback
     */
    function create_admin_page() {
        $option_name = 'CC_newsletter_options';
        wp_cache_delete($option_name);
        $this->options = get_option($option_name);
        //include( CPAS_POPUP_PATH . 'admin/CC-newsletter-popup-settings.php' );
?>
        <div class="wrap">
            <h1>Newsletter Popup</h1>
            <form method="post" action="<?php echo CC_POPUP_AJAX_URL; ?>" id="CC_newsletter_settings_form">
			<?php
                // This prints out all hidden setting fields
                settings_fields( 'CC_newsletter_options_group' );   
                do_settings_sections( CPAS_PLUGIN_PREFIX . 'popup_admin_settings' );
                submit_button(); 
            ?>
            </form>
            <div class="clear"></div>
        </div>
<?php
    }

    /**
     * subscribers page callback
     */
    function subscriber_list() {
        printf("<p>subscriber-page</p>");
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input ) {
        $new_input = array();
        //print_r($input);exit;
        if( isset( $input['id_number'] ) )
            $new_input['id_number'] = absint( $input['id_number'] );

        if( isset( $input['title'] ) )
            $new_input['title'] = sanitize_text_field( $input['title'] );

        return $new_input;
    }
}
