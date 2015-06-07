<?php
//Template name: Contact page
get_header();
?>

<div class="row">
			
	<div class="col-md-9">
		
		<div class="content">
			
			<div class="titleArea">
				<h1><?php echo $C->pageTitle(); ?></h1>
				<?php if($C->isPageMeta("page_details_subtitle")): ?>
				<h2><?php echo $C->pageMeta("page_details_subtitle"); ?></h2>	
				<?php endif; ?>
			</div>
			<?php echo $C->pageContent(); ?>
			
			<?php $C->pageForm(1,"mainContactForm"); ?>
		
		</div>
							
	</div>
	
	<div class="col-md-3">
		
		<?php include("includes/sidebar.php"); ?>
		
	</div>

</div>

<?php get_footer(); ?>