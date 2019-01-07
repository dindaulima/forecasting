<?php
	class alabama_model extends CI_Model{
		public function __construct(){
			$this->load->database();
		}

		public function getData($data){

			$query = $this->db->get_where('data.'.$data);

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
			}
			$query = $this->db->get_where('data.'.$object, $cond);

			return $query->result_array();
		}

		public function getDifference($data, $object, $param, $abs = false){
			$diff = array();
			$n = count($data[$object]);
			$totaldiff = 0;

			for ($i=0;$i<=$n-1;$i++) {
				$diff[$object][$i] = $data[$object][$i];
				if($i==0)
					$diff[$object][$i]['diff'] = 0;
				else if ($abs)
					$diff[$object][$i]['diff'] = round(abs($data[$object][$i][$param]-$data[$object][$i-1][$param]),3);
				else
					$diff[$object][$i]['diff'] = round($data[$object][$i][$param]-$data[$object][$i-1][$param],3);
				$totaldiff += $diff[$object][$i]['diff'];
			}
			// $diff[$object.'uji'] = $data[$object.'uji'];

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

		public function getMinMaxInterval($min, $max, $basis, $partisi = 6, $intval = 20){
			$vmin = $vmax = 0;
			$v1 = rand(0,100);
			$v2 = rand(100,150);

			$vmin = round(($min-$v1)/$basis, 0, PHP_ROUND_HALF_UP)*$basis;
			$vmax = round(($max+$v2)/$basis, 0, PHP_ROUND_HALF_UP)*$basis;
			if($intval>0)
				$length = $intval;
			else
				$length = ($vmax - $vmin)/$partisi;
			
			return array($vmin, $vmax, $length);
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
				$fuzzy[$k]['fs'] = $this->alabama_model->getSingleFuzzySet($data[$k][$param], $universe);
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
			$A = array();
			for($i=1; $i<=$cnt; $i++){
				for($j=1; $j<=$cnt; $j++){
					if($j == $i)
						$A['A'.$i][$j] = 1;
					else if($i == $j+1 || $i == $j-1)
						$A['A'.$i][$j] = 0.5;
					else
						$A['A'.$i][$j] = 0;
				}
			}
			return $A;
		}

		public function getFuzzyOutput($rule, $data, $partisi){
			foreach ($data as $k => $value) {
				if(empty($k) or $k == '-')
					continue;
				else
					foreach($value as $l => $target)
						for($i=1;$i<=$partisi;$i++)
							$R[$k][$i][] = $rule[$target][$i];
				
			}
			foreach ($R as $idx => $val)
				foreach ($val as $i => $rowv)
					$fuzzy[$idx][$i] = max($rowv);

			return $fuzzy;
		}
		
		public function getStandardizeFuzzyOutput($data, $partisi=null){
			$standard=array();
			foreach($data as $idx => $row){
				$n = array_sum($row);
				for($i=1;$i<=$partisi;$i++){
					$standard[$idx][$i] = round($row[$i]/$n,3);
				}
			}
			return $standard;
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

		public function HitungOutput($data, $fuzzyoutput, $year = null){
			$n = count($data);
			if(!empty($year)){
				$output = $fuzzyoutput[$data['fs']];
			}
			// else {
			// 	for ($i=0; $i<$n;$i++) {
			// 		if($i>1)
			// 			$output[$data[$i]['tahun']] = $fuzzyoutput[$data[$i-1]['fs']];
			// 		else 
			// 			$output[$data[$i]['tahun']] = array();
			// 	}
			// }
			return $output;
		}

		public function countOccurrences($arr, $n, $x) { 
		    $res = 0; 
		    for ($i=1; $i<=$n; $i++) 
		        if ($x == $arr[$i]) 
		          $res++; 
		    return $res; 
		} 

		public function forecast($data, $universe, $fuzzyoutput, $standartoutput, $nperiod = null){
			print_r($nperiod);
			$max = 1;
			$cnt = 0;
			$n = count($universe);
			$m = count($data);
			$forecast = $forecastoutput = array();
			foreach ($data as $k => $value) {
				if($k<=1){
					$forecastoutput[$value['tahun']] = null;
					$forecast[$value['tahun']] = null;
					$forecastedvar[$value['tahun']] = null;
					continue;
				} 
				$pastvalue = $data[$k-1];
				$output = $this->alabama_model->HitungOutput($pastvalue,$fuzzyoutput, $value['tahun']);
				// echo "<pre> output "; print_r($value['tahun']); echo "</pre>";
				// echo "<pre> output "; print_r($output); echo "</pre>";

				$forecastoutput[$value['tahun']] = $output;

				$countmax = $this->alabama_model->countOccurrences($output, $n, $max);
				// echo "<pre>"; print_r($countmax); echo "</pre>";
				$newvar =  0;

				if($countmax == 1){
					foreach($output as $i => $x){
						if($x==$max){
							$newvar = $universe[$i]['midpoint'];
							// echo "<pre> forcasted variation = ".$x.'x'.$universe[$i]['midpoint'].' = '; print_r($newvar); echo "</pre>";
						}
					}
				} else {
					$consecutive = true;
					for($i=1;$i<$n-1;$i++){
						if($output[$i]==$max and $output[$i+1]!=$max){
							$consecutive = false;
							break;
						}
					}
				// print_r($value['tahun']);echo "<br>";
				// print_r('urut = '.$consecutive);echo "<br>";
					$sumx = $sumvalue = 0;

					if($consecutive){
						foreach($output as $i => $x){
							if($x==$max){
								$sumvalue += $x*$universe[$i]['midpoint'];
								$sumx += $x;
							}
						}
						$newvar = $sumvalue/$sumx;
					} else{
						foreach($standartoutput[$pastvalue['fs']] as $i => $x){
							// print_r($x); echo "<br>";
								$sumvalue += $x*$universe[$i]['midpoint'];
								$sumx += $x;
						}
						$newvar = round($sumvalue/$sumx);
					}
					
				}
				$forecastedvar[$value['tahun']] = $newvar;
				$forecast[$value['tahun']] = $forecastedvar[$value['tahun']]+$pastvalue['jumlah'];
				// echo "<pre> forcasted value = ".$forecastedvar[$value['tahun']].'+'.$pastvalue['jumlah'].' = '; print_r($forecast[$value['tahun']]); echo "</pre>";
			}
			return array($forecastoutput,$forecastedvar, $forecast);
		}

		public function forecastuji($data, $universe, $fuzzyoutput, $standartoutput, $nperiod){
			echo "<pre>";print_r($data); echo "</pre>";
			$max = 1;
			$cnt = 0;
			$n = count($universe);
			$m = count($data);
			$forecast = $forecastoutput = array();
			for ($i=1;$i<$nperiod;$i++) {
				
				$output = $this->alabama_model->HitungOutput($data,$fuzzyoutput, $data['tahun']+1);
				$forecastoutput[$data['tahun']] = $output;
				$countmax = $this->alabama_model->countOccurrences($output, $n, $max);
				echo "<pre>"; print_r($countmax); echo "</pre>";
				echo "<pre>";print_r($output); echo "</pre>";
				$newvar =  0;
				if($countmax == 1){
					foreach($output as $i => $x){
						if($x==$max){
							$newvar = $universe[$i]['midpoint'];
						}
					}
				} else {
					$consecutive = true;
					for($i=1;$i<$n-1;$i++){
						if($output[$i]==$max and $output[$i+1]!=$max){
							$consecutive = false;
							break;
						}
					}
				// print_r($value['tahun']);echo "<br>";
				// print_r('urut = '.$consecutive);echo "<br>";
					$sumx = $sumvalue = 0;

					if($consecutive){
						foreach($output as $i => $x){
							if($x==$max){
								$sumvalue += $x*$universe[$i]['midpoint'];
								$sumx += $x;
							}
						}
						$newvar = $sumvalue/$sumx;
					} else{
						foreach($standartoutput[$data['fs']] as $i => $x){
							// print_r($x); echo "<br>";
								$sumvalue += $x*$universe[$i]['midpoint'];
								$sumx += $x;
						}
						$newvar = round($sumvalue/$sumx);
					}
					
				}
			echo "<pre>";print_r($newvar); echo "</pre>";
			die;

			}
			return array($forecastoutput,$forecastedvar, $forecast);
		}
	}
?>