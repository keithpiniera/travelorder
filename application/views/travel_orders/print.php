
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Travel Order</title>

    <style type="text/css">
    .box {
    	border: 1px solid;
    }
    .underline {
    	border-bottom: 1px solid;
    	padding: 2px 0;    	
    }
    .fixed_height {
    	height: 11px;
    }
    </style>
</head>
<body style="font-family: 'Arial Narrow', Arial, sans-serif; font-size: 11px;">
	<div style="" height="100%">
		<div>
			<div style="display: inline-block; width: 80px;">
				<img src="<?php echo base_url(); ?>assets/images/philrice.png">
			</div>
			<div style="display: inline-block; width: 456px;">
				<div class="underline" style="font-size: 16px; width: 350px; margin-top: 42px; margin-left: 35px; height: 18px;">PHILIPPINE RICE RESEARCH INSTITUTE</div>
				<div>Central Experiment Station</div>
			</div>
			<div style="display: inline-block; width: 150px;">
				<!-- <img src="<?php echo base_url(); ?>assets/images/ci.png"> -->
			</div>
		</div>

		<br>
    	<div style="text-align: center;">TRAVEL ORDER</div>
    	<div>
    		<div style="display: inline-block; width: 533px;"></div>
    		<div style="display: inline-block; width: 162px; text-align: center;">
    			<div style="border-bottom: 1px solid;">
	    			<?php echo $details['date_prepared']; ?>
	    		</div>
	    		<div>
	    			Date Prepared
	    		</div>
    		</div>
    	</div>

    	<br>
    	<div>
    		<div style="width: 336px; display: inline-block; text-align: center; font-weight: bold;">Employee/s in this Travel:</div>
    		<div style="margin-left: 15px; width: 162px; display: inline-block; text-align: center; font-weight: bold;">Position:</div>
    		<div style="margin-left: 15px; width: 162px; display: inline-block; text-align: center; font-weight: bold;">Mobile Number:</div>
    	</div>

        <?php $emp = array(1,2,3,4,5); ?>
        <?php $project_code = array(); ?>
        <?php for($i = 0; $i < count($employees); $i++): ?>
            <?php $emp = $employees[$i]; ?>
            <?php $project_code[] = $emp['project_code']; ?>
    	<div>
    		<div style="width: 10px; display: inline-block; text-align: center;">
    			<?php echo $i + 1; ?>
    		</div>
    		<div class="fixed_height underline" style="width: 326px; display: inline-block;">
    			<?php echo $emp['name']; ?>
    		</div>
    		<div class="fixed_height underline" style="margin-left: 15px; width: 162px; display: inline-block;">
    			<?php echo $emp['position']; ?>
    		</div>
    		<div class="fixed_height underline" style="margin-left: 15px; width: 162px; display: inline-block; text-align: center;">
    			<?php echo $emp['mobile_number']; ?>
    		</div>
    	</div>
    	<?php endfor; ?>

    	<br>
    	<div>Details:</div>
    	<div class="box" style="padding-left: 2px;">
    		<div class="underline">
    			<div style="display: inline-block; width: 140px;">
    				Project Code (Pls. Specify):
    			</div>
    			<div style="display: inline-block; width: 553px;">
    				<?php echo implode(', ', array_unique($project_code)); ?>
    			</div>
    		</div>
    		<div class="underline">
    			<div style="display: inline-block; width: 140px;">
    				Purpose/s:
    			</div>
    			<div style="display: inline-block; width: 553px;">
    				<?php echo $details['purpose']; ?>
    			</div>
    		</div>
    		<div class="underline">
    			<div style="display: inline-block; width: 140px;">
    				Destination/s:
    			</div>
    			<div style="display: inline-block; width: 553px;">
    				<?php echo $destination_string; ?>
    			</div>
    		</div>
    		<div class="underline">
    			<div style="display: inline-block; width: 200px;">
    				Inclusive Date/s of Travel:
    			</div>
    			<div style="display: inline-block; width: 162px; text-align: right;">
    				<?php echo $details['date_from']; ?>
    			</div>
    			<div style="display: inline-block; width: 155px; text-align: center;">
    				to
    			</div>
    			<div style="display: inline-block; width: 162px; text-align: right;">
    				<?php echo $details['date_to']; ?>
    			</div>
    		</div>
    		<div class="underline"></div>
    		<div class="underline">
    			<div style="display: inline-block; width: 140px;">
    				Mode of Transportation:
    			</div>
    			<div style="display: inline-block; width: 553px;">
    				<?php echo $details['mode_of_transportation']; ?>
    			</div>
    		</div>
    		<div class="underline">
    			<div style="display: inline-block; width: 360px;">
    				Vehicle Prioritization (Choose from Mandatory, Necessary, Last Priority):
    			</div>
    			<div style="display: inline-block; width: 333px;">
    				<?php echo $details['vehicle_prioritization']; ?>
    			</div>
    		</div>
    		<div class="underline">
    			<div style="display: inline-block; width: 360px;">
    				Others (Pls Specify; e.g. estimate time of meeting, baggage, luggage):
    			</div>
    			<div style="display: inline-block; width: 333px;">
    				<?php echo $details['others']; ?>
    			</div>
    		</div>
    		<div>
    			<div style="display: inline-block; width: 140px;">
    				Time of Deparature:
    			</div>
    			<div style="display: inline-block; width: 553px;">
    				<?php echo $details['time_of_departure']; ?>
    			</div>
    		</div>
    	</div>

    	<br>
    	<div>
    		<div style="display: inline-block; width: 150px;">
                <?php if ( !empty($details['recommending_id']) ): ?>
    			<div>
    				Recommended By:
    			</div>

    			<br>
    			<br>
    			<div class="underline" style="font-weight: bold;">
                    <?php echo $details['recommending_name']; ?>         
                </div>
    			<div> <?php echo $details['recommending_designation'] .', '. $details['recommending_division']; ?> </div>
                <?php endif; ?>
    		</div>
    		<div style="display: inline-block; width: 170px;"></div>
    		<div style="display: inline-block; width: 150px;">
    			<div>
    				Approved By:
    			</div>

    			<br>
    			<br>
    			<div class="underline" style="font-weight: bold;">
                    <?php echo $details['approving_name']; ?>           
                </div>
    			<div> <?php echo $details['approving_designation'] .', '. $details['approving_division']; ?> </div>
    		</div>
    		<div style="display: inline-block; width: 170px;"></div>
    	</div>

    	<br>
    	<br>
    	<br>
    	<div class="underline"></div>
    	<div style="text-align: center; font-weight: bold; padding: 5px 0;">Certificate of Appearance</div>
    	<div>
    		<div style="width:520px; display: inline-block;">
    			This is to certify that the above-mentioned employee/s has personaly appeared at:
    		</div>
    		<div style="width: 170px; display: inline-block; text-align: center; font-weight: bold;">
    			Appendix "C"
    		</div>
    	</div>

    	<br>
    	<div>
    		<div style="width: 165px; display: inline-block; text-align: center; margin-right: 8px;">Destination</div>
    		<div style="width: 165px; display: inline-block; text-align: center; margin-right: 8px;">Date</div>
    		<div style="width: 165px; display: inline-block; text-align: center; margin-right: 8px;">Name in Print</div>
    		<div style="width: 165px; display: inline-block; text-align: center;">Signature</div>
    	</div>
    	<?php for($i = 1; $i <= 5; $i++): ?>
    	<div>
    		<div style="width: 10px; display: inline-block; text-align: center;">
    			<?php echo $i; ?>
    		</div>
    		<div class="fixed_height underline" style="width: 153px; display: inline-block; text-align: center; margin-right: 8">
    		</div>
    		<div class="fixed_height underline" style="width: 165px; display: inline-block; text-align: center; margin-right: 8">
    			
    		</div>
    		<div class="fixed_height underline" style="width: 165px; display: inline-block; text-align: center; margin-right: 8">
    			
    		</div>
    		<div class="fixed_height underline" style="width: 165px; display: inline-block; text-align: center;">
    			
    		</div>
    	</div>
    	<?php endfor; ?>
    	<div style="text-align: right; font-size: 8px; font-style: italic; padding-right: 25px; margin-top: 5px;">PPD Travel Order Rev. 01 Effectivity Date: Feb 17, 2014</div>
    </div>
</body>
</html>