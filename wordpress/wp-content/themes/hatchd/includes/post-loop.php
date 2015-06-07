<?php 
if(isset($posts["paging"])) $paging = $posts["paging"]; 
if(isset($posts["posts"])){ $posts = $posts["posts"]; }else{ $paging = false; }
?>

<ul class="postList list-unstyled">
			
	<?php if(!empty($posts)): ?>
	
	<?php foreach($posts as $key => $post): ?>
	<li class="<?php echo $C->pageColorScheme($post->ID); ?>">
		<div class="media">
			<?php if($C->pageFeatureThumb() != ""): ?>
			<a class="thumb" href="<?php echo $post->guid; ?>" title="<?php echo $C->pageExcerpt($post->ID); ?>"><?php echo $C->pageFeatureThumb("square_thumb",$post->ID); ?></a>
			<?php elseif($post->post_type == "labs"): ?>
			<?php $labs = $C->pageLabs($post->ID); ?>
			<?php if(!empty($labs) && isset($labs[1])): ?>
			<iframe class="thumb" src="<?php echo $labs[1]["index"]; ?>" scrolling="no" seamless="true"></iframe>
			<?php endif; ?>
			<?php endif; ?>
			<div class="media-body">
				<?php if($post->post_type == "post" || $post->post_type == "labs"): ?>
				<div class="date starburst"><div><?php echo $C->postDate($post->ID); ?></div></div>
				<?php endif; ?>
				<h3 class="media-heading"><a href="<?php echo $post->guid; ?>" title="<?php echo $C->pageExcerpt($post->ID); ?>"><?php echo $post->post_title; ?></a></h3>
				<p><?php echo $C->pageExcerpt($post->ID); ?></p>
				<a href="<?php echo $post->guid; ?>" class="btn pull-right" title="<?php echo $C->pageExcerpt($post->ID); ?>">Read more</a>
			</div>
		</div>
	</li>
	<?php endforeach; ?>

	<?php if($paging["prev"] || $paging["next"]): ?>
	<?php wp_reset_postdata(); ?>
	<li class="pagination">
		<div class="buttons">
			<?php if($paging["prev"]): ?>
			<a class="btn" href="<?php echo $paging["prev"]; ?>">&laquo; Prev</a>
			<?php endif; ?>
			<?php if($paging["next"]): ?>
			<a class="btn" href="<?php echo $paging["next"]; ?>">Next &raquo;</a>
			<?php endif; ?>
		</div>
	</li>
	<?php endif; ?>

	<?php else: ?>
	<li class="alert alert-warning">Sorry, No '<?php echo $C->pageTitle(); ?>' posts found.</li>
	<?php endif; ?>

</ul>