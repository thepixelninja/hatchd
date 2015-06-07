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
			<h2 class="pageTitle"><?php echo $pagename; ?></h2>
			<?php if($cat->description !== ""): ?>
			<div class="editableContent">
				<p><?php echo $cat->description; ?></p>
			</div>
			<?php endif; ?>
			<?php else: ?>
			<?php if($C->isPageMeta("page_details_subtitle")): ?>
			<h2 class="pageTitle"><?php echo $C->pageMeta("page_details_subtitle"); ?></h2>
			<?php else: ?>
			<h2 class="pageTitle"><?php echo $C->pageTitle(); ?></h2>
			<?php endif; ?>
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