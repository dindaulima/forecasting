
<div class="row">
<?php $n = count($scsi); ?>
	<div class="col-md-2">
		<div class="table responsive">
			<table class="table table-bordered table-stripped">
				<thead>
					<tr>
						<th>Tahun</th>
						<th>Jumlah Mahasiswa</th>
					</tr>
				</thead>
				<tbody>
				<?php for ($i=0; $i < $n/3; $i++) { ?>
						<tr>
							<td><?php echo $scsi[$i]['tahun']?></td>
							<td><?php echo $scsi[$i]['jumlah']?></td>
						</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="col-md-2">
		<div class="table responsive">
			<table class="table table-bordered table-stripped">
				<thead>
					<tr>
						<th>Tahun</th>
						<th>Jumlah Mahasiswa</th>
					</tr>
				</thead>
				<tbody>
				<?php for ($i=$n/3; $i < 2*($n/3); $i++) { ?>
						<tr>
							<td><?php echo $scsi[$i]['tahun']?></td>
							<td><?php echo $scsi[$i]['jumlah']?></td>
						</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
		<div class="col-md-2">
		<div class="table responsive">
			<table class="table table-bordered table-stripped">
				<thead>
					<tr>
						<th>Tahun</th>
						<th>JUmlah Mahasiswa</th>
					</tr>
				</thead>
				<tbody>
				<?php for ($i=2*($n/3); $i < $n; $i++) { ?>
						<tr>
							<td><?php echo $scsi[$i]['tahun']?></td>
							<td><?php echo $scsi[$i]['jumlah']?></td>
						</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="col-md-6">
		<?php echo form_open('alabama/forecast'); ?>
		<input type="hidden" name="object" id="object" value="alabama">
		<input type="hidden" name="var" id="var" value="jumlah">
		<input type="hidden" name="param" id="param" value="diff">
		<div class="row">
			<div class="col-md-1">
				<label>Tahun</label>
				<input type="hidden" name="period" id="period" value="tahun">
			</div>
			<div class="col-md-3">
				<select name="thnawal" size="1" id="thnawal" class="input-thn form-control" >
					<?php 
						foreach($a_tahun as $t => $thn)
							echo "<option value=".$t.">".$thn."</option>";
					?>
				</select>
			</div>
			<div class="col-md-1">
				<label>sampai</label>
			</div>
			<div class="col-md-3">
				<select name="thnakhir" size="1" id="thnakhir" class="input-thn form-control">
					<?php 
						foreach($a_tahun as $t => $thn)
							echo "<option value=".$t.">".$thn."</option>";
					?>
				</select>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-md-3">
				<label>Periode peramalan</label>
			</div>
			<div class="col-md-2">
				<select name="forecastingperiod" size="1" id="forecastingperiod" class="input-thn form-control">
					<?php 
						for($i=1;$i<count($scsi)/2;$i++){
							echo "<option value=".$i.">".$i."</option>";
						}
					?>
				</select>
			</div>
			<div class="col-md-4">
				<button type="submit" name="filter-stock" class="btn btn-success">Fuzzy Tme Series</button>
        	</div>
		</div>
	</form>	
	</div>
</div>
