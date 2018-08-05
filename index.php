<!DOCTYPE html>
<html  ng-app="app">
<head>
  <meta charset="utf-8">
	<base href="<?php $url_info = parse_url( home_url() ); echo trailingslashit( $url_info['path'] ); ?>">
	<title state-title="Afriflow" pattern="%s | Afriflow"></title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body>
<toast></toast>
     
  <ui-view id="main"></ui-view>      
  
  <?php wp_footer(); ?>
</body>
</html>