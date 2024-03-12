<?php
/*
Plugin Name: Query Management System
Description: This plugin will help to create the essential tables in database and add functionality to register and login users and showing them the data.
Version: 1.0
Author:  Team of four: (Abdullah Sarfraz, Ruqyya, Ahmad)
Author URI: http://querymanagement.local/
*/
?>

<?php

//css enqueuing
function enqueue_your_files() {
	// css
	wp_enqueue_style('main-stylesheet', get_template_directory_uri() . "/plugins/style.css");
}
add_action( 'wp_enqueue_scripts', 'enqueue_your_files' );
// DATABASE 
function create_table_for_registeration_on_activation() {
    global $wpdb;

    // Define the table name with the WordPress prefix
    $table_name = $wpdb->prefix . 'registeration';

    $sql = "CREATE TABLE $table_name (
		id int(11) NOT NULL AUTO_INCREMENT,
		name varchar(50) NOT NULL,
        phone varchar(100) NOT NULL,
		email varchar(100) NOT NULL,
		password varchar(255) NOT NULL,
        roll varchar(100) NOT NULL DEFAULT 'user',
		PRIMARY KEY (id)
	  );";

    
    $wpdb->query($sql);
}

register_activation_hook(__FILE__, 'create_table_for_registeration_on_activation');

function registeration_shortcode() {
    ?>

        <form id="myRegisterationForm" action="<?php echo esc_attr( admin_url('admin-post.php') ); ?>" method="POST">
                <input type="hidden" name="action" value="<?php echo esc_attr( 'save_my_custom_form2' ); ?>" />
                                <div class="employee-part">    
                                <label for="name" style="color: #000; font-weight: 600; ">Name:</label>
                                <br>    
                                <input type="text" placeholder="Enter your Name" style="width: 26%; margin-top: 5px; border-radius: 8px; padding: 5px 10px;" name="name" id="iname" required/>  

                                <br>    
                                <br>   
                                <label for="a1" style="color: #000; font-weight: 600; margin-top: 5px; border-radius: 8px; padding: 5px 10px;">Contact Number:</label>  
                                <br>  
                            
                                <input type="text" placeholder="Enter your valid Phone Number" id="a1" required style="width: 26%; margin-top: 5px; border-radius: 8px; padding: 5px 10px;" name="phone" />
                            <br>   
                            <br> 
                            <label for="e1" style="color: #000; font-weight: 600;  margin-top: 5px; border-radius: 8px; padding: 5px 10px;">Email:</label>
                            <br>
                            
                            <input type="int" placeholder="Enter your valid Email" id="e1" required style="width: 26%; margin-top: 5px; border-radius: 8px; padding: 5px 10px;" name="email" />
                            <br>
                            <br> 
                            <label for="b2" style="color: #000; font-weight: 600;  margin-top: 5px; border-radius: 8px; padding: 5px 10px;">Password:</label>
                            <br>
                            
                            <input type="password" placeholder="Enter your valid Password" id="b2" required style="width: 26%; margin-top: 5px; border-radius: 8px; padding: 5px 10px;" name="password" />
                            <br>
                            <br> 
                            <label for="b3" style="color: #000; font-weight: 600;  margin-top: 5px; border-radius: 8px; padding: 5px 10px;">Confirm Password:</label>
                            <br>
                        
                            <input type="password" placeholder="Enter your valid Password" id="b3" required style="width: 26%; margin-top: 5px; border-radius: 8px; padding: 5px 10px;" name="confirmpw" />
                            <br>
                            <br> 
                        
                            <input type="submit" name="registerbtn" value="Register" style="padding: 8px 25px; border-radius: 14px; color: #fff; background-color: green;">
                            <br>
                        </div>  
        </form>
    <?php   
}

add_shortcode('my_registeration_shortcode', 'registeration_shortcode');


//Inserting data into table For registeration form
function save_my_custom_form2() {
	global $wpdb;
    $table_name = $wpdb->prefix . 'registeration';

	$name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $confirmpw = $_POST['confirmpw'];

    // email already exists or not
    $existing_email = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE email = %s", $email));

    if ($existing_email > 0) {
        
        echo "<script>alert('Email already registered, try again !'); window.location.href = '" . site_url('/registerationform') . "';</script>";
        exit;
        
    } else {

        if (password_verify($confirmpw, $password)) {

                // Passwords match, proceed with insertion
            $check = $wpdb->insert(
                        $table_name,
                    $data = array(
                        'name' => $name,
                        'phone' => $phone,
                        'email'    => $email,
                        'password' => $password,
                    ),
                    array( '%s', '%s', '%s', '%s' )
                    );

                    if ($check) {

                        echo "<script>alert('Your registration is done successfully. Now you can login !'); window.location.href = '" . site_url('/loginform') . "';</script>";
                        exit;

                    } else {

                        echo "<script>alert('Data not inserted: " . $wpdb->last_error . "')</script>";
                        $wpdb->print_error();
                    }        
            
        } else {
            
            echo "<script>alert('Passwords do not match, try again !'); window.location.href = '" . site_url('/registerationform') . "';</script>";
            exit;
}}}

