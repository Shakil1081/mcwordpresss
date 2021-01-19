<?	
// Home Page
require_once("wp-blog-header.php");



if($_REQUEST['secret'] !== "ci6Qc0uWtR9Bj9isORmn") {
	out(array("error" => "Bad secret"));
}

if($_REQUEST['type'] == "vendors") {
	
	 $args = array(
        'type' => 'product',
        'child_of' => 0,
        'parent' => 0,
        'orderby' => 'name',
        'order' => 'ASC',
        'hide_empty' => 0,
        'hierarchical' => 1,
        'exclude' => '',
        'include' => '',
        'number' => '',
        'taxonomy' => 'product_cat',
        'pad_counts' => false,
    );
    $prodCats = get_categories($args);
    
    foreach($prodCats as $p) {
	    $out[] = array("id" => $p->term_id, "name" => $p->name);
    }
	
	out(array("payload" => $out));
}

if($_REQUEST['type'] == "certifications") {
	$catObject = get_term_by('name', $_REQUEST['vendor'], 'product_cat');
	global $wpdb; 


	$subCats = $wpdb->get_results("select t.name,t.slug,t.term_id,tt.count from $wpdb->terms t join $wpdb->term_taxonomy tt on t.term_id = tt.term_id where tt.taxonomy = 'product_cat' and tt.parent = " . $catObject->term_id . " order by t.name asc");

	if (count($subCats) > 0) {
		foreach($subCats as $cat) {
			$out[] = array("id" => $cat->term_id, "name" => $cat->name);
		}
		
		out(array("payload" => $out));
	}
}

