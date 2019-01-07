<?php 
foreach($a_kelas as $k){
	$a_data = $sevimapay[$k];
	$desc = $a_desc[$k];
	$universe = $a_universe[$k];
	$rule = $a_rule[$k];
	$flrg = $a_flrg[$k];
	$fuzzyoutput = $a_fuzzyoutput[$k];
	$sfuzzyoutput = $a_sfuzzyoutput[$k];
	$forecastedvar = $a_forecastedvar[$k];
	$output = $a_output[$k];
	$forecast = $a_forecast[$k];
	?>
<div class="row">

	<div class="col-md-6">
		<div class="table responsive">

			<table class="table">
				<tr>
					<td colspan="6" align="center"><?php echo $k?></td>
				</tr>
				<tr>
					<td>Min</td>
					<td><?php echo $desc['min']?></td>
					<td>Max</td>
					<td><?php echo $desc['max']?></td>
					<td>Mean</td>
					<td><?php echo $desc['mean']?></td>
				</tr>
				<tr>
					<td>Umin</td>
					<td><?php echo $desc['vmin']?></td>
					<td>Umax</td>
					<td><?php echo $desc['vmax']?></td>
					<td>Panjang Interval</td>
					<td><?php echo $desc['length']?></td>
				</tr>
				<tr>
					<td>Min Diff</td>
					<td><?php echo $desc['mindiff']?></td>
					<td>Max Diff</td>
					<td><?php echo $desc['maxdiff']?></td>
					<td>Mean 1st Diff</td>
					<td><?php echo $desc['meandiff']?></td>
				</tr>
			</table>
			<table class="table table-bordered table-stripped">
				<thead>
					<tr>
						<th>Periode</th>
						<th>Actual </th>
						<th>Variation</th>
						<th>Fuzzified <br>Variation</th>
						<th>Forecasted <br> Variation</th>
						<th>Fuzzy Output</th>
						<th>Forecasted <br> Enrollment</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach($a_data as $t => $row){ ?>
						<tr>
							<td><?php echo $row['periode']?></td>
							<td><?php echo $row['jumlah']?></td>
							<td><?php echo $row['diff']?></td>
							<td><?php echo $row['fs']?></td>
							<td><?php echo $forecastedvar[$row['periode']]?></td>
							<td><?php echo (!empty($output[$row['periode']]))?implode(' ',$output[$row['periode']]):''?></td>
							<td><?php echo $forecast[$row['periode']]?></td>
						</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="col-md-6">
		<div class="row">
			<div class="col-md-12">
				<div class="table responsive">
					
					<table class="table table-bordered">
						<tr>
							<th colspan="8" style="text-align: center">FUZZY SET <?php echo $k?></th>
						</tr>
						<tr>
							<th>Ui</th>
							<th>min</th>
							<th>midpoint</th>
							<th>max</th>
							
						</tr>
						<?php 
							$x = count($universe);
							for($p=1;$p<=$x;$p++){
						?>
						<tr>
							<td><?php echo 'U'.$p?></td>
							<td><?php echo $universe[$p]['min']?></td>
							<td><?php echo $universe[$p]['midpoint']?></td>
							<td><?php echo $universe[$p]['max']?></td>
							
						</tr>
						<?php } ?>
					</table>
					<?php $n = count($rule)?>
					<table class="table table-bordered">
						<tr>
							<th colspan="<?php echo $n+1?>" style="text-align: center">FUZZY RULE</th>
						</tr>
						<?php foreach($rule as $k => $a){?>
						<tr>
							<td><?php echo $k?></td>
							<?php for($i=1;$i<=$n;$i++)
									echo '<td>'.$a[$i].'</td>';
							?>
						</tr>
						<?php } ?>
					</table>
					<table class="table table-bordered">
						<tr>
							<th colspan="4" style="text-align: center">FUZZY LOGIC RELATIONSHIP GROUP</th>
						</tr>
						<tr>
							<th>Ai</th>
							<th>GROUP</th>
							<th>FUZZY OUTPUT</th>
						</tr>
						<?php foreach($flrg as $k => $row){
								$output = null;
								if(!(empty($k) or empty($row)))
									$output = implode(str_repeat('&nbsp;', 10), $fuzzyoutput[$k]);
							?>
						<tr>
							<td><?php echo $k?></td>
							<td><?php echo $row?></td>
							<td><?php echo $output?></td>
						</tr>
						<?php } ?>
					</table>
					<table class="table table-bordered">
						<tr>
							<th colspan="8" style="text-align: center">STANDARDIZE FUZZY OUTPUT</th>
						</tr>
						<?php foreach($flrg as $k => $row){
								if(!(empty($k) or empty($row))){
							?>
									<tr>
										<td><?php echo $k?></td>
										<?php foreach($sfuzzyoutput[$k] as $row){
											echo '<td>'.$row.'</td>';
										} ?>
									</tr>
						<?php }
							} ?>
					</table>
				</div>
			</div>
		</div>
		</div>
</div>
<?php } ?>