add_action( 'admin_post_nopriv_save_my_custom_form2', 'save_my_custom_form2' );
add_action( 'admin_post_save_my_custom_form2', 'save_my_custom_form2' );


// Wordpress login page redirection

function custom_login_redirect( $redirect_to, $request, $user ) {
    // Check if the user has roles and if the roles array is not empty
    if ( isset( $user->roles ) && is_array( $user->roles ) && ! empty( $user->roles ) ) {
    // Get the current user's role
    $user_role = $user->roles[0];
 
    // Set the URL to redirect users to based on their role
    if ( $user_role == 'subscriber' ) {
        $redirect_to = '/employeedashboard/';
    } 
}
    return $redirect_to;
}
add_filter( 'login_redirect', 'custom_login_redirect', 10, 3 );

// DATABASE for Queries
function create_table_for_queries_on_activation() {
    global $wpdb;

    // Define the table name with the WordPress prefix
    $table_name = $wpdb->prefix . 'queryform';

    $sql = "CREATE TABLE $table_name (
		id int(11) NOT NULL AUTO_INCREMENT,
        name varchar(50) NOT NULL DEFAULT 'Anonymous',
        email varchar(100) NOT NULL DEFAULT 'Anonymous',
		category VARCHAR(100) NOT NULL DEFAULT 'Anonymous',
        priorty VARCHAR(100) NOT NULL DEFAULT 'not defined',
        status VARCHAR(100) NOT NULL DEFAULT 'not defined',
        description TEXT NOT NULL,
        answers TEXT NOT NULL,
        file_path VARCHAR(255) NULL  DEFAULT 'none.jpg',
		PRIMARY KEY (id)
	  );";

    // Execute the SQL query to create the voter table
    $wpdb->query($sql);
}

register_activation_hook(__FILE__, 'create_table_for_queries_on_activation');

//creating shortcode of the Query Form page
function querform_shortcode() {
    ?>

                        </div class="main-form" >
                            <form id="myQueryForm" action="<?php echo esc_attr( admin_url('admin-post.php') ); ?>" method="POST">
                                <input type="hidden" name="action" value="<?php echo esc_attr( 'save_my_custom_form4' ); ?>" />
                                        <br>
                                   
                                        <label for="name" style="color: #000; font-weight: 600; margin-right: 250px;">Employee Name:</label>
                                        <br>    
                                        <input type="text" placeholder="Enter your Name" style="width: 26%; margin-top: 5px; border-radius: 8px; padding: 5px 10px;" id="name" name="name" />  
                                        <br>    
                                        <br>   
                                        <label for="e1" style="color: #000; font-weight: 600; margin-top: 5px; margin-right: 330px;">Email:</label>
                                        <br>
                                        <input type="int" placeholder="Enter your valid email" id="e1" style="width: 26%; margin-top: 5px; border-radius: 8px; padding: 5px 10px;" name="email" />
                                        <br>
                                        <br> 
                                        <label for="category" style="color: #000; font-weight: 600; margin-top: 5px;">Query Category:</label>
                                        <select name="category" id="category" style="border: none; outline: none;">
                                            <option value="General">General</option>
                                            <option value="Technical">Technical</option>
                                            <option value="Hardware">Hardware</option>
                                            <option value="Software">Software</option>
                                        </select>
                                        <label for="priority" style="color: #000; font-weight: 600; margin-top: 5px;">Priority:</label>
                                        <select name="priority" id="priority" style="border: none; outline: none;">
                                            <option value="High">High</option>
                                            <option value="Medium">Medium</option>
                                            <option value="Low">Low</option>
                                        </select>
                                        <br>
                                        <br> 
                                        <label for="desc" style="color: #000; font-weight: 600; margin-top: 5px; margin-right: 285px;">Description:</label>
                                        <br>
                                        <br> 
                                        <textarea id="desc" name="desc" rows="4" cols="50" style=" outline: none;">   
                                        </textarea>
                                        <br>
                                        <br> 
                                        <input type="file" id="myfile" name="myfile">
                                        <br>
                                        <br> 
                                        <input type="submit" name="querybtn_one" value="Submit Query" style="padding: 8px 25px; border-radius: 14px; color: #fff; background-color: green;">
                                        <br>
                                 
                            </form>
                            <button class="anonymous">Send a Anonymous Query</button>
                        </div>   

                    <!-- Second Anonymous Form -->
                        <div class="second-form" style=" display: none;">
                            <form id="myqueryForm1" action="<?php echo esc_attr( admin_url('admin-post.php') ); ?>" method="POST">
                                <input type="hidden" name="action" value="<?php echo esc_attr( 'save_my_custom_form5' ); ?>" />
                                <h2 style="color: blue;">Fill the Anonymous Form</h2>
                                <br>
                                   
                                   <label for="name" style="color: #000; font-weight: 600; margin-right: 250px;">Employee Name: Anonymous</label>
                                   <br>    
                                   <input type="hidden" placeholder="Enter your Name" style="width: 26%; margin-top: 5px; border-radius: 8px; padding: 5px 10px;" id="name" name="name" value="anonymous"/>  
                                   <br>    
                                   <br>   
                                   <label for="e1" style="color: #000; font-weight: 600; margin-top: 5px; margin-right: 330px;">Email:</label>
                                   <br>
                                   <input type="int" placeholder="Enter your valid email" id="e1" style="width: 26%; margin-top: 5px; border-radius: 8px; padding: 5px 10px;" name="email" />
                                   <br>
                                   <br>
                            
                                <br> 
                               
                                
                                <br>
                                <br> 
                                <label for="category" style="color: #000; font-weight: 600; margin-top: 5px;">Query Category:</label>
                                <select name="category" id="category" style="border: none; outline: none;">
                                    <option value="General">General</option>
                                    <option value="Technical">Technical</option>
                                    <option value="Hardware">Hardware</option>
                                    <option value="Software">Software</option>
                                </select>
                                <label for="priority" style="color: #000; font-weight: 600; margin-top: 5px;">Priority:</label>
                                <select name="priority" id="priority" style="border: none; outline: none;">
                                    <option value="High">High</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Low">Low</option>
                                </select>
                                <br>
                                <br>
                                
                                <label for="desc" style="color: #000; font-weight: 600; margin-top: 5px; margin-right: 285px;">Description:</label>
                                <br>
                                <br> 
                                <textarea id="desc" name="desc" rows="4" cols="50" style=" outline: none;">   
                                </textarea>
                                <br>
                                <br> 
                                <input type="file" id="myfile" name="myfile">
                                <br>
                                <br> 
                                <input type="submit" name="querybtn_second" value="Submit Query" style="padding: 8px 25px; border-radius: 14px; color: #fff; background-color: green;">
                                <br>

                            
                            </form>

                            
                        </div>

                        <script>

                        const secondDiv = document.getElementsByClassName ('second-form')[0];
                        const leftClick = document.getElementsByClassName ('anonymous')[0];

                        leftClick.addEventListener('click', ()=> {
                            
                            secondDiv.style.display = 'block';
                        });

                        </script>
    <?php   
}

