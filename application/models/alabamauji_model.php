<?php
	class alabamauji_model extends CI_Model{
		public function __construct(){
			$this->load->database();
		}

		public function getData($thnawal = null, $thnakhir = null){
			$cond = '1=1';
			if(empty($thnawal) and empty($thnakhir)){
				$query = $this->db->get('data.scsi');
				return $query->result_array();
			} 

			if(!empty($thnawal)){
				$cond .= " and extract(year from tglstock) >= ".$this->db->escape($thnawal);
			} 
			if(!empty($thnakhir)){
				$cond .= " and extract(year from tglstock) <= ".$this->db->escape($thnakhir);
			}
			
			$query = $this->db->get_where('data.scsi', $cond);

			return $query->result_array();
		}

		public function getDataByTgl($tglawal = null, $tglakhir = null){
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
			
			$query = $this->db->get_where('data.scsi', $cond);

			return $query->result_array();
		}

		public function ListTahun(){
			for($i=1990;$i<=date('Y');$i++)
				$tahun[$i] = $i;

			return $tahun;
		}

		public function getDataByYear($thn){
			if(!empty($thn)){
				$cond = " and extract(year from tglstock) = ".$this->db->escape($thn);
			} 
			$query = $this->db->get_where('data.scsi',"1=1 " . $cond);

			return $query->result_array();
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

		public function getUniverse($Dmin, $Dmax, $panjang){
			$x = $Dmin;
			$i = 1;
			while($x<$Dmax){
				$data[$i]['min'] = $x;
				$data[$i]['max'] = $x+$panjang;
				$data[$i]['midpoint'] = $x+$panjang/2;

				$i++;
				$x+=$panjang;
			}
			return $data;
		}

		public function getSingleFuzzySet($indexharga, $universe){
			$fs = null;
			foreach ($universe as $j => $row) {
				if($indexharga>$row['min'] and $indexharga<$row['max']){
					$fs = 'A'.$j;
					break;
				}
			}
			return $fs;
		}

		public function getFuzzySet($data, $universe){
			//menentun fuzzy set dan fuzzy logic relationship
			foreach ($data as $k => $value) {
				if($k==0){
					$fuzzy[$k]['fs'] = null;
					$fuzzy[$k]['flr']['dari'] = null;
					$fuzzy[$k]['flr']['ke'] = null;
					continue;
				}

				$fuzzy[$k]['fs'] = $this->scsiuji_model->getSingleFuzzySet($data[$k]['indexharga'], $universe);
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
			$fs = $fspoint;
			foreach ($data as $k => $value) {
				if(empty($fs)){
					$fs = $value['fs'];
				} 
				if(empty($fs)){
					$forecast[$k] = null;
					continue;
				}
				$forecast[$k] = $this->scsiuji_model->singleforecast($fs, $flrg, $universe);	
				$fs = $this->scsiuji_model->getSingleFuzzySet($forecast[$k],$universe);
			}
			return $forecast;
		}
	}
?>