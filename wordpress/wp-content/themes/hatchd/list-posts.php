<?php
//Template name: List posts
get_header();
//grab posts
if($C->isCategory()){
	$cat = $C->getCategory();
	$pagename = $cat->slug;
}
$posts = $C->pagePosts($pagename,0,20);
?>

<div class="row">
			
	<div class="col-md-9">
		
		<div class="content">
		
			<?php if($C->isCategory()): ?>
				
				<div class="titleArea">
					<h1><?php echo $pagename; ?></h1>
				</div>
				<?php if($cat->description !== ""): ?>
				<div class="editableContent">
					<p><?php echo $cat->description; ?></p>
				</div>
				<?php endif; ?>
			
			<?php else: ?>
				
				<div class="titleArea">
					<h1><?php echo $C->pageTitle(); ?></h1>
					<?php if($C->isPageMeta("page_details_subtitle")): ?>
					<h2><?php echo $C->pageMeta("page_details_subtitle"); ?></h2>	
					<?php endif; ?>
				</div>
				<?php echo $C->pageContent(); ?>
				
			<?php endif; ?>
		
			<?php include("includes/post-loop.php"); ?>
		
		</div>
							
	</div>
	
	<div class="col-md-3">
		
		<?php include("includes/sidebar.php"); ?>
		
	</div>

</div>
		
<?php get_footer(); ?>