add_shortcode('my_queryform_shortcode', 'querform_shortcode');

//Query Form Submittion with name
function save_my_custom_form4() {
	global $wpdb;
    $table_name = $wpdb->prefix . 'queryform';

	$name = $_POST['name'];
    $email = $_POST['email'];
    $category = $_POST['category'];
    $priority = ($_POST['priority']);
    $desc = $_POST['desc'];
    $myfile = $_POST['myfile'];
    

            $check = $wpdb->insert(
                        $table_name,
                    $data = array(
                        'name' => $name,
                        'email' => $email,
                        'category' => $category,
                        'priorty' => $priority,
                        'description' => $desc,
                        'file_path' => $myfile,

                    ),
                    array( '%s', '%s', '%s', '%s','%s', '%s', '%s', )
                    );

                    if ($check) {

                        echo "<script>alert('Your Query Submitted !'); window.location.href = '" . site_url('/query-form') . "';</script>";
                        exit;

                    } else {

                        echo "<script>alert('Data not inserted: " . $wpdb->last_error . "')</script>";
                        $wpdb->print_error();
                    }        
            
        
                }
add_action( 'admin_post_nopriv_save_my_custom_form4', 'save_my_custom_form4' );
add_action( 'admin_post_save_my_custom_form4', 'save_my_custom_form4' );


//Query Form Submittion without name
function save_my_custom_form5() {
	global $wpdb;
    $table_name = $wpdb->prefix . 'queryform';

	$name = $_POST['name'];
    $email = $_POST['email'];
    $category = $_POST['category'];
    $priority = ($_POST['priority']);
    $desc = $_POST['desc'];
    $myfile = $_POST['myfile'];
    

            $check = $wpdb->insert(
                        $table_name,
                        $data = array(
                           
                            'name' => $name,
                            'email' => $email,
                            'category' => $category,
                            'priorty' => $priority,
                            'description' => $desc,
                            'file_path' => $myfile,
    
                        ),
                        array( '%s', '%s', '%s', '%s', '%s', '%s' )
                        );

                    if ($check) {

                        echo "<script>alert('Your Query Submitted !'); window.location.href = '" . site_url('/query-form') . "';</script>";
                        exit;

                    } else {

                        echo "<script>alert('Data not inserted: " . $wpdb->last_error . "')</script>";
                        $wpdb->print_error();
                    }        
            
        
                }
