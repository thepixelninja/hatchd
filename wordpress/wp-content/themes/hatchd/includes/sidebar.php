<?php 
//grab parent id
global $post;
$parent = $post->post_parent;
//grab page children
$children = $C->pageChildren(); 
//grab related
$related = $C->pageRelated(false,0,5);
//grab siblings
$siblings = $C->pageSiblings();
//grab other posts types
$types = $C->postTypes($pagename,0,5);
//greb sibling types
$siblingTypes = $C->postTypes($post->post_type,0,5);
//grab the downloads
$downloads = $C->pageDownloads();
//reset all post data
wp_reset_postdata();
?>

<aside id="sideBar">
	
	<?php if($C->pageTemplateIs("list-posts") || $C->isCategory() || $C->getCategory()): ?>
	<div class="pod navPod">
		
		<div class="podTitle">
			<h3>Categories</h3>
		</div>
		
		<nav class="podContent sideNav">
			<?php echo $C->siteCategories(); ?>
		</nav>
	
	</div>
	<?php endif; ?>
	
	<?php if(!empty($terms)): ?>
		
	<div class="pod navPod">
		
		<div class="podTitle">
			<h3><?php echo $podTitle; ?></h3>
		</div>
		
		<nav class="podContent sideNav">
			<ul class="list-unstyled">
				<?php foreach($terms as $t): $t["posts"][0]; $term = $t["term"]; ?>
				<?php $active = ""; if($term->slug == $curTerm){ $active = "active"; } ?>
				<li class="<?php echo $active; ?>"><a href="<?php echo $C->sitePath()."/".$tax."/".$term->slug; ?>" title="<?php echo $C->pageExcerpt($post->ID,20); ?>"><?php echo $term->name; ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>
		
	</div>
		
	<?php else: ?>

		<?php if(!empty($types["posts"])): ?>
			
		<div class="pod navPod">
			
			<div class="podTitle">
				<?php if($C->pageTitle() != ""): ?>
				<h3><?php echo $C->pageTitle(); ?></h3>
				<?php else: ?>
				<h3>Select Page</h3>	
				<?php endif; ?>
			</div>
			
			<nav class="podContent sideNav">
				<ul class="list-unstyled">
					<?php foreach($types["posts"] as $type): ?>
					<li><a href="<?php echo $type->guid; ?>" title="<?php echo $C->pageExcerpt($type->ID,20); ?>"><?php echo $type->post_title; ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>
		
		</div>
		
		<?php elseif(!empty($siblingTypes["posts"]) && $post->post_type != "home" && $post->post_type != "page" && $post->post_type != "post"): ?>
			
		<div class="pod navPod">
			
			<div class="podTitle">
				<h3><?php echo str_replace(array("_","-")," ",ucfirst(strtolower($post->post_type))); ?></h3>	
			</div>
			
			<nav class="podContent sideNav">
				<ul class="list-unstyled">
					<?php foreach($siblingTypes["posts"] as $type): ?>
					<?php $active = ""; if($type->ID == get_the_ID()){ $active = "active"; } ?>
					<li class="<?php echo $active; ?>"><a href="<?php echo $type->guid; ?>" title="<?php echo $C->pageExcerpt($type->ID,20); ?>"><?php echo $type->post_title; ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>
		
		</div>
	
		<?php endif; ?>
	
	<?php endif; ?>
	
	<?php if(!empty($children)): ?>
		
	<div class="pod navPod">
		
		<div class="podTitle">
			<?php if($C->pageTitle() != ""): ?>
			<h3><?php echo $C->pageTitle(); ?></h3>
			<?php else: ?>
			<h3>Select Page</h3>	
			<?php endif; ?>
		</div>
		
		<nav class="podContent sideNav">
			<ul class="list-unstyled">
				<?php foreach($children as $child): ?>
				<li><a href="<?php echo $child->guid; ?>" title="<?php echo $C->pageExcerpt($child->ID,20); ?>"><?php echo $child->post_title; ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>
	
	</div>
	
	<?php elseif(!empty($siblings)): ?>
		
	<div class="pod navPod">
		
		<div class="podTitle">
			<?php if($C->pageTitle($parent) != ""): ?>
			<h3><?php echo $C->pageTitle($parent); ?></h3>
			<?php else: ?>
			<h3>Select Page</h3>	
			<?php endif; ?>
		</div>
		
		<nav class="podContent sideNav">
			<ul class="list-unstyled">
				<?php foreach($siblings as $sibling): ?>
				<?php $active = ""; if($sibling->ID == get_the_ID()){ $active = "active"; } ?>
				<li class="<?php echo $active; ?>"><a href="<?php echo $sibling->guid; ?>" title="<?php echo $C->pageExcerpt($sibling->ID,20); ?>"><?php echo $sibling->post_title; ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>
		
	</div>
	<?php endif; ?>
	
	<?php if(!empty($related)): ?>
	<div class="pod navPod">
		
		<div class="podTitle">
			<h3>Related Articles</h3>
		</div>
		
		<nav class="podContent sideNav">
			<ul class="list-unstyled">
				<?php foreach($related as $rel): ?>
				<?php $active = ""; if($rel->ID == get_the_ID()){ $active = "active"; } ?>
				<li class="<?php echo $active; ?>"><a href="<?php echo $rel->guid; ?>" title="<?php echo $rel->post_title; ?>"><?php echo $rel->post_title; ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>
	
	</div>
	<?php endif; ?>
	
	<?php if(!empty($downloads)): ?>
	<div class="pod navPod">
		
		<div class="podTitle">
			<h3>Downloads</h3>
		</div>
		
		<nav class="podContent">
			
			<?php foreach($downloads as $download): ?>
			<div class="<?php echo $download["ext"]; ?> podItem">
				<a href="<?php echo $download["file"]; ?>" data-event="true" data-goal="true" data-type="Download" data-description="<?php echo $download["title"]; ?>" title="<?php echo $download["title"]; ?>">
					<img class="fileIcon" src="<?php echo $download["icon"]; ?>" alt="<?php echo $download["ext"]; ?> icon"/>
					<?php echo $download["title"]; ?>
				</a>
			</div>
			<?php endforeach; ?>
			
		</nav>
	
	</div>
	<?php endif; ?>
	
	<?php include("stackoverflow-pod.php"); ?>
	
	<?php include("twitter-pod.php"); ?>
	
	<div class="hidden-xs hidden-sm">
		<?php include("sidebar-bottom.php"); ?>
	</div>
	
</aside>
