<?php

/*
Plugin Name: mm_Users
Plugin URI: http://muscle-and-motion-server-side.localhost/
Description: Create control table to users.
Author: Maxim Gavriushenko
Version: 1.0
Author URI: http://muscle-and-motion-server-side.localhost/
*/
//
//include('class_databasemm.php');
//include('class_usersession.php');


class Admin_Users  {


/******************************************************************************************
HOOKS
 *******************************************************************************************/


    function __construct() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'my_scripts_method'));
        add_action('wp_ajax_insert_user',  array($this, 'insert_user'));
        add_action('wp_ajax_update_user',  array($this, 'update_user'));
        add_action( 'wp_ajax_delete_user',  array( $this, 'delete_user'));
    }


/******************************************************************************************
SCRIPTS AND STYLES
 *******************************************************************************************/


    function my_scripts_method() {
        wp_register_script('users-js', plugins_url().'/mm_users/libs/main.js');
        wp_enqueue_script('users-js');
        wp_register_style('users-css', plugins_url().'/mm_users/libs/main.css');
        wp_enqueue_style('users-css');
    }


/******************************************************************************************
ADMIN SETTINGS
 *******************************************************************************************/


    function admin_menu () {
        add_options_page( 'M&M Users','M&M Users','manage_options','options_page_slug', array( $this, 'settings_page' ) );
    }


    function  settings_page () {
        global $wpdb;
        $users = $wpdb->get_results
            ( "SELECT * FROM stgfit_mm_users" );?>

        <div class = "new_user">
            <button class ="insert_button">Insert new user</button>
                <div class = "new_user_form">
                    <form method="post" action="mm_users.php" id="new_form">
                        <input type="text" placeholder = "Users name" name="user_name">
                        <input type="text" placeholder = "Users email" name="user_email">
                        <input type = "text" placeholder = "Password" size="50" name="user_pass">
<!--                        <input type = "text" placeholder = "Users status payment" name = "user_status_payment">-->
                        <input type = "text" placeholder = "User organization" name = "mm_user_org">
                        <button class = "save_form">Save</button>
                    </form>
                </div>
        </div>

        <div id ="admin_users_table">
        <center><h2>Admin users table</h2></center>
        <table border="1" cellspacing="0">
            <tr>
                <th>Users ID</th>
                <th>Users name</th>
                <th>Users email</th>
                <th>Users pass</th>
<!--                <th>Pass not hash</th>-->
                <th>Users status payment</th>
                <th>User organization</th>
                <th>Actions</th>
            </tr>
        <?php foreach ($users as $user):?>
            <?php
            $check_payment = $this->check_user_payment_status($user->ID);
            if(empty($check_payment))
                $paid = 'no';
            else
                $paid = 'yes';
            ?>


            <tr data-id="<?php echo $user->ID?>">
                <td data-name="id"><?php echo $user->ID?></td>
                <td data-name="name"><?php echo $user->mm_user_name?></td>
                <td data-name="email"><?php echo $user->mm_user_email?></td>
                <td data-name="password" class="user_password"><?php echo $user->mm_pass_not_hash?></td>
                <td data-name="payment-status"><center><?php echo $paid ?></center></td>
                <td data-name="organization"><?php echo $user->mm_user_org?></td>
                <td data-name="actions">
                    <button class ="edit_button">Edit</button>
                    <button class ="delete_button">Delete</button>
                </td>
            </tr>
        <?php endforeach;?>

        </table>
        <?php
    }


/******************************************************************************************
METHODS
 *******************************************************************************************/


    public static function get_user_auth_key($email){
        global $wpdb;
        $row = $wpdb->get_results
            ('SELECT `mm_user_authkey` FROM `stgfit_mm_users` WHERE `mm_user_email` = "' . $email . '"');
        //var_dump($row[0]->mm_user_authkey);
        if ($row[0]->mm_user_authkey !== '') {
            return true;
        } else {
            return false;
        }
    }


    public static function delete_user (){
        global $wpdb;
        $del_user = $wpdb-> delete
            ('stgfit_mm_users',
                array(
                    'ID' => $_POST['user_id'] )
        );
        exit;
    }


    public static function update_user (){
        global $wpdb;
        $wpdb->update(
            'stgfit_mm_users',
                array(
                    'mm_user_name' => $_POST['user_name'],
                    'mm_user_email' => $_POST['user_email'],
                    'mm_user_org' => $_POST['user_org'],
                    'mm_user_pass' => password_hash(trim($_POST['mm_pass']), PASSWORD_DEFAULT),
                    'mm_pass_not_hash' => $_POST['mm_pass'],
                ),
                array(
                    'ID' => $_POST['id']
                )
            );

        }


    public static function insert_user(){
        global $wpdb;
        $wpdb->insert(
            'stgfit_mm_users',
            array(
                'ID' => 'ID',
                'mm_user_name' => $_POST['user_name'],
                'mm_user_email' => $_POST['user_email'],
                'mm_user_pass' => password_hash(trim($_POST['user_pass']), PASSWORD_DEFAULT),
                'mm_pass_not_hash' => $_POST['user_pass'],
                'mm_user_org' => $_POST['mm_user_org'],
                'mm_user_authkey' => self::generate_user_auth_key(),
                'mm_user_registered' => date('Y-m-d H:i:s')
            )
        );
    }

    public static function generate_user_auth_key($len = 10){
        $bytes = openssl_random_pseudo_bytes($len);
        $hex = bin2hex($bytes);
        return $hex;
    }



    public function check_user_payment_status($id){
        global $wpdb;
        $time = time();
        $val1 = "RECURRING";
        $val2 = "CHARGE";
        $query = $wpdb->get_results('SELECT pu_contract_id FROM mm_users_mmpayment WHERE pu_user_id = "' . $id . '" AND pu_expire_time < '.$time.' AND (pu_transactionType = "'.$val1.'" OR pu_transactionType = "'.$val2.'")');
        //$row = $wpdb->get_results($query);
        return $query;

    }
}


+
$admin_users    =  new Admin_Users();