add_action( 'admin_post_nopriv_save_my_custom_form5', 'save_my_custom_form5' );
add_action( 'admin_post_save_my_custom_form5', 'save_my_custom_form5' );


//creating shortcode for the Employee Dashboard
function employee_shortcode() {

if (is_user_logged_in()) {
    // Get the current user object
    $current_user = wp_get_current_user();

    // Access user data
    $user_name = $current_user->display_name;
    $user_email = $current_user->user_email;

    // Display user data
    $live_user = $user_email;
}
                                                   
?>
    <div class="top-header" style=" background-color: green; padding-top: 10px; padding-bottom: 10px;">
    <p>Hello,
                    <?php 
                    // Check if the user is logged in
    // Get the current user ID
    $user_id = get_current_user_id();

    // Get user meta data
    $user_name = get_user_meta($user_id, 'user_data_name', true);
    $user_email = get_user_meta($user_id, 'user_data_email', true);

    // Check if the user meta data exists
    if ($user_name && $user_email) {
        // Display user data
        $live_user = $user_email;
    }
                    ?>
        <h3 style=" color: #fff; font-size: 16px; text-align: center; font-family: 'oswald', sans-serif;">Employee Queries Portal</h3>
    </div>
    
    <div class="emp-queries">
        <div style=" margin-top: 30px; text-align: center; margin-left: 1000px;">
            <form method="post" >
                <button type="submit" name="logout" style="color: #fff; text-decoration: none; padding: 5px 10px; background-color: purple; border-radius: 14px; border: none; outline: none;">Logout</button>
            </form>    
        </div>
      
<?php
    if (isset($_POST['logout']) ) {
        wp_redirect(home_url('/wp-login.php'));
        exit();
    }
?>
        <div class="query-btn" style=" margin-top: 30px; text-align: center; margin-right: 1000px;">
            <a href="<?php echo esc_url( site_url( '/query-form' ) ); ?>" style="color: #fff; text-decoration: none; padding: 5px 10px; background-color: purple; border-radius: 14px; border: none; outline: none;">Create a Query</a>
        </div>
        <br>

        <div class="queries-table">
            <h4 style="font-size: 32px; text-align: center; font-family: 'oswald', sans-serif;">Your Queries</h4>
            <br>
            <br>
            <table style=" font-size: 18px; font-family: 'oswald', sans-serif; border-collapse: collapse; width: 83%; margin-left: 110px;">
                <tr>
                  <th name="id" style="border: 1px solid skyblue; background-color: skyblue;  color: #fff; padding-top: 5px; padding-right: 2px;">ID</th>         
                  <th name="category" style="border: 1px solid skyblue; background-color: skyblue;  color: #fff; padding-top: 5px; padding-right: 2px;">category</th>
                  <th name="prty" style="border: 1px solid skyblue; background-color: skyblue;  color: #fff; padding-top: 5px; padding-right: 2px;">Priority</th>
                  <th name="des" style="border: 1px solid skyblue; background-color: skyblue;  color: #fff; padding-top: 5px; padding-right: 2px;">Description</th>
                  <th name="status" style="border: 1px solid skyblue; background-color: skyblue;  color: #fff; padding-top: 5px; padding-right: 2px;">Status</th>        
                  <th name="answer" style="border: 1px solid skyblue; background-color: skyblue;  color: #fff; padding-top: 5px; padding-right: 2px;">Answer</th>    
                  <th name="answer" style="border: 1px solid skyblue; background-color: skyblue;  color: #fff; padding-top: 5px; padding-right: 2px;">Delete</th>    
                </tr>
                    <?php

                        global $wpdb;
                        $table_name = $wpdb->prefix . 'queryform';

                        

                        // Using $wpdb->prepare to safely insert the variable into the query
                        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE email = %s", $live_user);
                        // Fetch data using $wpdb with Array accociate pattern means
                        // that the result will be an associative array where the column names are used as keys.
                        $rows = $wpdb->get_results($query, ARRAY_A);

                        foreach ($rows as $row) {
                            $id = $row["id"];
                            $category = $row["category"];
                            $description = $row["description"];
                            $answers = $row["answers"];
                            $status = $row["status"];
                            $priorty = $row["priorty"];
                        
                            echo "
                                <tr>
                                <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$id</th>
                                <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$category</th>
                                <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$priorty</th>
                                <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$description</th>
                                <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$status</th>
                                <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$answers</th>
                                <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>
                                    <form method='post' action=''>
                                        <input type='hidden' name='delete_id' value='$id'>
                                        <button type='submit' name='delete_row'>Delete</button>
                                    </form>
                                
                                </th>
                                
                                </tr>
                            ";       
                                
                        }
                    ?>

<?php
    if (isset($_POST['delete_row'])) {
        $delete_id = $_POST['delete_id'];
    
        // Perform the delete operation
        $delete_query = $wpdb->prepare("DELETE FROM $table_name WHERE id = %d", $delete_id);
        $wpdb->query($delete_query);
    
        if ($wpdb->rows_affected > 0) {
            
            echo "<script>alert('Row Deleted Sucessfully'); window.location.href = '" . site_url('/employeedashboard') . "';</script>";
            
        } else {
            
            echo "<script>alert('No rows deleted. Row with ID $delete_id may not exist.'); window.location.href = '" . site_url('/employeedashboard') . "';</script>";

        }
    }

   ?>           
              </table>
        </div>

        
    </div>

    <?php
}

