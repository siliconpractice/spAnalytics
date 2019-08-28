<?php
/**
 * Template Name: Silicon Practice Dashboard
 */
 ob_start();
?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7 ieold" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8 ieold" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
  <!--<![endif]-->
  <head>	
    <meta charset="<?php bloginfo( 'charset' ); ?>">	
    <meta name="viewport" content="width=device-width, initial-scale=1">	
    <title>
      <?php wp_title( '|', true, 'right' ); ?>
    </title>	
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300' rel='stylesheet' type='text/css'>	
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/fonts/fontello-footfall/css/fontello.css">	
    <!--[if IE 7]>
        <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/fonts/fontello-footfall/css/fontello-ie7.css">
        <![endif]-->	
    <!--[if lt IE 9]>
    	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>
    	<![endif]-->	
  <?php 
    wp_head();
	  wp_enqueue_script('velocity-js', get_template_directory_uri() . '/js/velocity.min.js',array('jquery'));
    wp_enqueue_script('velocity-ui-js', get_template_directory_uri() . '/js/velocity.ui.min.js',array('velocity-js'));
      ?>
     
  </head>
  <body <?php body_class(); ?>>   
    <div class="outterpage">  
      <div class="innerpage">       
        <!-- START OF MAIN CONTENT -->   
        <div id='dashboard_hdr'>		   		  
          <div id="header-logo">
            <img id='dashboardlogo' src="<?php echo plugins_url() . '/spForms/images/footfalldashboard.png'; ?>">				
            <h1 class="site-title"><?php echo get_bloginfo( 'name' ); ?></h1>			
          </div> <!-- #header-logo -->
          <?php 
          if (is_user_logged_in() && current_user_can('dashboard_login')) {  ?>
  	          <div id='currentuserinfo'>
              <?php echo show_user_info(); ?>
              </div>
          <?php }  ?>
          		 	
        </div>		
        <div class="dashboard_content">			
          <?php 
            dashboard_page_stub();
          ?>
           <a class="lost-password-dash" href='<?php echo wp_lostpassword_url( $redirect ); ?>'>Lost your password?</a> 		
        </div>   <!-- entry-content -->	 
      </div> <!-- innerpage -->	
    </div> <!-- outerpage -->		
    <?php wp_footer(); ?> 
  </body>
</html>
<?php ob_end_flush(); ?>