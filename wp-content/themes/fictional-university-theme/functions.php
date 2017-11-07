<?php
	function get_id_by_slug($page_slug) {
	    $page = get_page_by_path($page_slug);
	    if ($page) {
	        return $page->ID;
	    } else {
	        return null;
	    }
	}
	function pageBanner($args = NULL) {
		if (!$args['title']) {
			$args['title'] = get_the_title();
		}
		if (!$args['subtitle']) {
			$args['subtitle'] = get_field('page_banner_subtitle');
		}
		if (!$args['image']) {
			$pageBannerImage = get_field('page_banner_background_image');
			if ($pageBannerImage) {
				$args['image'] = $pageBannerImage['sizes']['pageBanner']; 
			} else {
				$args['image'] = get_theme_file_uri('/images/ocean.jpg');
			}
		}

		// Drop out of php into html
		?>
		<div class="page-banner">
			<div class="page-banner__bg-image" 
				style=
					"background-image: url(
					<?php
						echo $args['image']; 					
					?>);
					">		
			</div>
			<div class="page-banner__content container container--narrow">
		 		<h1 class="page-banner__title"><?php echo $args['title']; ?></h1>
				<div class="page-banner__intro">
					<p><?php echo $args['subtitle']; ?></p>
				</div>
			</div>  
		</div>
		<!-- Drop back into PHP here -->
		<?php
	}  
	function university_files()
	{
		wp_enqueue_script('main-university-js', get_theme_file_uri('js/scripts-bundled.js'), NULL, '1.0', true);
		wp_enqueue_style('custom-google-font', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
		wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
		wp_enqueue_style('university_main_styles', get_stylesheet_uri());
	}
	add_action('wp_enqueue_scripts', 'university_files');

	function university_features() {
		add_theme_support('title-tag');
		// enable featured images for blog posts
		add_theme_support('post-thumbnails');
		add_image_size('professorLandscape', 400, 260, true);
		add_image_size('professorPortrait', 480, 650, true);
		add_image_size('pageBanner', 1500, 350, true);
	}

	add_action('after_setup_theme', 'university_features');

	function university_adjust_queries($query) {
		// We only want to manipulate the events archive page - not the admin pages, and not custom queries
		if (!is_admin() and is_post_type_archive('event') and is_main_query()) {
			$today = date('Ymd');

			// sort by event date in ascending order           
			$query->set('meta_key', 'event_date');
			$query->set('orderby', 'meta_value_num');
			$query->set('order', 'ASC');
			
			// filter to event date greater than or equal to todays date
            // custom event date field is stored as YYYYMMDD
            $query->set('meta_query', array(
              array(
                'key' => 'event_date',
                'compare' => '>=',
                'value' => $today,
                'type' => 'numeric'
              )
            ));
		}
		if (!is_admin() and is_post_type_archive('program') and is_main_query()) {
			$query->set('orderby', 'title');
			$query->set('order', 'ASC');
			$query->set('posts_per_page', -1);
		}
	}
	add_action('pre_get_posts', 'university_adjust_queries');

	function university_map_key($api) {
		$api['key'] = 'AIzaSyBeuIrRBsTSpDg8QDJGZPVYjKEyg2zrWjs';
		return $api;
	}
	add_filter('acf/fields/google_map/api', 'university_map_key');
?>