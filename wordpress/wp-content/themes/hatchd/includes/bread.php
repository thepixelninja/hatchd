<?php 
$trail = array_reverse($C->getPageTrail()); 
?>
<div class="row">
	<div class="col-md-12">
		<ul id="bread" class="list-inline list-unstyled">
			<li><a href="<?php echo $C->sitePath(); ?>">Home</a></li>
			<?php foreach($trail as $key => $t): ?>
			<li><span>/</span> <a <?php if($key+1 == count($trail)): ?>class="active"<?php endif; ?> href="<?php echo $t->guid; ?>"><?php echo $t->post_title; ?></a></li>	
			<?php endforeach; ?>
		</ul>
	</div>
</div>