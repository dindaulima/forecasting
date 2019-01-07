
<div class="row">
<?php $n = count($sevimapay); ?>
	<div class="col-md-6">
		<div class="table responsive">
			<table class="table table-bordered table-stripped">
				<thead>
					<tr>
						<th>Periode</th>
					<?php foreach ($kelas as $value) { ?>
						<th><?php echo $value?></th>
					<?php } ?>
						
					</tr>
				</thead>
				<tbody>
				<?php foreach ($sevimapay as $k => $row) { ?>
						<tr>
							<td><?php echo $k?></td>
							<?php foreach ($kelas as $value) { ?>
								<td><?php echo $row[$value]['jumlah']?></th>
							<?php } ?>
						</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="col-md-6">
		<?php echo form_open('sevimapay/forecast'); ?>
		<input type="hidden" name="object" id="object" value="sevimapay">
		<input type="hidden" name="var" id="var" value="jumlah">
		<input type="hidden" name="param" id="param" value="diff">
		<div class="row">
			<div class="col-md-1">
				<label>Bulan</label>
				<input type="hidden" name="period" id="period" value="periode">
			</div>
			<div class="col-md-3">
				<select name="periodeawal" size="1" id="periodeawal" class="input-thn form-control" >
					<?php 
						foreach($a_periode as $t => $bln)
							echo "<option value=".$t.">".$bln."</option>";
					?>
				</select>
			</div>
			<div class="col-md-1">
				<label>sampai</label>
			</div>
			<div class="col-md-3">
				<select name="periodeakhir" size="1" id="periodeakhir" class="input-thn form-control">
					<?php 
						foreach($a_periode as $t => $bln)
							echo "<option value=".$t.">".$bln."</option>";
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
						for($i=1;$i<count($sevimapay)/3;$i++){
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
