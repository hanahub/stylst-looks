<div class="wrap">
    <h2>SocialRoot - Stylst Looks Plugin Settings</h2>
    <form method="post" action="options.php">
        <?php settings_fields('dlst_stylst_options'); ?>
        <?php $options = get_option('dlst_stylst_options'); ?>
        <table class="form-table">
             <tr valign="top">
                <th scope="row">
                    Enter your provided SocialRoot username
                </th>
                <td>
                    <input name="dlst_stylst_options[name]" type="text" value="<?php ($options['name'] != '') ? print($options['name']) : print('stylst');?>"/>
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
    </form>
</div>

<br/>

<div class="wrap">
    <h3>Don't have an account?</h3>
    Apply for one here: <a href="http://www.stylst.com/plugin/" target="_blank">Stylst</a> Publisher Program
</div>
