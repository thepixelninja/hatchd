<?php
get_header();
//grab taxonomy details and posts
$taxonomy 	= get_query_var("taxonomy");
$term 		= get_query_var("term");
$title		= ucfirst(str_replace("-"," ",$term));
$posts 		= $C->termChildren();
?>
<div class="row">
	
	<div class="col-md-3">
		<?php include("includes/sidebar.php"); ?>
	</div>
	
	<div class="col-md-9">
		
		<div class="titleArea">
			<h1><?php echo $title; ?></h1>
		</div>
		
		<ul class="postList list-unstyled">
			
		<?php if(!empty($posts)): ?>
			
			<?php foreach($posts as $post): ?>
			<li>
				<div class="image">
					<a href="<?php echo $post->guid; ?>" title="<?php echo $C->pageExcerpt($post->ID); ?>"><?php echo $C->pageFeatureThumb($post->ID); ?></a>
				</div>
				<div class="text">
					<h2><a href="<?php echo $post->guid; ?>" title="<?php echo $C->pageExcerpt($post->ID); ?>"><?php echo $post->post_title; ?></a></h2>
					<p><?php echo $C->pageExcerpt($post->ID); ?> <a href="<?php echo $post->guid; ?>" title="<?php echo $C->pageExcerpt($post->ID); ?>">Read more</a></p>
				</div>
			</li>
			<?php endforeach; ?>
		
		<?php else: ?>
			<li>Sorry, No Sub-pages found.</li>
		<?php endif; ?>
	
		</ul>
		
	</div>
	
</div>

<div class="row visible-xs visible-sm sideBar">
	<div class="col-md-12">
	<?php include("includes/sidebar-bottom.php"); ?>
	</div>
</div>

<?php get_footer(); ?>