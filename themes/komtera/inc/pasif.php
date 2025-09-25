<?php
/**
 * Admin’e, Users → Edit ekranında "Hesabı pasif" checkbox’ı ekle
 */
add_action('show_user_profile', 'my_user_disable_field');
add_action('edit_user_profile', 'my_user_disable_field');

// Ticket: 1651 Active/Inactive users development
function my_user_disable_field($user){
    $u = wp_get_current_user();
    $u = wp_get_current_user();
    if (!array_intersect(['manage_options', 'administrator' ,'jadmin'], (array)$u->roles)) {
        return;
    }
    $disabled = get_user_meta($user->ID, 'account_disabled', true);
    wp_nonce_field('my_user_disable_save', 'my_user_disable_nonce');
    ?>
    <h2><?php echo __('Hesap Durumu','komtera'); ?></h2>
    <table class="form-table" role="presentation">
        <tr>
            <th><label for="account_disabled"><?php echo __('Hesap Devre Dışı','komtera'); ?></label></th>
            <td>
                <label>
                    <input type="checkbox" id="account_disabled" name="account_disabled" value="1" <?php checked($disabled, '1'); ?>>
                </label>
            </td>
        </tr>
    </table>
    <?php
}