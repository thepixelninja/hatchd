<?php 
get_header(); 
$labs = $C->pageLabs();
?>

<div class="row">
			
	<div class="col-md-9">
		
		<article class="content">
		
			<div class="date starburst"><div><?php echo $C->postDate($post->ID); ?></div></div>
			
			<?php if($C->isPageFeatureImage()): ?>
			<div class="featureImage">
				<?php echo $C->pageFeatureImage(); ?>
			</div>
			<?php endif; ?>
			
			<?php if($C->isPageMeta("page_details_subtitle")): ?>
			<h2 class="pageTitle"><span><?php echo $C->pageMeta("page_details_subtitle"); ?></span></h2>
			<?php else: ?>
			<h2 class="pageTitle"><span><?php echo $C->pageTitle(); ?></span></h2>
			<?php endif; ?>
			
			<?php include("includes/social-buttons.php"); ?>
			
			<?php echo $C->pageContent(); ?>
			
			<?php if(!empty($labs)): ?>
			<div id="labs">
			
				<?php //$C->debug($labs); ?>
					
				<?php foreach($labs as $key => $lab): ?>
				<div class="labItem" id="lab-<?php echo $key; ?>">
					
					<h3><?php echo $lab["lab_title"]; ?></h3>
					
					<?php if($lab["lab_description"] != ""): ?>
					<div class="editableContent">
						<?php echo $lab["lab_description"]; ?>
					</div>
					<?php endif; ?>
					
					<div class="tabs">
						
						<ul class="nav nav-tabs">
							<?php if($lab["index"] != ""): ?>
							<li class="active"><a href="#demo-<?php echo $key; ?>" data-toggle="tab">Demo</a></li>
							<?php endif; ?>
							<?php if($lab["html"] != ""): ?>
							<li><a href="#html-<?php echo $key; ?>" data-toggle="tab">HTML</a></li>
							<?php endif; ?>
							<?php if($lab["css"] != ""): ?>
							<li><a href="#css-<?php echo $key; ?>" data-toggle="tab">CSS</a></li>
							<?php endif; ?>
							<?php if($lab["js"] != ""): ?>
							<li><a href="#js-<?php echo $key; ?>" data-toggle="tab">JS</a></li>
							<?php endif; ?>
						</ul>
						
						<div class="tab-content">
							
							<a class="fullScreen" data-event="true" data-type="Fullscreen lab launched" data-description="<?php echo $lab["lab_title"]; ?>">
								<span class="glyphicon glyphicon-fullscreen"></span>
							</a>
							
							<?php if($lab["index"] != ""): ?>
							<div class="tab-pane active" id="demo-<?php echo $key; ?>">
								<iframe src="<?php echo $lab["index"]; ?>" seamless="seamless"></iframe>
							</div>
							<?php endif; ?>
							
							<?php if($lab["html"] != ""): ?>
							<div class="tab-pane" id="html-<?php echo $key; ?>">
								<?php echo $lab["html"]; ?>
							</div>
							<?php endif; ?>
							
							<?php if($lab["css"] != ""): ?>
							<div class="tab-pane" id="css-<?php echo $key; ?>">
								<?php echo $lab["css"]; ?>
							</div>
							<?php endif; ?>
							
							<?php if($lab["js"] != ""): ?>
							<div class="tab-pane" id="js-<?php echo $key; ?>">
								<?php echo $lab["js"]; ?>
							</div>
							<?php endif; ?>
							
						</div>
						
					</div>
					
					<a class="btn" data-goal="true" data-event="true" data-type="Lab Download" data-description="<?php echo $lab["lab_title"]; ?>" href="<?php echo $lab["lab_files"]; ?>">Download files</a>
					<?php if($lab["index"] != ""): ?>
					<a class="btn" target="_blank" data-event="true" data-type="Lab New Window" data-description="<?php echo $lab["lab_title"]; ?>" href="<?php echo $lab["index"]; ?>">Open in a new window</a>
					<?php endif; ?>
					
				</div>
				<?php endforeach; ?>
				
			</div>
			<?php endif; ?>
			
			<?php if($post->post_type == "post" || $post->post_type == "labs"): ?>
			<div id="comments"></div>
			<?php endif; ?>
		
		</article>
							
	</div>
	
	<div class="col-md-3">
		
		<?php include("includes/sidebar.php"); ?>
		
	</div>

</div>

<?php get_footer(); ?>