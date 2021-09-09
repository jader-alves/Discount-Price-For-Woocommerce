<?php

if (!defined('ABSPATH'))
    exit;

if (!class_exists('DPFW_front')) {

    class DPFW_front {

        protected static $instance;
        function DPFW_add_custom_price( $cart_object ) { 
            
            if ( is_admin() && ! defined( 'DOING_AJAX' ) )
            return;

            if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
            return;

            global $post, $woocommerce, $current_user;
            $user_roles = $current_user->roles[0];

            foreach ( $cart_object->cart_contents as $key => $value ) {

                $product_id = $value['product_id'];
                $product = wc_get_product( $product_id );
                $product_vari = wc_get_product($value['variation_id']);
               
                $qty = $value['quantity'];
                

                $minqty = get_post_meta($product_id,'wqrmin',true);
                $maxqty = get_post_meta($product_id,'wqrmax',true);
                $dtype  = get_post_meta($product_id,'wqrdiscount_type',true);
                $discnt = get_post_meta($product_id,'wqrdiscount',true);
                $roles  = get_post_meta($product_id,'wqr_roles',true);

                if($value['variation_id'] != 0){
                    if(!empty($minqty)){
                        foreach ($minqty as $keys => $values) {
                            
                            $min  = $values;
                            $max  = $maxqty[$keys];
                            $role = $roles[$keys];                         
                            if ($role==="" || in_array($user_roles, $role)) {
                                if($min <= $qty && $max >= $qty){
                                    $dtypea  = $dtype[$keys];
                                    $discnta = $discnt[$keys];

                                    $price  = $product_vari->get_price();
                                    $new_price = $this->DPFW_count_price($dtypea, $discnta, $price);
                                    $value['data']->price = $new_price;
                                    $value['data']->set_price($new_price); 
                                }
                         }
                        }
                    } 
                }else{
                    if(!empty($minqty)){
                        foreach ($minqty as $keys => $values) {
                            
                            $min  = $values;
                            $max  = $maxqty[$keys];
                            $role = $roles[$keys];
                            

                            if(empty($roles) || empty($role)) {
                                if($min <= $qty && $max >= $qty) {
                                    $dtypea  = $dtype[$keys];
                                    $discnta = $discnt[$keys];
                                    $price  = $product->get_price();
                                    
                                    $new_price = $this->DPFW_count_price($dtypea, $discnta, $price);
                                    $value['data']->price = $new_price;
                                    $value['data']->set_price($new_price); 
                                }
                            }
                            else if (!empty($role)) {
                                if (in_array($user_roles, $role)) {
                                    if($min <= $qty && $max >= $qty) {
                                        $dtypea  = $dtype[$keys];
                                        $discnta = $discnt[$keys];
                                        $price  = $product->get_price();
                                        
                                        $new_price = $this->DPFW_count_price($dtypea, $discnta, $price);
                                        $value['data']->price = $new_price;
                                        $value['data']->set_price($new_price); 
                                    }
                                }    
                            }
                            else {

                            } 
                        }
                    }  
                }               
            }    
        }


        function DPFW_count_price($dtype, $discnt, $price) {
            if($dtype == "fixed") {
                $prices = $price - $discnt;
            }
            if($dtype == "percentage") {
                $prices = $price - ($price * $discnt / 100);
            }
            return $prices;   
        }


        function DPFW_qtytable(){
            global $product, $current_user;
            $user_roles = $current_user->roles[0];
            $product_id = $product->get_id();
            $minqty = get_post_meta($product_id,'wqrmin',true);
            $maxqty = get_post_meta($product_id,'wqrmax',true);
            $dtype  = get_post_meta($product_id,'wqrdiscount_type',true);
            $discnt = get_post_meta($product_id,'wqrdiscount',true);
            $roles  = get_post_meta($product_id,'wqr_roles',true);
            

            $discount_arr = array();
            if(!empty($minqty)){
                foreach ($minqty as $keys => $values) {
                    
                    $dtypea  = $dtype[$keys];
                    $discnta = $discnt[$keys];
                    $price  = $product->get_price();
                    $new_price = $this->DPFW_count_price($dtypea, $discnta, $price);
                    $role = $roles[$keys];
                    
                    if(empty($roles) || empty($role)) {
                        $discount_arr[] = array(
                            "qty" => $values." - ".$maxqty[$keys],
                            "price" => wc_price($new_price)." ".$this->DPFW_discount_text($dtypea, $discnta)
                        );  
                    }
                    else if (!empty($role)) {
                            if (in_array($user_roles, $role)) {
                            $discount_arr[] = array(
                                "qty" => $values." - ".$maxqty[$keys],
                                "price" => wc_price($new_price)." ".$this->DPFW_discount_text($dtypea, $discnta)
                            );
                        }   
                        
                    } 
                    else {

                    }    
                    
                }
            }
            

            if(!empty($discount_arr)){
                echo "<table class='wqr_qtytable'>"; 
                    echo "<thead>";
                        echo "<tr>";
                            echo "<th>Quantidade</th>";
                            echo "<th>Preço</th>";
                        echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";
                        foreach ($discount_arr as $keys => $values) {
                            echo "<tr>";
                                echo "<td>".$values['qty']."</td>";
                                echo "<td>".$values['price']."</td>";
                            echo "</tr>";
                        }
                    echo "</tbody>";
                echo "</table>"; 
            }       
        }


        function DPFW_discount_text($dtype, $discnt) {
            if($dtype == "fixed") {
                $text = "(".wc_price($discnt)." de desconto)";
            }
            if($dtype == "percentage") {
                $text = "(".$discnt." % de desconto)";
            }
            return $text;
        }


        function init() {
            add_action( 'woocommerce_before_calculate_totals', array($this, 'DPFW_add_custom_price' ));
            add_action( 'woocommerce_before_add_to_cart_form', array($this, 'DPFW_qtytable' ));
        }


        public static function instance() {
            if (!isset(self::$instance)) {
                self::$instance = new self();
                self::$instance->init();
            }
            return self::$instance;
        }
    }
    DPFW_front::instance();
}


