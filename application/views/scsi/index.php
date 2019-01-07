<?php //echo validation_errors(); ?>
<?php //echo form_open('scsi/getByYear'); ?>
<!-- 	<div class="row">
		<div class="col-md-1"></div>
			<div class="col-md-10">
				<div class="row">
					<div class="form-group">
						<label>Tahun</label>
						<select name="thnawal" size="1" id="thnawal" class="input-thn form-control" >
							<?php 
								foreach($a_tahun as $t => $thn)
									echo "<option value=".$t.">".$thn."</option>";
							?>
						</select>
						<label>sampai</label>
						<select name="thnaakhir" size="1" id="thnakhir" class="input-thn form-control">
							<?php 
								foreach($a_tahun as $t => $thn)
									echo "<option value=".$t.">".$thn."</option>";
							?>
						</select>
						<button type="submit" name="filter-stock" class="btn btn-success">check</button>
		        	</div>
				</div>
			</div>
		<div class="col-md-1"></div>
	</div>
</form>	 -->
<div class="row">
	<div class="col-md-1"></div>
		<div class="col-md-10">
			<div class="table responsive">
				<table class="table table-bordered table-stripped">
					<thead>
						<tr>
							<th>Nomor</th>
							<th>Tanggal</th>
							<th>Index Harga</th>
							<!-- <th>|Dt+1 - Dt|</th> -->
						</tr>
					</thead>
					<tbody>
					<?php 
						$no = 1;
						foreach ($scsi as $value) { 
					?>
							
						<tr>
							<td><?php echo $no++?></td>
							<td><?php echo $value['tglstock']?></td>
							<td><?php echo $value['indexharga']?></td>
							<!-- <td><?php echo $value['diff']?></td> -->
						</tr>
						<?php }?>
					</tbody>
				</table>
			</div>
		</div>
	<div class="col-md-1"></div>
</div>