add_shortcode('employee_shortcode', 'employee_shortcode');

// shortcode for the HR Dashboard page
function hrdashboard_shortcode() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'queryform';

    $total = "SELECT COUNT(*) FROM $table_name";
    $total_tickets = $wpdb->get_var($total);

    $status = "not defined";
    $opened = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE status= %s", $status);
    $opened_tickets = $wpdb->get_var($opened);

    $answer = "";
    $answered = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE answers = %s", $answer);
    $not_answered = $wpdb->get_var($answered);
    $answered_tickets = $total_tickets - $not_answered;

    $pend = "Pending";
    $pending = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE status= %s", $pend);
    $pending_tickets = $wpdb->get_var($pending);

    $decl = "Declined";
    $decline = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE status= %s", $decl);
    $decline_tickets = $wpdb->get_var($decline);

    $proc = "In Process";
    $process = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE status= %s", $proc);
    $process_tickets = $wpdb->get_var($process);

    $proces = "Processed";
    $processed = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE status= %s", $proces);
    $processed_tickets = $wpdb->get_var($processed);

?>

<div class="head-section" style="background-color: #4dc3ff; display: flex;">
                
        <div class="hr-main" style="  display: flex; padding-top: 30px; padding-bottom: 30px;">
            <div class="total-tickets" style="border: 3px solid #fff; border-radius: 10px; padding: 10px 20px; margin-left: 100px;">
              <label style="color: #4d4dff ;">Total Tickets</label>
                <br>
                <label style="padding: 15px 30px; color:black;" for="" value="35" name="35"><?php echo $total_tickets; ?></label>
                
            </div>
            <div class="open-tickets" style=" border: 3px solid #fff; border-radius: 10px; margin-left: 10px; padding: 10px 10px;">
                
                <label style="color:#4d4dff;">Open/New Tickets</label>
                <br>
                <label style="padding: 25px 50px; color:black;" for="" value="35" name="35"><?php echo $opened_tickets; ?></label>
            </div>
            <div class="answered" style="border: 3px solid #fff; border-radius: 10px; margin-left: 10px; padding: 10px 10px;">
                
                <label style="color:#4d4dff;">Answered </label>
                <br>
                <label style="padding: 5px 20px; color:black;" for="" value="35" name="35"><?php echo $answered_tickets; ?></label>
            </div>
            
            <div class="pending" style=" border: 3px solid #fff; border-radius: 10px; margin-left: 10px; padding: 10px 10px;">
                
                <label style="color:#4d4dff;">Pending</label>
                <br>
                <label style="padding: 5px 20px; color:black;" for="" value="35" name="35"><?php echo $pending_tickets; ?></label>
            </div>
            <div class="declined" style=" border: 3px solid #fff; border-radius: 10px; margin-left: 10px; padding: 10px 20px;">
                
                <label style="color:#4d4dff;">Declined</label>
                <br>
                <label style="padding: 5px 20px; color:black;" for="" value="35" name="35"><?php echo $decline_tickets; ?></label>
            </div>
            <div class="process" style=" border: 3px solid #fff; border-radius: 10px; margin-left: 10px; padding: 10px 20px;">
                
                <label style="color:#4d4dff;">In Process</label>
                <br>
                <label style="padding: 5px 20px; color:black;" for="" value="35" name="35"><?php echo $process_tickets; ?></label>
            </div>

            <div class="processed" style=" border: 3px solid #fff; border-radius: 10px; margin-left: 10px; padding: 10px 20px;">
                
                <label style="color:#4d4dff;">Processed</label>
                <br>
                <label style="padding: 5px 20px; color:black;" for="" value="35" name="35"><?php echo $processed_tickets; ?></label>
            </div>

        </div>

        <div style=" margin-top: 45px; text-align: center; margin-left: 320px;">
            <form method="post" >
                <button type="submit" name="logout" style="color: #fff; text-decoration: none; padding: 5px 10px; background-color: purple; border-radius: 14px; border: none; outline: none;">Logout</button>
            </form>    

            <?php
                // Check if the logout parameter is present in the URL
                if (isset($_POST['logout']) && $_POST['logout'] == 1) {
                // Call the WordPress logout function
                wp_logout();
            
                // Redirect to the login page
                wp_redirect(home_url('/loginform'));
            
                // Exit to stop further execution
                exit();
            }
           ?>

      
