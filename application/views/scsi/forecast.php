

<div class="row">
	<div class="col-md-6">
		<div class="table responsive">
			<table class="table table-bordered table-stripped">
				<thead>
					<tr>
						<th>Tanggal</th>
						<th>Index Harga</th>
						<th>Forecast</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach($scsi as $t => $row){ ?>
						<tr>
							<td><?php echo $row['tglstock']?></td>
							<td><?php echo $row['indexharga']?></td>
							<td><?php echo $forecast[$t]?></td>
						</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="col-md-6">
		<div class="table responsive">
			<table class="table table-bordered table-stripped">
				<thead>
					<tr>
						<th>Tanggal</th>
						<th>Index Harga</th>
						<th>Forecast</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach($scsiuji as $t => $row){ ?>
						<tr>
							<td><?php echo $row['tglstock']?></td>
							<td><?php echo $row['indexharga']?></td>
							<td><?php echo $forecastuji[$t]?></td>
						</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>