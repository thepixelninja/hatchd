<?php
//Template name: List terms
get_header();
//grab terms
$tax	= $C->singular($pagename);
$terms 	= $C->taxChildren($tax,0,1);
?>

<div class="row">
	
	<div class="col-md-3">
		<?php include("includes/sidebar.php"); ?>
	</div>
	
	<div class="col-md-9">
		
		<?php if($C->isPageFeatureImage()): ?>
		<div class="featureImage">
			<?php echo $C->pageFeatureImage(); ?>
		</div>
		<?php endif; ?>
	
		<h1><?php echo $C->pageTitle(); ?></h1>
		<?php if($C->isPageMeta("page_details_subtitle")): ?>
		<h2><?php echo $C->pageMeta("page_details_subtitle"); ?></h2>
		<?php endif; ?>
		
		<?php echo $C->pageContent(); ?>
		
		<?php if(!empty($terms)): ?>
			
		<div class="termList row">
			
			<?php foreach($terms as $t): $post = $t["posts"][0]; $term = $t["term"]; ?>
			<div class="col-md-3">
				<div class="term">
					<a href="<?php echo $C->sitePath()."/".$tax."/".$term->slug; ?>" title="<?php echo $C->pageExcerpt($post->ID); ?>"><?php echo $C->pageFeatureThumb("feature_thumb",$post->ID); ?></a>
					<h2><a href="<?php echo $C->sitePath()."/".$tax."/".$term->slug; ?>" title="<?php echo $C->pageExcerpt($post->ID); ?>"><?php echo $term->name; ?></a></h2>
				</div>
			</div>
			<?php endforeach; ?>
			
		</div>
		
		<?php endif; ?>
		
	</div>
	
</div>

<div class="row visible-xs visible-sm sideBar">
	<div class="col-md-12">
	<?php include("includes/sidebar-bottom.php"); ?>
	</div>
</div>

<?php get_footer(); ?>