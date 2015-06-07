<?php global $C; ?>

			</div>

		</div>

	</div>

	<footer>

		<div class="container">

			<div class="row">

				<div class="col-md-6">
					<span class="copyright">&copy; <?php echo date("Y"); ?> Hatchd</span>
				</div>

				<div class="col-md-6">
					<?php echo $C->siteMenu(1,"list-unstyled list-inline"); ?>
				</div>

			</div>

		</div>

	</footer>

</div>

<div id="backToTop">
	<i class="glyphicon glyphicon-circle-arrow-up"></i>
</div>

<div id="layoutDetector" class="hidden-xs hidden-sm"></div>

<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Lobster|Source+Sans+Pro:400,300,600,300italic,400italic,600italic" type="text/css"/>
<link rel="stylesheet" href="<?php echo $C->themePath(); ?>/functions/min/?g=css" type="text/css"/>

<script type="text/javascript">
	window.themePath = "<?php echo $C->themePath(); ?>";
</script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $C->themePath(); ?>/functions/min/?g=js"></script>

<!--[if lt IE 9]>
<script type="text/javascript" src="<?php echo $C->themePath(); ?>/js/respond.min.js"></script>
<script type="text/javascript" src="<?php echo $C->themePath(); ?>/js/selectivizr.min.js"></script>
<![endif]-->

<?php wp_footer(); ?>

<!--
**************************
* SITE BUILT BY ED FRYER *
* ED@THEPIXEL.NINJA      *
**************************
-->

</body>

</html>
