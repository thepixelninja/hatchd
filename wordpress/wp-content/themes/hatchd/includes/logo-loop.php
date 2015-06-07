<?php $logos = $C->pageLogos(); ?>

<?php if(!empty($logos)): ?>
<div id="logos">
		
	<?php foreach($logos as $logo): ?>
	<div class="item">
		<a <?php if($logo["link"] != ""): ?>href="<?php echo $logo["link"]; ?>"<?php endif; ?> title="<?php echo $logo["title"]; ?>">
			<?php echo $logo["picturefill"]; ?>
		</a>
	</div>
	<?php endforeach; ?>
		
</div>
<?php endif; ?>