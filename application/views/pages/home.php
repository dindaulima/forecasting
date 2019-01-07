
<div class="row">
<?php $n = count($scsi); ?>
	<div class="col-md-2">
		<div class="table responsive">
			<table class="table table-bordered table-stripped">
				<thead>
					<tr>
						<th>Tanggal</th>
						<th>Index Harga</th>
					</tr>
				</thead>
				<tbody>
				<?php for ($i=0; $i < $n/3; $i++) { ?>
						<tr>
							<td><?php echo $scsi[$i]['tglstock']?></td>
							<td><?php echo $scsi[$i]['indexharga']?></td>
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
						<th>Tanggal</th>
						<th>Index Harga</th>
					</tr>
				</thead>
				<tbody>
				<?php for ($i=$n/3; $i < 2*($n/3); $i++) { ?>
						<tr>
							<td><?php echo $scsi[$i]['tglstock']?></td>
							<td><?php echo $scsi[$i]['indexharga']?></td>
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
						<th>Tanggal</th>
						<th>Index Harga</th>
					</tr>
				</thead>
				<tbody>
				<?php for ($i=2*($n/3); $i < $n; $i++) { ?>
						<tr>
							<td><?php echo $scsi[$i]['tglstock']?></td>
							<td><?php echo $scsi[$i]['indexharga']?></td>
						</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="col-md-6">
		<?php echo form_open('scsi/fst'); ?>
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
			<div class="col-md-4">
				<button type="submit" name="filter-stock" class="btn btn-success">Fuzzy Tme Series</button>
        	</div>
		</div>
	</form>	
	<?php echo form_open('scsi/forecast'); ?>
		<input type="hidden" name="period" id="period" value="tanggal">
		<input type="hidden" name="object" id="object" value="scsi">
		<input type="hidden" name="var" id="var" value="indexharga">
		<div class="row">
			<div class="col-md-1">
				<label>Tanggal</label>
			</div>
			<div class="col-md-3">
				<input type="date" id="tglawalin" name="tglawalin">
			</div>
			<div class="col-md-1">
				<label>sampai</label>
			</div>
			<div class="col-md-3">
				<input type="date" id="tglakhirin" name="tglakhirin">
			</div>
			<div class="col-md-4">
        	</div>
		</div>
		<div class="row">
			<div class="col-md-1">
				<label>Tanggal</label>
			</div>
			<div class="col-md-3">
				<input type="date" id="tglawalout" name="tglawalout">
			</div>
			<div class="col-md-1">
				<label>sampai</label>
			</div>
			<div class="col-md-3">
				<input type="date" id="tglakhirout" name="tglakhirout">
			</div>
			<div class="col-md-4">
				<button type="submit" name="filter-stock" class="btn btn-success">FORECAST</button>
        	</div>
		</div>
	</form>	
	</div>
</div>
