<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
	<base href="<?php $url_info = parse_url( home_url() ); echo trailingslashit( $url_info['path'] ); ?>">
	<title>Error</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body>
     
  <h1>Notfound</h1>     
  
  <?php wp_footer(); ?>
</body>
</html>