<?php
	class scsi_model extends CI_Model{
		public function __construct(){
			$this->load->database();
		}

		public function getData($data){

			$query = $this->db->get_where('data.'.$data);

			return $query->result_array();
		}

		public function getDataByTgl($tglawal = null, $tglakhir = null, $object){
			$cond = '1=1';
			if(empty($tglawal) and empty($tglakhir)){
				$query = $this->db->get('data.scsi');
				return $query->result_array();
			} 

			if(!empty($tglawal)){
				$cond .= " and tglstock >= ".$this->db->escape($tglawal);
			} 
			if(!empty($tglakhir)){
				$cond .= " and tglstock <= ".$this->db->escape($tglakhir);
			}
			
			$query = $this->db->get_where('data.'.$object, $cond);

			return $query->result_array();
		}

		public function ListTahun(){
			for($i=1970;$i<=date('Y');$i++)
				$tahun[$i] = $i;

			return $tahun;
		}

		public function getDataByYear($thnawal, $thnakhir, $object){
			$cond = " 1=1";
			if($object == 'alabama'){
				if(!empty($thnawal))
					$cond .= " and tahun >= ".$this->db->escape($thnawal);
				if(!empty($thnakhir))
					$cond .= " and tahun <= ".$this->db->escape($thnakhir);
			} else {
				if(!empty($thnawal))
					$cond .= " and extract(year from tglstock) >= ".$this->db->escape($thnawal);
				if(!empty($thnakhir))
					$cond .= " and extract(year from tglstock) <= ".$this->db->escape($thnakhir);
			}
			$query = $this->db->get_where('data.'.$object, $cond);

			return $query->result_array();
		}

		public function getDifference($data, $object, $param, $abs = false){
			$diff = array();
			$n = count($data[$object]);
			$totaldiff = 0;
			$diff[$object.'uji'] = $data[$object.'uji'];

			for ($i=0;$i<=$n-1;$i++) {
				$diff[$object][$i] = $data[$object][$i];
				if($i==0 or $i==$n-1)
					$diff[$object][$i]['diff'] = 0;
				else if ($abs)
					$diff[$object][$i]['diff'] = round(abs($data[$object][$i-1][$param]-$data[$object][$i][$param]),3);
				else
					$diff[$object][$i]['diff'] = round($data[$object][$i-1][$param]-$data[$object][$i][$param],3);

				$totaldiff += $diff[$object][$i]['diff'];
			}

			return $diff;

		}

		public function DataDescription($data, $param){
			$list = array();
			foreach ($data as $value) {
				$list[] = $value[$param];
			}
			$min = min($list);
			$max = max($list);
			$mean = array_sum($list)/count($list);

			return array($min, $max, $mean);
		}

		public function getMinMaxInterval($min, $max, $basis, $part = 6, $intval = 20){
			$vmin = $vmax = 0;

			$vmin = round($min/$basis, 0, PHP_ROUND_HALF_UP)*$basis;
			$vmax = round($max/$basis, 0, PHP_ROUND_HALF_UP)*$basis;
			if($intval>0)
				$length = $intval;
			else
				$length = ($vmax - $vmin)/$part;
			
			return array($vmin, $vmax, $length);
		}

		public function getUniverse($Dmin, $Dmax, $panjang){
			$x = $Dmin;
			$i = 1;
			$data = array();
			while($x<$Dmax){
				$data[$i]['min'] = $x;
				$data[$i]['max'] = $x+$panjang;
				$data[$i]['midpoint'] = $x+$panjang/2;

				$i++;
				$x+=$panjang;
			}
			return $data;
		}

		public function getSingleFuzzySet($data, $universe){
			$fs = null;
			foreach ($universe as $j => $row) {
				if($data>$row['min'] and $data<$row['max']){
					$fs = 'A'.$j;
					break;
				}
			}
			return $fs;
		}

		public function getFuzzySet($data, $universe, $param){
			//menentun fuzzy set dan fuzzy logic relationship
			foreach ($data as $k => $value) {
				if($k==0){
					$fuzzy[$k]['fs'] = null;
					$fuzzy[$k]['flr']['dari'] = null;
					$fuzzy[$k]['flr']['ke'] = null;
					continue;
				}
				$fuzzy[$k]['fs'] = $this->scsi_model->getSingleFuzzySet($data[$k][$param], $universe);
				$fuzzy[$k]['flr']['dari'] = $fuzzy[$k-1]['fs'];
				$fuzzy[$k]['flr']['ke'] = $fuzzy[$k]['fs'];
				
			}
			return $fuzzy;
		}

		public function getFLRG($data){
			//menentun fuzzy set dan fuzzy logic relationship GROUP
			$flr = array();
			foreach ($data as $k => $value) {
				$flr[$value['flr']['dari']][] = $value['flr']['ke'];
			}
			foreach ($flr as $Ai => $rowa) {
				sort($rowa);
				$flrg[$Ai] = array_unique($rowa);
			}
			return array($flr,$flrg);
		}

		public function getFuzzyRule($data){
			$cnt =0;
			foreach ($data as $k => $value) {
				if(empty($k) or $k == '-')
					continue;
				$cnt++;
			}
			for($i=1; $i<=$cnt; $i++){
				for($j=1; $j<=$cnt; $j++){
					if($j == $i)
						$A[$i][$j] = 1;
					else if($i == $j+1 || $i == $j-1)
						$A[$i][$j] = 0.5;
					else
						$A[$i][$j] = 0;
				}
			}
			return $A;
		}

		public function singleforecast($fs, $flrg, $universe){
			$sum = 0;
			$cnt=0;
			$n = count($flrg[$fs]);			
			foreach ($flrg[$fs] as $row){
				$cnt++;
				if($n<=1 and empty($row))
					$sum = $universe[substr($fs,1)]['midpoint'];
				else
					$sum += $universe[substr($row,1)]['midpoint'];
			}
			return $sum/$cnt;
		}

		public function forecast($data, $flrg, $universe, $fspoint = null){
			if(!empty($fspoint)){
				$valfs = false;
				$fs = $fspoint;
			}else
				$valfs = true;

			foreach ($data as $k => $value) {
			// print_r($value); echo "<br>";
			// print_r(empty($fs));
				if($valfs)
					$fs = $value['fs'];
				 
			// print_r($fs); echo "<br>";
				if(empty($fs)){
					$forecast[$k] = null;
			// print_r('null = '.$forecast[$k]); echo "<br>";
					continue;
				}
				$forecast[$k] = $this->scsi_model->singleforecast($fs, $flrg, $universe);	
			// print_r($forecast[$k]); echo "<br>";
				$fs = $this->scsi_model->getSingleFuzzySet($forecast[$k],$universe);
			}
			return $forecast;
		}
	}
?>