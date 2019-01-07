<div class="row">
	<div class="col-md-6">
		<div class="table responsive">
			<table class="table table-bordered table-stripped">
				<thead>
					<tr>
						<th>Tanggal</th>
						<th>Index Harga</th>
						<th>Forecast</th>
						<th>|Dt+1 - Dt|</th>
						<th>Fuzzy Set</th>
						<th>Fuzzy Logic <br> Relatiopnship</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach($scsi as $t => $row){ ?>
						<tr>
							<td><?php echo $row['tglstock']?></td>
							<td><?php echo $row['indexharga']?></td>
							<td><?php echo $forecast[$t]?></td>
							<td><?php echo $row['diff']?></td>
							<td><?php echo $row['fs']?></td>
							<td><?php echo $row['flr']['dari'].' --> '.$row['flr']['ke']?></td>
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
					<table class="table">
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
							<td><?php echo $desc['umin']?></td>
							<td>Umax</td>
							<td><?php echo $desc['umax']?></td>
							<td>Panjang Interval</td>
							<td><?php echo $desc['panjanginterval']?></td>
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
					<table class="table table-bordered">
						<tr>
							<th colspan="8" style="text-align: center">FUZZY SET</th>
						</tr>
						<tr>
							<th>Ui</th>
							<th>min</th>
							<th>midpoint</th>
							<th>max</th>
							<th>Ui</th>
							<th>min</th>
							<th>midpoint</th>
							<th>max</th>
						</tr>
						<?php //foreach($universe as $k => $row){?>
						<?php 
							$x = count($universe);
							$q=ceil($x/2);
							for($p=1;$p<=$x/2;$p++){
								$q++;
						?>
						<tr>
							<td><?php echo 'U'.$p?></td>
							<td><?php echo $universe[$p]['min']?></td>
							<td><?php echo $universe[$p]['midpoint']?></td>
							<td><?php echo $universe[$p]['max']?></td>
							<td><?php echo 'U'.$q?></td>
							<td><?php echo $universe[$q]['min']?></td>
							<td><?php echo $universe[$q]['midpoint']?></td>
							<td><?php echo $universe[$q]['max']?></td>
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
						</tr>
						<?php foreach($flrg as $k => $row){?>
						<tr>
							<td><?php echo $k?></td>
							<td><?php echo $row?></td>
						</tr>
						<?php } ?>
					</table>
					<?php $n = count($rule)?>
					<table class="table table-bordered">
						<tr>
							<th colspan="<?php echo $n+1?>" style="text-align: center">FUZZY RULE</th>
						</tr>
						<tr>
							<th>A(i,j)</th>
							<?php for($i=1;$i<=$n;$i++)
									echo '<th>'.$i.'</th>';
							?>
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
				</div>
			</div>
		</div>
			
	</div>
</div>