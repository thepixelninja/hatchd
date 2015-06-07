<?php
//Template name: Portfolio
get_header();
//grab portfolio pages
$projects = $C->postTypes("portfolio");
shuffle($projects["posts"]);
//grab fliter list terms
$terms = $C->getTerms("type");
?>

<div class="row">
	
	<div class="col-md-3">
		
		<aside id="sideBar" class="sticky leftSidebar">
		
			<div class="pod navPod">
			
				<div class="podTitle">
					<h3>Filter by</h3>
				</div>
				
				<nav class="podContent sideNav">
					<ul class="list-unstyled">
						<li class="filter active" data-filter=".mix" data-event="true" data-type="Portfolio filter" data-description="All"><a>All</a></li>
						<?php foreach($terms as $term): ?>
						<li class="filter" data-filter=".<?php echo $term->slug; ?>" data-event="true" data-type="Portfolio filter" data-description="<?php echo $term->name; ?>"><a><?php echo $term->name; ?></a></li>
						<?php endforeach; ?>
					</ul>
				</nav>
			
			</div>
			
		</aside>
		
	</div>
	
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
					
			<div class="projectList" id="projects">
				
			<?php if(!empty($projects["posts"])): ?>
				
				<?php foreach($projects["posts"] as $post): ?>
				<?php $gallery = $C->pageGallery($post->ID); ?>
				<div class="mix portfolioItem <?php echo $C->pageTerms($post->ID,"type",true); ?>">
					<a href="<?php echo $post->guid; ?>" title="<?php echo $C->pageExcerpt($post->ID); ?>">
						<?php if(isset($gallery[0]["small"][0])): ?>
						<img src="<?php echo $gallery[0]["small"][0]; ?>" alt="<?php echo $gallery[0]["title"]; ?>"/>
						<?php else: ?>
						<?php echo $C->pageFeatureThumb("square_thumb",$post->ID); ?>
						<?php endif; ?>
					</a>
					<div class="hover">
						<a href="<?php echo $post->guid; ?>" title="<?php echo $C->pageExcerpt($post->ID); ?>">
							<h3>
								<span class="title"><?php echo $post->post_title; ?></span>
								<span class="glyphicon glyphicon-plus"></span>
							</h3>
						</a>
					</div>
					<?php /*<div class="info">
						<h3><a href="<?php echo $post->guid; ?>" title="<?php echo $C->pageExcerpt($post->ID); ?>"><?php echo $post->post_title; ?></a></h3>
						<?php if($C->isPageMeta("page_details_subtitle",$post->ID)): ?>
						<h4><?php echo $C->pageMeta("page_details_subtitle",$post->ID); ?></h4>
						<?php endif; ?>
						<p><?php echo $C->pageExcerpt($post->ID,20); ?></p>
						<a class="btn btn-fryed" href="<?php echo $post->guid; ?>" title="<?php echo $C->pageExcerpt($post->ID); ?>">Read more</a>
					</div>*/ ?>
				</div>
				<?php endforeach; ?>
			
			<?php else: ?>
				<p class="alert alert-warning">Sorry, No projects found.</p>
			<?php endif; ?>
		
			</div>
		
		</div>
		
	</div>
	
</div>

<?php get_footer(); ?>