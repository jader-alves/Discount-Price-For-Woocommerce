<?php

if (!defined('ABSPATH'))
    exit;

if (!class_exists('DPFW_menu')) {

    class DPFW_menu {

        protected static $DPFW_instance;


        function DPFW_custom_product_tabs( $tabs) {
            $tabs['wqrqty_rules'] = array(
                'label'     => __( 'Quantity Rules', 'woocommerce' ),
                'target'    => 'wqrqty_options',
                'class'     => array( 'show_if_simple', 'show_if_variable', 'show_if_grouped', 'show_if_external' ),
            );
            return $tabs;
        }


        function DPFW_custom_product_tabs_fields() {
            global $post, $product_object;
            $product_id = $post->ID;
            ?> 
                <div id="wqrqty_options" class="panel woocommerce_options_panel">
                    <div class='options_group' >
                        <div class="wqrrules_container">
                            <p>** if you are not select any user role then it will apply for all users.</p>
                            <table class="wqrrules_table">
                                <thead>
                                    <tr>
                                        <th>Min Qty</th>
                                        <th>Max Qty</th>
                                        <th>Discount type</th>
                                        <th>User Role</th>
                                        <th>Remove</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    <?php 
                                        $qtymin = get_post_meta($product_id,'wqrmin',true);
                                        $qtymax = get_post_meta($product_id,'wqrmax',true);
                                        $wqrdiscount_type = get_post_meta($product_id,'wqrdiscount_type',true);
                                        $wqrdiscount = get_post_meta($product_id,'wqrdiscount',true);
                                        $wqr_roles = get_post_meta($product_id,'wqr_roles',true);
                                        
                                        if(empty($qtymin)){
                                            ?>
                                                <tr>
                                                    <td>
                                                        <input type="number" name="new_wqrmin[]" min="1">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="new_wqrmax[]" min="1">
                                                    </td>
                                                    <td>
                                                        <select name="new_wqrdiscount_type[]">
                                                            <option value="fixed">Fixed</option>
                                                            <option value="percentage">Percentage</option>
                                                        </select>
                                                        <input type="number" step="0.01" name="new_wqrdiscount[]">
                                                    </td>
                                                    <td>
                                                        <?php $mam_roles = get_editable_roles(); ?>
                                                        <select name="new_wqr_roles[][]" id="wqr_roles" multiple>
                                                            <?php 
                                                                foreach ($mam_roles as $mam_roles_key => $mam_roles_value) {
                                                                    echo "<option value='".$mam_roles_key."'>".$mam_roles_value['name']."</option>";
                                                                }
                                                            ?>
                                                        </select>   
                                                    </td>
                                                    <td>
                                                        <p class="wqrremove">Remove</p>
                                                    </td>
                                                </tr>
                                            <?php
                                        }else{
                                            foreach ($qtymin as $key => $value) {
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <input type="number" name="new_wqrmin[]" value="<?php echo $value; ?>" min="1">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="new_wqrmax[]" value="<?php echo $qtymax[$key]; ?>" min="1">
                                                        </td>
                                                        <td>
                                                            <select name="new_wqrdiscount_type[]">
                                                                <option value="fixed" <?php if($wqrdiscount_type[$key] == "fixed"){ echo "selected"; } ?> >Fixed</option>
                                                                <option value="percentage" <?php if($wqrdiscount_type[$key] == "percentage"){ echo "selected"; } ?>>Percentage</option>
                                                            </select>
                                                            <input type="number" name="new_wqrdiscount[]" step="0.01" value="<?php echo $wqrdiscount[$key]; ?>">
                                                        </td>
                                                        <td>
                                                            <?php $mam_roles = get_editable_roles(); ?>
                                                            <select name="new_wqr_roles[][]" id="wqr_roles" multiple>
                                                                <?php 
                                                                    foreach ($mam_roles as $mam_roles_key => $mam_roles_value) {
                                                                        ?>
                                                                        <option value="<?php echo $mam_roles_key; ?>" <?php if(!empty($wqr_roles) && !is_null($wqr_roles[$key])) { if(in_array($mam_roles_key,$wqr_roles[$key])){ echo "selected"; } } ?>>
                                                                            <?php echo $mam_roles_value['name']; ?>
                                                                        </option>
                                                                        <?php
                                                                    }
                                                                ?>
                                                            </select>   
                                                        </td>
                                                        <td>
                                                            <p class="wqrremove">Remove</p>
                                                        </td>
                                                    </tr>
                                                <?php
                                            }
                                        }
                                    ?>
                                </tbody>
                            </table>
                            <a class="add_more">ADD</a>
                        </div>
                    </div>
                </div>
            <?php
        }

   
        function DPFW_save_proddata_custom_fields($post_id) {
            
            $wqrmin = $this->recursive_sanitize_text_field($_POST['wqrmin']);
            $wqrmax = $this->recursive_sanitize_text_field($_POST['wqrmax']);
            $wqrdiscount_type = $this->recursive_sanitize_text_field($_POST['wqrdiscount_type']);
            $wqrdiscount = $this->recursive_sanitize_text_field($_POST['wqrdiscount']);
            $wqr_roles = $this->recursive_sanitize_text_field($_POST['wqr_roles']);


            $new_wqrmin = $this->recursive_sanitize_text_field($_POST['new_wqrmin']);
            $new_wqrmax = $this->recursive_sanitize_text_field($_POST['new_wqrmax']);
            $new_wqrdiscount_type = $this->recursive_sanitize_text_field($_POST['new_wqrdiscount_type']);
            $new_wqrdiscount = $this->recursive_sanitize_text_field($_POST['new_wqrdiscount']);
            $new_wqr_roles = $this->recursive_sanitize_text_field($_POST['new_wqr_roles']);

     
            update_post_meta($post_id,'wqrmin','');
            update_post_meta($post_id,'wqrmax','');
            update_post_meta($post_id,'wqrdiscount_type','');
            update_post_meta($post_id,'wqrdiscount','');
            update_post_meta($post_id,'wqr_roles','');
            if(!empty($new_wqrmin)) {
                
                $wqrminall = $this->recursive_sanitize_text_field($_POST['new_wqrmin']);
                $wqrmaxall = $this->recursive_sanitize_text_field($_POST['new_wqrmax']);
                $wqrdiscount_typeall = $this->recursive_sanitize_text_field($_POST['new_wqrdiscount_type']);
                $wqrdiscountall = $this->recursive_sanitize_text_field($_POST['new_wqrdiscount']);
                $wqr_rolesall = $this->recursive_sanitize_text_field($_POST['new_wqr_roles']);
               
            }else{
              
                
            }
           

            if(!empty($wqrminall[0]) && !empty($wqrmaxall[0]) && !empty($wqrdiscountall[0])) {
                update_post_meta($post_id,'wqrmin',$wqrminall);
                update_post_meta($post_id,'wqrmax',$wqrmaxall);
                update_post_meta($post_id,'wqrdiscount_type',$wqrdiscount_typeall);
                update_post_meta($post_id,'wqrdiscount',$wqrdiscountall);
                update_post_meta($post_id,'wqr_roles',$wqr_rolesall);
            }
            
        }
     
        
        function recursive_sanitize_text_field($array) {

            foreach ( $array as $key => $value ) {
                if ( is_array( $value ) ) {
                    $value = $this->recursive_sanitize_text_field($value);
                }else{
                    $value = sanitize_text_field( $value );
                }
            }
            return $array;
        }


        function DPFW_foot_script() {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function(){
                   
                    jQuery(".add_more").click(function(){
                        var html = "<?php   $mam_roles = get_editable_roles(); 
                                foreach ($mam_roles as $mam_roles_key => $mam_roles_value) {
                                    echo "<option value='".$mam_roles_key."'>".$mam_roles_value['name']."</option>";
                                }
                        ?>";
                        jQuery(".wqrrules_table tbody").append('<tr><td><input type="number" name="new_wqrmin[]" min="1"></td><td><input type="number" name="new_wqrmax[]" min="1"></td><td><select name="new_wqrdiscount_type[]"><option value="fixed">Fixed</option><option value="percentage">Percentage</option></select><input type="number" step="0.01" name="new_wqrdiscount[]"></td><td><select name="new_wqr_roles[][]" id="wqr_roles" multiple>'+html+'</select></td><td><p class="wqrremove">Remove</label></td></tr>');
                           
                    });
                });
            </script>
            <?php
        }
       


        function init() {
            add_filter( 'woocommerce_product_data_tabs', array($this, 'DPFW_custom_product_tabs') );
            add_action( 'woocommerce_product_data_panels', array($this, 'DPFW_custom_product_tabs_fields') );
            add_action( 'woocommerce_process_product_meta', array($this, 'DPFW_save_proddata_custom_fields') );
            add_action( 'admin_footer', array($this, 'DPFW_foot_script') );
        }


        public static function DPFW_instance() {
            if (!isset(self::$DPFW_instance)) {
                self::$DPFW_instance = new self();
                self::$DPFW_instance->init();
            }
            return self::$DPFW_instance;
        }
    }
    DPFW_menu::DPFW_instance();
}
