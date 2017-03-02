	<div class="row">
		<div class="col-lg-12">
			<div class="alert bg-success" role="alert">
				<svg class="glyph stroked checkmark"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#stroked-checkmark"></use></svg>
				<?php if ($fn == 'recommend'): ?>
				Travel Order #<?php echo $travel_id; ?> has been successfully recommended. Thank you.
				<?php endif; ?>

				<?php if ($fn == 'approve'): ?>
				Travel Order #<?php echo $travel_id; ?> has been successfully approved. Thank you.
				<?php endif; ?>

				<a href="#" class="pull-right"><span class="glyphicon glyphicon-remove"></span></a>
			</div>
		</div><!-- /.col-->
	</div><!-- /.row -->	