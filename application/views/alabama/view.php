<div class="row">
	<div class="col-md-1"></div>
		<div class="col-md-10">
			<div class="table responsive">
				<table class="table table-bordered table-stripped">
					<thead>
						<tr>
							<th>No</th>
							<th>t</th>
							<th>Dt</th>
							<th>|Dt+1 - Dt|</th>
						</tr>
					</thead>
					<tbody>
					<?php 
						$no = 1;
						foreach ($scsi as $value) { 
					?>
							
						<tr>
							<td><?php echo $no++?></td>
							<td><?php echo $value['tahun']?></td>
							<td><?php echo $value['jumlah']?></td>
							<td><?php echo $value['diff']?></td>
						</tr>
						<?php }?>
					</tbody>
				</table>
			</div>
		</div>
	<div class="col-md-1"></div>
</div>
