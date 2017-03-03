	<div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar">
		<form role="search">
			<div class="form-group">
				<input type="text" class="form-control" placeholder="Search">
			</div>
		</form>
		<ul class="nav menu">
			<!-- <li class="">
				<a href="<?php echo base_url(); ?>index.php/travel_orders/index">
					<svg class="glyph stroked home"><use xlink:href="#stroked-home"/></svg>
					My Travels
				</a>
			</li> -->
			<?php if ($this->session->userdata('type') != 'admin'): ?>
			<li>
				<a href="<?php echo base_url(); ?>index.php/travel_orders/create">
					<svg class="glyph stroked plus sign"><use xlink:href="#stroked-plus-sign"/></svg>
					Create Travel Order
				</a>
			</li>
			<?php endif; ?>
			<li class="parent ">
				<a href="#">
					<span data-toggle="collapse" href="#sub-item-2"><svg class="glyph stroked chevron-down"><use xlink:href="#stroked-chevron-down"></use></svg></span>
					Travel Orders 
				</a>
				<ul class="children " id="sub-item-2">
					<li>
						<a class="" href="<?php echo base_url(); ?>index.php/travel_orders/status/all">
							<svg class="glyph stroked chevron-right"><use xlink:href="#stroked-chevron-right"></use></svg>
							All
						</a>
					</li>
					<li>
						<a class="" href="<?php echo base_url(); ?>index.php/travel_orders/status/pending">
							<svg class="glyph stroked chevron-right"><use xlink:href="#stroked-chevron-right"></use></svg>
							Pending
						</a>
					</li>
					<li>
						<a class="" href="<?php echo base_url(); ?>index.php/travel_orders/status/canceled">
							<svg class="glyph stroked chevron-right"><use xlink:href="#stroked-chevron-right"></use></svg>
							Canceled
						</a>
					</li>
					<li>
						<a class="" href="<?php echo base_url(); ?>index.php/travel_orders/status/declined">
							<svg class="glyph stroked chevron-right"><use xlink:href="#stroked-chevron-right"></use></svg>
							Declined
						</a>
					</li>
					<li>
						<a class="" href="<?php echo base_url(); ?>index.php/travel_orders/status/approved">
							<svg class="glyph stroked chevron-right"><use xlink:href="#stroked-chevron-right"></use></svg>
							Approved
						</a>
					</li>
					<li>
						<a class="" href="<?php echo base_url(); ?>index.php/travel_orders/status/for_recommendation">
							<svg class="glyph stroked chevron-right"><use xlink:href="#stroked-chevron-right"></use></svg>
							For Recommendation
						</a>
					</li>
					<li>
						<a class="" href="<?php echo base_url(); ?>index.php/travel_orders/status/for_approval">
							<svg class="glyph stroked chevron-right"><use xlink:href="#stroked-chevron-right"></use></svg>
							For Approval
						</a>
					</li>
				</ul>
			</li>
			<?php if ($this->session->userdata('type') == 'admin'): ?>
			<li class="parent ">
				<a href="#">
					<span data-toggle="collapse" href="#sub-item-1"><svg class="glyph stroked chevron-down"><use xlink:href="#stroked-chevron-down"></use></svg></span>
					Destinations
				</a>
				<ul class="children collapse" id="sub-item-1">
					<li>
						<a class="" href="<?php echo base_url(); ?>index.php/travel_orders/offices">
							<svg class="glyph stroked chevron-right"><use xlink:href="#stroked-chevron-right"></use></svg>
							Offices/Agency/Building
						</a>
					</li>
				</ul>
			</li>
			<?php endif; ?>
		</ul>
		<div class="attribution">Template by <a href="http://www.medialoot.com/item/lumino-admin-bootstrap-template/">Medialoot</a><br/><a href="http://www.glyphs.co" style="color: #333;">Icons by Glyphs</a></div>
	</div><!--/.sidebar-->
	<script type="text/javascript">
		$('.nav.menu').find('a[href="<?php echo $page; ?>"]').parent().addClass('active');
		$('form[role="search"]').submit(function(e){
			e.preventDefault();
			return false;
		});
		$('form[role="search"]').on('keyup', 'input[type="text"]', function(e){
			e.preventDefault();
			var k = e.which;
			if (k != 13) return;
			if ($(this).val() == '') return;
			window.location.href = '<?php echo base_url(); ?>index.php/travel_orders/search/'+ encodeURIComponent($(this).val());
		});
	</script>