if($_REQUEST['type'] == "exams") {

	// Getting Parent ID
	$parent = get_term_by('name', $_REQUEST['vendor'], 'product_cat');
	// Checking if SubCategory Exist. 
	$term = term_exists($_REQUEST['certification'], 'product_cat', $parent->term_id);

	//if Exist Do the query
	if ($term !== 0 && $term !== null) {
		$args = array(
			'posts_per_page'   => 500,
			'tax_query' => array(
			    array(
			      'taxonomy' => 'product_cat',
			      'field' => 'id',
			      'terms' => $term['term_id'], // Where term_id of Term 1 is "1".
			      'include_children' => false
			    )
			  ),
			'post_type' => 'product'
		);
		
		$posts_array = get_posts($args);
		
		if(count($posts_array) > 0) {
			foreach($posts_array as $post) {
				$out[] = array("id" => $post->ID, "name" => $post->post_title);
			}
			
			out(array("payload" => $out));
		}
	} else {
		out(array("error" => "vendor or certification doesnt exist"));
	}
}
if($_REQUEST['type'] == "get_exam") {
	$parent = get_term_by('name', $_REQUEST['vendor'], 'product_cat');
	// Checking if SubCategory Exist. 
	$term = term_exists($_REQUEST['certification'], 'product_cat', $parent->term_id);
	//if Exist Do the query
	if ($term !== 0 && $term !== null) {
		//vendor and certification exist
		$args = array(
			'posts_per_page'   => 500,
			'tax_query' => array(
			    array(
			      'taxonomy' => 'product_cat',
			      'field' => 'id',
			      'terms' => $term['term_id'], // Where term_id of Term 1 is "1".
			      'include_children' => false
			    )
			  ),
			'post_type' => 'product'
		);
		
		$posts_array = get_posts($args);
		if(count($posts_array) > 0) {
			foreach($posts_array as $post) {
				if($post->post_title == $_REQUEST['ecode']) {
					$payload['ecode'] = $post->post_title;
					$payload['name'] = get_post_meta($post->ID, "exam_full_name", true);
					$payload['hot'] =  get_post_meta($post->ID, "is_popular", true);
					$payload['questions'] = get_post_meta($post->ID, "questions_number", true);
					$payload['price'] = get_post_meta($post->ID, "_regular_price", true);
					
					//get files 
					
					$demo_id = get_post_meta($post->ID, "demo_file", true);
					if($demo_id) {
						$demo_post = get_post($demo_id);
						if($demo_post) {
							$payload['demo_file'] = $demo_post->guid;
						}
					}
					
					$exam_id = get_post_meta($post->ID, "exam_file", true);
					if($exam_id) {
						$exam_post = get_post($exam_id);
						if($exam_post) {
							$payload['main_file'] = $exam_post->guid;
						}
					}
					
					out(array("payload" => $payload));
				}
			}
			
		}
		
		out(array("error" => "Exam not found on assumed site"));
		
	} else {
		out(array("error" => "Exam not found on assumed site"));
	}
}
if($_REQUEST['type'] == "exams_search") {
	
	function custom_where( $where = '' ) {
	    global $wpdb;
	 
	    $where .= $wpdb->prepare( " AND post_title LIKE '%s'", $_REQUEST['search'] );
	 
	    return $where;
	}
	 
	add_filter( 'posts_where', 'custom_where' );
	
	$args = array(
		'posts_per_page'   => 500,
		'suppress_filters' => false,
		'post_type' => 'product'
	);
	
	$posts_array = get_posts($args);
	
	// Important to avoid modifying other queries
	remove_filter( 'posts_where', 'custom_where' );
	
	if(count($posts_array) > 0) {
		foreach($posts_array as $post) {
			$cert = wp_get_post_terms($post->ID, 'product_cat');
			if($cert[0]->parent == "0") {
				$vendor = $cert[0];
				$cert = $cert[1];
			} else {
				$cert = $cert[0];
				$vendor = get_term_by("id", $cert->parent, 'product_cat');
			}
			$cert = $cert->name;
			$vendor = $vendor->name;
			$out[] = array("id" => $post->ID, "name" => $post->post_title, "vendor" => $vendor, "certification" => $cert);
		}
		
		out(array("payload" => $out));
	}
	
}
if($_REQUEST['type'] == "add_exam") {
	$parent = get_term_by('name', $_REQUEST['vendor'], 'product_cat');
	// Checking if SubCategory Exist. 
	$term = term_exists($_REQUEST['certification'], 'product_cat', $parent->term_id);
	//if Exist Do the query
	if ($term !== 0 && $term !== null) {
		//vendor and certification exist
		$args = array(
			'posts_per_page'   => 500,
			'tax_query' => array(
			    array(
			      'taxonomy' => 'product_cat',
			      'field' => 'id',
			      'terms' => $term['term_id'], // Where term_id of Term 1 is "1".
			      'include_children' => false
			    )
			  ),
			'post_type' => 'product'
		);
		
		$posts_array = get_posts($args);
		
		if(count($posts_array) > 0) {
			$exam_id = false;
			foreach($posts_array as $post) {
				if($post->post_title == $_REQUEST['ecode']) {
					$exam_id = $post->ID;
				}
			}
			
			if($exam_id) {
				//update exam
				update_exam($exam_id, $parent->term_id, $term['term_id']);
			} else {
				//add exam
				add_exam($parent->term_id, $term['term_id']);
			}
		} else {
			add_exam($parent->term_id, $term['term_id']);
			
		}
	} else {
		out(array("error" => "vendor or certification doesnt exist"));
	}
}
if($_REQUEST['type'] == "add_vendor") {
	$parent = get_term_by('name', $_REQUEST['name'], 'product_cat');
	if ($term !== 0 && $term !== null) {
		//already exists	
		out(array("payload" => "OK"));
	} else {
		//create
		wp_insert_term($_REQUEST['name'], 'product_cat');
		out(array("payload" => "OK"));
	}
	exit;
}
if($_REQUEST['type'] == "add_certification") {
	$parent = get_term_by('name', $_REQUEST['vendor'], 'product_cat');
	if ($term === null) {
		wp_insert_term($_REQUEST['vendor'], 'product_cat');
		$parent = get_term_by('name', $_REQUEST['vendor'], 'product_cat');
	} 		
	$sub_cat = get_term_by('name', $_REQUEST['name'], 'product_cat');
	if($sub_cat === false) {
		wp_insert_term($_REQUEST['name'], 'product_cat', array("parent" => $parent->term_id));
	}
	out(array("payload" => "OK"));
	exit;
}
if($_REQUEST['type'] == "delete_exam") {
	$parent = get_term_by('name', $_REQUEST['vendor'], 'product_cat');
	// Checking if SubCategory Exist. 
	$term = term_exists($_REQUEST['certification'], 'product_cat', $parent->term_id);
	//if Exist Do the query
	if ($term !== 0 && $term !== null) {
		//vendor and certification exist
		$args = array(
			'posts_per_page'   => 500,
			'tax_query' => array(
			    array(
			      'taxonomy' => 'product_cat',
			      'field' => 'id',
			      'terms' => $term['term_id'], // Where term_id of Term 1 is "1".
			      'include_children' => false
			    )
			  ),
			'post_type' => 'product'
		);
		
		$posts_array = get_posts($args);
		
		if(count($posts_array) > 0) {
			$exam_id = false;
			foreach($posts_array as $post) {
				if($post->post_title == $_REQUEST['delete_exam']) {
					$exam_id = $post->ID;
					
					$demo_post_id = get_post_meta($exam_id, "demo_file", true);
					if(!empty($demo_post_id)) {
						wp_delete_post($demo_post_id);
					}
					
					$exam_post_id = get_post_meta($exam_id, "exam_file", true);
					if(!empty($exam_post_id)) {
						wp_delete_post($exam_post_id);
					}
					wp_delete_post($exam_id);
					
					out(array("payload" => "OK"));
				}
			}
			
			out(array("error" => "Exam not found"));
		} else {
			out(array("error" => "Exam not found"));
		}
	} else {
		out(array("error" => "vendor or certification doesnt exist"));
	}
}
function update_exam($exam_id, $vendor_id, $cert_id) {
	$args = array(
		"ID" => $exam_id,
		"post_title" => $_REQUEST['ecode'],
	);
	
	wp_update_post($args,$wp_error);
	
	$product_url = get_permalink( $exam_id );
	
	wp_set_object_terms( $post_id, (int)$vendor_id, 'product_cat' );
	wp_set_object_terms( $post_id, (int)$cert_id, 'product_cat', true);
	
	if(!empty($_REQUEST['demo'])) {
		$demo_post_id = get_post_meta($exam_id, "demo_file", true);
		if(!empty($demo_post_id)) {
			$demo_post = get_post($demo_post_id);
			if($demo_post) {
				$args = array(
					'post_mime_type' => 'application/pdf',
					'guid' => 'http://certshost.com/storage/'.$_REQUEST['namespace'].'/'.$_REQUEST['ecode'].'/'.$_REQUEST['demo']
				);
				
				global $wpdb;
				$updated = $wpdb->update($wpdb->posts,$args,array("ID" => $demo_post_id));
				
			} else {
				$args = array(
					"post_author" => "1",
					"post_title" => $_REQUEST['ecode']."demo",
					"post_status" => "inherit",
					'post_type' => 'attachment',
					'post_mime_type' => 'application/pdf',
					'guid' => 'http://certshost.com/storage/'.$_REQUEST['namespace'].'/'.$_REQUEST['ecode'].'/'.$_REQUEST['demo'],
					'post_parent' => '0',
					
				);
				
				$demo_post_id = wp_insert_post($args,$wp_error);
			}
		} else {
			$args = array(
				"post_author" => "1",
				"post_title" => $_REQUEST['ecode']."demo",
				"post_status" => "inherit",
				'post_type' => 'attachment',
				'post_mime_type' => 'application/pdf',
				'guid' => 'http://certshost.com/storage/'.$_REQUEST['namespace'].'/'.$_REQUEST['ecode'].'/'.$_REQUEST['demo'],
				'post_parent' => '0',
				
			);
			
			$demo_post_id = wp_insert_post($args,$wp_error);
		}
		
	}
	
	if(!empty($_REQUEST['main_file'])) {	
		$main_post_id = get_post_meta($post->ID, "exam_file", true);
		if($main_post_id) {
			$main_post = get_post($main_post_id);
			if($main_post) {
				$args = array(
					"ID" => $main_post_id,
					'post_mime_type' => 'application/pdf',
					'guid' => 'http://certshost.com/storage/'.$_REQUEST['namespace'].'/'.$_REQUEST['ecode'].'/'.$_REQUEST['main_file']
				);
				
				wp_update_post($args,$wp_error);
			} else {
				$args = array(
					"post_author" => "1",
					"post_title" => $_REQUEST['ecode'],
					"post_status" => "inherit",
					'post_type' => 'attachment',
					'post_mime_type' => 'application/pdf',
					'guid' => 'http://certshost.com/storage/'.$_REQUEST['namespace'].'/'.$_REQUEST['ecode'].'/'.$_REQUEST['main_file'],
					'post_parent' => '0',
					
				);
				
				$main_post_id = wp_insert_post($args,$wp_error);
			}
		} else {
			$args = array(
				"post_author" => "1",
				"post_title" => $_REQUEST['ecode'],
				"post_status" => "inherit",
				'post_type' => 'attachment',
				'post_mime_type' => 'application/pdf',
				'guid' => 'http://certshost.com/storage/'.$_REQUEST['namespace'].'/'.$_REQUEST['ecode'].'/'.$_REQUEST['main_file'],
				'post_parent' => '0',
				
			);
			
			$main_post_id = wp_insert_post($args,$wp_error);
		}
		
	}
	
	$meta = array(
		"_regular_price" => $_REQUEST['price'],
		"_featured" => ($_REQUEST['hot'] == 1 ? "yes" : "no"),
		"_price" => $_REQUEST['price'],
		"exam_full_name" => $_REQUEST['name'],
		"vendor_name" => $_REQUEST['vendor'],
		"questions_number" => $_REQUEST['questions'],
		"demo_file" => $demo_post_id,
		"exam_file" => $main_post_id,
		"is_popular" => ($_REQUEST['hot'] == 1 ? "yes" : "no"),
	);
	foreach($meta as $key => $val) {
		update_post_meta($exam_id, $key, $val);
	}
	
	out(array("payload" => "OK", "url" => $product_url));

}
function add_exam($vendor_id, $cert_id) {
	$args = array(
		"post_author" => "1",
		"post_title" => $_REQUEST['ecode'],
		"post_status" => "publish",
		'post_type' => 'product',
	);
	
	$post_id = wp_insert_post($args,$wp_error);
	
	$product_url = get_permalink( $post_id );
	
	wp_set_object_terms( $post_id, (int)$vendor_id, 'product_cat' );
	wp_set_object_terms( $post_id, (int)$cert_id, 'product_cat', true);
	
	if(!empty($_REQUEST['demo'])) {
		$args = array(
			"post_author" => "1",
			"post_title" => $_REQUEST['ecode']."demo",
			"post_status" => "inherit",
			'post_type' => 'attachment',
			'post_mime_type' => 'application/pdf',
			'guid' => 'http://certshost.com/storage/'.$_REQUEST['namespace'].'/'.$_REQUEST['ecode'].'/'.$_REQUEST['demo'],
			'post_parent' => '0',
			
		);
		
		$demo_post_id = wp_insert_post($args,$wp_error);
	}
	
	if(!empty($_REQUEST['main_file'])) {	
		$args = array(
			"post_author" => "1",
			"post_title" => $_REQUEST['ecode'],
			"post_status" => "inherit",
			'post_type' => 'attachment',
			'post_mime_type' => 'application/pdf',
			'guid' => 'http://certshost.com/storage/'.$_REQUEST['namespace'].'/'.$_REQUEST['ecode'].'/'.$_REQUEST['main_file'],
			'post_parent' => '0',
			
		);
		
		$main_post_id = wp_insert_post($args,$wp_error);
	}
	
	$meta = array(
		"_visibility" => "visible",
		"_stock_status" => "instock",
		"total_sales" => "0",
		"_downloadable" => "no",
		"_virtual" => "no",
		"_regular_price" => $_REQUEST['price'],
		"_sale_price" => "",
		"_purchase_note" => "",
		"_featured" => ($_REQUEST['hot'] == 1 ? "yes" : "no"),
		"_weight" => "",
		"_length" => "",
		"_width" => "",
		"_height" => "",
		"_sku" => "",
		"_product_attributes" => "a:0:{}",
		"_sale_price_dates_from" => "",
		"_sale_price_dates_to" => "",
		"_price" => $_REQUEST['price'],
		"_sold_individually" => "",
		"_manage_stock" => "no",
		"_backorders" => "no",
		"_stock" => "",
		"_upsell_ids" => "a:0:{}",
		"_crosssell_ids" => "a:0:{}",
		"_product_version" => "2.4.10",
		"_product_image_gallery" => "",
		"exam_full_name" => $_REQUEST['name'],
		"_exam_full_name" => "field_5309aeb9a6610",
		"vendor_name" => $_REQUEST['vendor'],
		"_vendor_name" => "field_5309bde5466b5",
		"questions_number" => $_REQUEST['questions'],
		"_questions_number" => "field_5309bfbdf4644",
		"demo_file" => $demo_post_id,
		"_demo_file" => "field_5309ba6f13aa7",
		"exam_file" => $main_post_id,
		"_exam_file" => "field_5309f9e1afbba",
		"is_popular" => ($_REQUEST['hot'] == 1 ? "yes" : "no"),
		"_is_popular" => "field_5371f3d40e738",
		"exam_file_flag" => "no",
		"demo_file_flag" => "no",
		"files_uploaded" => "yes"
	);
	foreach($meta as $key => $val) {
		add_post_meta($post_id, $key, $val, true);
	}
	
	out(array("payload" => "OK", "url" => $product_url));
}
function out($ar) {
	exit(json_encode($ar));
}
function pre($a) {
	echo "<pre>".print_r($a,1)."</pre>";
}
?>