<?php
    if (isset($_POST['logout']) ) {
        wp_redirect(home_url('/wp-login.php'));
        exit();
    }
?>      
        </div>
</div>
    <br><br>

    <div>
        <a href="/reportingsystem" style="color: #fff; text-decoration: none; padding: 5px 10px; background-color: purple; border-radius: 14px; border: none; outline: none;">View and print Reports</a>
    </div>
    <br><br>
    <div class="search-section" style="display: flex;">
        
    </div>
    <br>
    <br>
    <table style="font-size: 18px; font-family: 'oswald', sans-serif; border-collapse: collapse; width: 88%; margin-left: 70px;">
        <tr>
          <th name="name" style=" border: 1px solid skyblue; background-color: #66ccff; color: #fff; padding-top: 5px; padding-right: 2px;">Name</th>
          <th name="email" style=" border: 1px solid skyblue; background-color: #66ccff; color: #fff; padding-top: 5px; padding-right: 2px;">Email</th>
          <th name="details" style=" border: 1px solid skyblue; background-color: #66ccff; color: #fff; padding-top: 5px; padding-right: 2px;">Details</th>
          <th name="category" style=" border: 1px solid skyblue; background-color: #66ccff; color: #fff; padding-top: 5px; padding-right: 2px;">Category</th>
          <th name="status" style=" border: 1px solid skyblue; background-color: #66ccff; color: #fff; padding-top: 5px; padding-right: 2px;">Status</th>
          <th name="priority" style=" border: 1px solid skyblue; background-color: #66ccff; color: #fff; padding-top: 5px; padding-right: 2px;">Priority</th>
          <th name="answer" style=" border: 1px solid skyblue; background-color: #66ccff; color: #fff; padding-top: 5px; padding-right: 2px;">Answer</th>
          <th name="update" style=" border: 1px solid skyblue; background-color: #66ccff; color: #fff; padding-top: 5px; padding-right: 2px;">Update</th>
        </tr>

        <?php

                        global $wpdb;
                        $table_name = $wpdb->prefix . 'queryform';

                        // Using $wpdb->prepare to safely insert the variable into the query
                        $query = $wpdb->prepare("SELECT * FROM $table_name");
                        // Fetch data using $wpdb with Array accociate pattern means
                        // that the result will be an associative array where the column names are used as keys.
                        $rows = $wpdb->get_results($query, ARRAY_A);

                        

                        foreach ($rows as $row) {

                            $queryId = $row['id'];
                            $name = $row["name"];
                            $email = $row["email"];
                            $category = $row["category"];
                            $description = $row["description"];
                            $answers = $row["answers"];
                            $status = $row["status"];
                            $priorty = $row["priorty"];

                            if($name == "anonymous") {
                                    echo "
                                    <tr>
                                    <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>Anonymous</th>
                                    <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>Anonymous</th>
                                    <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$description</th>
                                    <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$category</th>
                                    <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$status</th>
                                    <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$priorty</th>
                                    <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$answers</th>
                                    <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>
                                        <a href='/replyform?id=$queryId'>Update</a>
                                    </tr>
                                "; 
                            }else{
                                echo "
                                <tr>
                                <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$name</th>
                                <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$email</th>
                                <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$description</th>
                                <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$category</th>
                                <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$status</th>
                                <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$priorty</th>
                                <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$answers</th>
                                <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>
                                    <a href='/replyform?id=$queryId'>Update</a>
                                </tr>
                                "; 
                            }
                        
                            

                                
                                
                        } ?>
      </table>



      <?php     
                        
}

add_shortcode('hrdashboard_shortcode', 'hrdashboard_shortcode');


