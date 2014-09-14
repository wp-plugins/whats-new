<?php

	global $albdesign_whats_new;

	
		if(isset($albdesign_whatsnew_ouput_array['error'])){
		
			echo '<h3 class="albdesign_whatsnew_nothing_new_text">' . $nothing_new_posted . ' </h3>';
			
		} else {
		
			foreach($albdesign_whatsnew_ouput_array as $albdesign_whatsnew_ouput_array_key => $albdesign_whatsnew_ouput_array_value){
			
				if($albdesign_whatsnew_ouput_array_key != 'total_found' ){
				
		
					//if we have one or more post/page/product show the text header 
					if(count($albdesign_whatsnew_ouput_array[$albdesign_whatsnew_ouput_array_key]) >= 1 ){
			
						$text_header =''; 
			
						//Show corresponding header like "new posts,new pages,new products"
						if($albdesign_whatsnew_ouput_array_key=='posts'){
							$text_header =  $new_posts_text_header  ;
						}
						
						if($albdesign_whatsnew_ouput_array_key=='pages'){
							$text_header =  $new_pages_text_header  ;
						}
						
						if($albdesign_whatsnew_ouput_array_key=='products'){
							$text_header =  $new_products_text_header  ;
							
						}						
						
						echo '<h3 class="albdesign_whatsnew_text_header">' . $text_header  .'</h3>' ;
						
						foreach($albdesign_whatsnew_ouput_array[$albdesign_whatsnew_ouput_array_key] as $single_post_key => $single_post_attributes){
						
							//$single_post_attributes contains id,title,permalink,author,featured_image
						
							$posti = get_post($single_post_attributes['id']); 

							//print_r($posti);
							?>
							
							<div  class="albdesign_whatsnew_single_container" >
							
							
								<?php 

								if($show_featured_image=='yes'){ ?>
							
									<div class="albdesign_whatsnew_featured_image" >
									
										<!-- show featured image  -->
										<?php 
										
											if(isset($single_post_attributes['featured_image'])){
											
												echo $single_post_attributes['featured_image']; 
												
											}
											
										?>
										
									</div>
							
								<?php } ?>
							
								<div  class="albdesign_whatsnew_content"  >
								
									<!-- show the title -->
									
										<a href="<?php echo  get_permalink($posti->ID); ?>"  class="albdesign_whatsnew_title_link" > <?php echo  apply_filters('the_title',$posti->post_title); ?> </a>
																			
									<p>
										<?php
											if($show_date=='yes'){

													echo '<span class="albdesign_whats_new_post_date_span" >   Posted on ' .  $posti->post_modified . '</span>'; 

											}
											
											if($show_author=='yes'){
												
												if(isset($single_post_attributes['author'])){
												
													echo '<span  class="albdesign_whats_new_author_span" >  by ' .  $single_post_attributes['author'] . ' </span>'; 
													
												}											
											
											}
											
										?>										
									</p>
								

										<?php 
										
										if($show_excerpt=='yes'){
											echo $albdesign_whats_new->get_excerpt_by_id($posti->ID);
										}

										?>
									
								</div>

								<div class="albdesign_whatsnew_div_clear" ></div>
							
							</div>
						
							
							
						<?php 	
							
						}
				
					}
				
				}
				
			}
			
		}	//end ELSE
