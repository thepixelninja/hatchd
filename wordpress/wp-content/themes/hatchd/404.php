<?php get_header(); ?>

<div class="row">
			
	<div class="col-md-9">
		
		<div class="content">
		
			<?php if($C->isPageFeatureImage()): ?>
			<div class="featureImage">
				<?php echo $C->pageFeatureImage(); ?>
			</div>
			<?php endif; ?>
			
			
			<h2 class="pageTitle">Oooops, There appears to have been a boo boo!</h2>
			
			<div class="editableContent">
				<h6>404 Error :: Page not found.</h6>
				<p>Sorry the page you were looking for seems to have done a runner. Please select another page from the navigation.</p>
				<p><a class="btn" href="<?php echo $C->sitePath(); ?>">Back to home page</a></p>
			</div>
			
		</div>
							
	</div>
	
	<div class="col-md-3">
		
		<?php include("includes/sidebar.php"); ?>
		
	</div>

</div>

<?php get_footer(); ?>