// shortcode for the HR Update form Page
function replyform_shortcode() {
    $test_id = isset($_GET['id']) ? $_GET['id'] : '';
    echo $test_id;

    global $wpdb;
    $table_name = $wpdb->prefix . 'queryform';

    $query = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $test_id);
    $rows = $wpdb->get_results($query);

    foreach ($rows as $row) {
        $name = $row->name;
        $email = $row->email;
        $category = $row->category;
        $priorty = $row->priorty;
        $status = $row->status;
        $description = $row->description;
        $answers = $row->answers;
        $file_path = $row->file_path;
    }
    ?>
    <div class="main-form">
        <form id="updateform" action="<?php echo esc_attr(admin_url('admin-post.php')); ?>" method="POST">
            <input type="hidden" name="action" value="<?php echo esc_attr('save_my_custom_form9'); ?>" />
            <br>
            <label for="name" style="color: #000; font-weight: 600; margin-right: 250px;">Employee Name:</label>
            <br>
            <input type="text" value="<?php echo $name; ?>" placeholder="Enter your Name"
                   style="width: 26%; margin-top: 5px; border-radius: 8px; padding: 5px 10px;" id="name" name="name"/>
            <br>
            <br>
            <label for="e1" style="color: #000; font-weight: 600; margin-top: 5px; margin-right: 330px;">Email:</label>
            <br>
            <input type="text" value="<?php echo $email; ?>" placeholder="Enter your valid email" id="e1"
                   style="width: 26%; margin-top: 5px; border-radius: 8px; padding: 5px 10px;" name="email"/>
            <br>
            <br>
            <label for="category" style="color: #000; font-weight: 600; margin-top: 5px;">Query Category:</label>
            <select name="category" id="category" style="border: none; outline: none;">
                <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                <option value="General">General</option>
                <option value="Technical">Technical</option>
                <option value="Hardware">Hardware</option>
                <option value="Software">Software</option>
            </select>
            <label for="priority" style="color: #000; font-weight: 600; margin-top: 5px;">Priority:</label>
            <select name="priority" id="priority" style="border: none; outline: none;">
                <option value="<?php echo $priorty; ?>"><?php echo $priorty; ?></option>
                <option value="High">High</option>
                <option value="Medium">Medium</option>
                <option value="Low">Low</option>
            </select>
            <br>
            <br>
            <label for="status" style="color: #000; font-weight: 600; margin-top: 5px;">Status:</label>
            <select name="status" id="status" style="border: none; outline: none;">
                <option value="<?php echo $status; ?>"><?php echo $status; ?></option>
                <option value="Pending">Pending</option>
                <option value="Declined">Declined</option>
                <option value="In Process">In Process</option>
            </select>
            <br>
            <br>
            <label for="desc" style="color: #000; font-weight: 600; margin-top: 5px; margin-right: 285px;">Description:</label>
            <br>
            <br>
            <textarea id="desc" name="desc" rows="4" cols="50" style="outline: none;">
                <?php echo $description; ?>
            </textarea>
            <br>
            <br>
            <label for="answers" style="color: #000; font-weight: 600; margin-top: 5px; margin-right: 285px;">Answer:</label>
            <br>
            <br>
            <textarea id="ans" name="answers" rows="4" cols="50" style="outline: none;">
                <?php echo $answers; ?>
            </textarea>
            <br>
            <br>
            <input type="file" id="myfile" name="myfile">
            <label for="myfile">Current File: <?php echo $file_path; ?></label>
            <br>
            <br>
            <input type="hidden" name="id" value="<?php echo $test_id; ?>">
            <input type="submit" name="reply_querybtn_one" value="Update Query"
            style="padding: 8px 25px; border-radius: 14px; color: #fff; background-color: green;">
            <br>
        </form>
    </div>
    <?php
}

add_shortcode('replyform_shortcode', 'replyform_shortcode');

// Query Form Updation
function save_my_custom_form9() {
    $test_id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    global $wpdb;
    $table_name = $wpdb->prefix . 'queryform';

    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_text_field($_POST['email']);
    $category = sanitize_text_field($_POST['category']);
    $priority = sanitize_text_field($_POST['priority']);
    $desc = sanitize_textarea_field($_POST['desc']);
    $myfile = sanitize_text_field($_POST['myfile']);
    $status = sanitize_text_field($_POST['status']);
    $answers = sanitize_textarea_field($_POST['answers']);

    $check = $wpdb->update(
        $table_name,
        array(
            'name' => $name,
            'email' => $email,
            'category' => $category,
            'priorty' => $priority,
            'status' => $status,
            'description' => $desc,
            'answers' => $answers,
            'file_path' => $myfile,
        ),
        array('id' => $test_id),
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'),
        array('%d') // format for the WHERE condition (id is an integer, so use %d)
    );

    if ($check !== false) {
        echo "<script>alert('Your Query Updated!'); window.location.href = '" . site_url('/hrdashboard') . "';</script>";
        exit;
    } else {
        echo "<script>alert('Data not updated: " . $wpdb->last_error . "')</script>";
        $wpdb->print_error();
    }
}

add_action('admin_post_nopriv_save_my_custom_form9', 'save_my_custom_form9');
add_action('admin_post_save_my_custom_form9', 'save_my_custom_form9');


