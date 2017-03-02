	</div>	<!--/.main-->

	<script src="<?php echo base_url(); ?>assets/lumino/js/bootstrap.min.js"></script>
<!-- 	<script src="<?php echo base_url(); ?>assets/lumino/js/bootstrap-datepicker.js"></script> -->
	<script src="<?php echo base_url(); ?>assets/js/jquery-ui.js"></script>
	<script src="<?php echo base_url(); ?>assets/lumino/js/bootstrap-table.js"></script>
	<script>
		!function ($) {
		    $(document).on("click","ul.nav li.parent > a > span.icon", function(){          
		        $(this).find('em:first').toggleClass("glyphicon-minus");      
		    }); 
		    $(".sidebar span.icon").find('em:first').addClass("glyphicon-plus");
		}(window.jQuery);

		$(window).on('resize', function () {
		  if ($(window).width() > 768) $('#sidebar-collapse').collapse('show')
		})
		$(window).on('resize', function () {
		  if ($(window).width() <= 767) $('#sidebar-collapse').collapse('hide')
		})
		
	</script>	
</body>

</html>