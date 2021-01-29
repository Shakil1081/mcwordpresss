<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function default_unique_content_menu() {

    add_menu_page(
            __('Default Uc Option', 'de_content'), __('Default Uc link', 'de_content'), 'manage_options', 'default_uc_option', 'default_uc_option_from', '', 4);
}

function default_uc_option_from() {
    global $default_uc_from_valur;
    ob_start();
    ?>
    <div class="wrap">
        <h2><?php _e('Facebook Footer Link Settings', 'de_content'); ?></h2>
        <p><?php _e('Settings for the Facebook Footer Link plugin', 'de_content'); ?></p>
        <form method="post" action="options.php">
            <?php settings_fields('default_unique_content_group'); ?>

          <div class="default_unique_inpout">
                <label for="ducontent_settings[enable]"><?php _e('Enable', 'de_content'); ?></label>
                <input name="ducontent_settings[enable]" type="checkbox" id="ducontent_settings[enable]" value="1" <?php checked('1', $default_uc_from_valur['enable']); ?>> 
                </div>
            <div class="default_unique_inpout"><label for="ducontent_settings[link_color]"><?php _e('Link Color', 'de_content'); ?></label>
                    <textarea name="ducontent_settings[link_color]" type="text" cols="100" rows="6" id="ducontent_settings[link_color]" value="<?php echo $default_uc_from_valur['link_color']; ?>"></textarea>
                    <p class="description"><?php _e('Enter a color or HEX value with a #', 'de_content'); ?></p> 
 </div>
                    <div class="default_unique_inpout"><label for="ducontent_settings[show_in_feed]"><?php _e('Show In Posts Feed', 'de_content'); ?></label>
                        <input name="ducontent_settings[show_in_feed]" type="checkbox" id="ducontent_settings[show_in_feed]" value="1" <?php checked('1', $default_uc_from_valur['show_in_feed']); ?> 

                        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes', 'de_content'); ?>"</p>
                        </form>
                    </div>
                    <?php
                    echo ob_get_clean();
                }

                add_action('admin_menu', 'default_unique_content_menu');

// Register Settings
                function default_unique_content_register_settings() {
                    register_setting('default_unique_content_group', 'ducontent_settings');
                }

                add_action('admin_init', 'default_unique_content_register_settings');

                