function reportsystem_shortcode() {
    ?>

<h2>Select options to generate a report</h2><br><br>
<form id="myRegisterationForm" action="<?php echo esc_attr( admin_url('admin-post.php') ); ?>" method="POST">
    <input type="hidden" name="action" value="<?php echo esc_attr( 'save_my_custom_form8' ); ?>" />
    <div class="test" style="display:flex; gap:10px;">
        <div>
            <label for="name" style=" font-weight: bold;">Name:</label><br><br>
            <input type="text" name="name" id="name" style=" outline: none;"></input><br><br>
        </div>

        <div>
            <label for="email" style=" font-weight: bold;">Email:</label><br><br>
            <input type="email" name="email" id="email" style=" outline: none;"><br><br>
        </div>

        <div>
            <label for="category" style=" font-weight: bold;">Category:</label><br><br>
            <select name="category" id="category" style=" outline: none;">
                <option value=""></option>
                <option value="General">General</option>
                <option value="Technical">Technical</option>
                <option value="Hardware">Hardware</option>
                <option value="Software">Software</option>
            </select><br><br>
        </div>

        <div>
            <label for="priority" style=" font-weight: bold;">Priority:</label><br><br>
            <select name="priority" id="priority" style=" outline: none;">
                <option value=""></option>
                <option value="High">High</option>
                <option value="Medium">Medium</option>
                <option value="Low">Low</option>
            </select><br><br>
        </div>

        <div>
            <label for="status" style=" font-weight: bold;">Status:</label><br><br>
            <select name="status" id="status" style=" outline: none;">
                <option value=""></option>
                <option value="Pending">Pending</option>
                <option value="Declined">Declined</option>
                <option value="In Process">In Process</option>
            </select>
        </div>

        <div>
            <button class="reportgenerating" type="submit" style="color: #fff; text-decoration: none; margin-left:150px; margin-top:15px; padding: 5px 10px; background-color: purple; border-radius: 14px; border: none; outline: none;">Generate Report</button>
        </div>
    </div>
    

</form>

    <?php
}
add_shortcode('reportsystem_shortcode', 'reportsystem_shortcode');

function save_my_custom_form8() {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $category = $_POST['category'];
    $priority = ($_POST['priority']);
    $status = $_POST['status'];
?>

    
    <div class="reportclass">
        <br>
        <br>
        <table style="font-size: 18px; font-family: 'oswald', sans-serif; border-collapse: collapse; width: 88%; margin-left: 70px;">
            <tr>
            <th name="name" style=" border: 1px solid skyblue; background-color: #66ccff; color: #fff; padding-top: 5px; padding-right: 2px;">Name</th>
            <th name="email" style=" border: 1px solid skyblue; background-color: #66ccff; color: #fff; padding-top: 5px; padding-right: 2px;">Email</th>
            <th name="category" style=" border: 1px solid skyblue; background-color: #66ccff; color: #fff; padding-top: 5px; padding-right: 2px;">Category</th>
            <th name="status" style=" border: 1px solid skyblue; background-color: #66ccff; color: #fff; padding-top: 5px; padding-right: 2px;">Status</th>
            <th name="priority" style=" border: 1px solid skyblue; background-color: #66ccff; color: #fff; padding-top: 5px; padding-right: 2px;">Priority</th>
            <th name="update" style=" border: 1px solid skyblue; background-color: #66ccff; color: #fff; padding-top: 5px; padding-right: 2px;">Update</th>
            </tr>

            <?php

            global $wpdb;
            $table_name = $wpdb->prefix . 'queryform';

            $conditions = array();

            if (!empty($name)) {
                $conditions[] = $wpdb->prepare("name = %s", $name);
            }
            if (!empty($email)) {
                $conditions[] = $wpdb->prepare("email = %s", $email);
            }
            if (!empty($category)) {
                $conditions[] = $wpdb->prepare("category = %s", $category);
            }
            if (!empty($priority)) {
                $conditions[] = $wpdb->prepare("priorty = %s", $priority);
            }
            if (!empty($status)) {
                $conditions[] = $wpdb->prepare("status = %s", $status);
            }

            $where_clause = implode(' AND ', $conditions);

            $query = "SELECT * FROM $table_name";
            if(!empty($where_clause)) {
                $query .= " WHERE $where_clause";
            }
            
            $rows = $wpdb->get_results($query, ARRAY_A);

            

            foreach ($rows as $row) {

                $queryId = $row['id'];
                $name = $row["name"];
                $email = $row["email"];
                $category = $row["category"];
                $status = $row["status"];
                $priorty = $row["priorty"];
            
                echo "
                    <tr>
                    <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$name</th>
                    <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$email</th>
                    <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$category</th>
                    <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$status</th>
                    <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>$priorty</th>
                    <th style='border: 1px solid skyblue; color: #000; padding-top: 5px; padding-right: 2px;'>
                        <a href='/replyform?id=$queryId'>Update</a>
                    </tr>
                "; 

                    
                    
            } ?>
        </table>
    </div>


    <script>
    function printTable() {
        window.print();
    }
    </script>

    <!-- Button to trigger printing -->
    <button style="color: #fff; text-decoration: none; margin-left:150px; margin-top:15px; padding: 5px 10px; background-color: purple; border-radius: 14px; border: none; outline: none;" onclick="printTable()">Print/Download Table</button>
    <?php
}
add_action('admin_post_nopriv_save_my_custom_form8', 'save_my_custom_form8');
add_action('admin_post_save_my_custom_form8', 'save_my_custom_form8');
?>

