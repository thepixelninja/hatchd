<?php
//Template name: Contact page
get_header();
?>

<div class="row">
			
	<div class="col-md-9">
		
		<div class="content">
			
			<?php if($C->isPageFeatureImage()): ?>
			<div class="featureImage">
				<?php echo $C->pageFeatureImage(); ?>
			</div>
			<?php endif; ?>
			
			<?php if($C->isPageMeta("page_details_subtitle")): ?>
			<h2 class="pageTitle"><?php echo $C->pageMeta("page_details_subtitle"); ?></h2>
			<?php else: ?>
			<h2 class="pageTitle"><?php echo $C->pageTitle(); ?></h2>
			<?php endif; ?>
			
			<?php echo $C->pageContent(); ?>
			
			<?php $C->pageForm(1,"mainContactForm"); ?>
		
		</div>
							
	</div>
	
	<div class="col-md-3">
		
		<?php include("includes/sidebar.php"); ?>
		
	</div>

</div>

<?php get_footer(); ?>