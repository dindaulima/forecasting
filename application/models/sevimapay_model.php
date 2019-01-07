<?php
	class sevimapay_model extends CI_Model{
		public function __construct(){
			$this->load->database();
		}

		public function getData($data){

			$query = $this->db->get_where('data.'.$data);

			return $query->result_array();
		}

		public function MappingData($data, $param1, $param2=null){
			if(!empty($param2)){
				foreach ($data as $value) {
					$datanew[$value[$param1]][$value[$param2]]['jumlah']=$value['jumlah'];
				}
			} else {
				foreach ($data as $value) {
					$datanew[$value[$param1]][]=$value;
				}
			}
			return $datanew;
		}

		public function SortKelas($kelas){
			foreach($kelas as $k){
				$class[$k['kelas']] = str_replace('X', '', $k['kelas']);
			}
			sort($class);
			
			$kelas = array();
			foreach($class as $k)
				$kelas[$k-1] = 'X'.$k;

			return $kelas;
		}

		public function DistinctField($field){
			$this->db->distinct();
			$this->db->select($field);
			$query = $this->db->get('data.sevimapay');

			return $query->result_array();
		}

	 	public function getCombo($data, $field){
	 		foreach ($data as $key => $value) {
	 			$combo[$value[$field]] = $value[$field];
	 		}
	 		// echo "<pre>"; print_r($combo); echo "</pre>";
	 		ksort($combo);
	 		// echo "<pre>"; print_r($combo); echo "</pre>";

	 		return $combo;
	 	}

		public function getDataByField($tawal, $takhir, $param, $object){
			$cond = " 1=1";
			// if($object == 'alabama'){
				if(!empty($tawal))
					$cond .= " and ".$param." >= ".$this->db->escape($tawal);
				if(!empty($takhir))
					$cond .= " and ".$param." <= ".$this->db->escape($takhir);
			// }
			$query = $this->db->get_where('data.'.$object, $cond);

			return $query->result_array();
		}

		public function getDifference($data, $param, $abs = false){
			$diff = array();
			$n = count($data);
			$totaldiff = 0;

			for ($i=0;$i<=$n-1;$i++) {
				$diff[$i] = $data[$i];
				if($i==0)
					$diff[$i]['diff'] = 0;
				else if ($abs)
					$diff[$i]['diff'] = round(abs($data[$i][$param]-$data[$i-1][$param]),3);
				else
					$diff[$i]['diff'] = round($data[$i][$param]-$data[$i-1][$param],3);
				$totaldiff += $diff[$i]['diff'];
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
			$v1 = rand(0,$basis);
			$v2 = rand($basis,2*$basis);

			$vmin = floor(($min-$v1)/$basis)*$basis; //harus lebih kecil min
			$vmax = ceil(($max+$v2)/$basis)*$basis; //harus lebih besar dari max

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
				$fuzzy[$k]['fs'] = $this->sevimapay_model->getSingleFuzzySet($data[$k][$param], $universe);
				$fuzzy[$k]['flr']['dari'] = $fuzzy[$k-1]['fs'];
				$fuzzy[$k]['flr']['ke'] = $fuzzy[$k]['fs'];
			}
			return $fuzzy;
		}

		public function getFLRG($data, $rule){
			//menentun fuzzy set dan fuzzy logic relationship GROUP
			$flr = array();
			foreach ($data as $k => $value) {
				$flr[$value['flr']['dari']][] = $value['flr']['ke'];
			}

			foreach ($flr as $Ai => $rowa) {
				sort($rowa);
				$t_flrg[$Ai] = array_unique($rowa);
			}

			//rearranging the flrg
			foreach ($rule as $Ai => $val) {
				if(!empty($t_flrg[$Ai])){
					$flrg[$Ai] = $t_flrg[$Ai];
				} else
					$flrg[$Ai] = null;
			}

			return array($flr,$flrg);
		}

		public function getFuzzyRule($partisi){
			// $cnt =0;
			// foreach ($data as $k => $value) {
			// 	if(empty($k) or $k == '-')
			// 		continue;
			// 	$cnt++;
			// }
			$A = array();
			for($i=1; $i<=$partisi; $i++){
				for($j=1; $j<=$partisi; $j++){
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
			$R = $fuzzy = [];
			foreach ($data as $k => $value) {
				if(empty($value))
					$R[$k] = null;
				else
					foreach($value as $l => $target)
						if(!empty($target))
							for($i=1;$i<=$partisi;$i++)
								$R[$k][$i][] = $rule[$target][$i];
			}
			foreach ($R as $idx => $val)
				if(empty($val))
					$fuzzy[$idx]=null;
				else
					foreach ($val as $i => $rowv)
						$fuzzy[$idx][$i] = max($rowv);
				

			return $fuzzy;
		}
		
		public function getStandardizeFuzzyOutput($data, $partisi=null){
			$standard=array();
			foreach($data as $idx => $row){
				if(empty($row))
					$standard[$idx]=null;
				else {
					$n = array_sum($row);
					for($i=1;$i<=$partisi;$i++)
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

		public function HitungOutput($data, $fuzzyoutput){
				// echo "<pre>data "; print_r($data); echo "</pre>";
				// echo "<pre>fuzzyoutput "; print_r($fuzzyoutput); echo "</pre>";
			
			$output = $fuzzyoutput[$data['fs']];
				// echo "<pre>output "; print_r($output); echo "</pre>";

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
			$max = 1;
			$cnt = 0;
			$n = count($universe);
			$m = count($data);
			$forecast = $forecastoutput = array();
			foreach ($data as $k => $value) {

				if($k<=1 or empty($value['fs'])){
					$forecastoutput[$value['periode']] = null;
					$forecast[$value['periode']] = null;
					$forecastedvar[$value['periode']] = null;
					continue;
				} 
				$pastvalue = $data[$k-1];

				if(empty($pastvalue['fs']) or empty($fuzzyoutput)){
					$forecastoutput[$value['periode']] = null;
					$forecast[$value['periode']] = null;
					$forecastedvar[$value['periode']] = null;
					continue;
				}


				$output = $this->sevimapay_model->HitungOutput($pastvalue,$fuzzyoutput);
				$forecastoutput[$value['periode']] = $output;
				$countmax = $this->sevimapay_model->countOccurrences($output, $n, $max);
				$newvar =  0;
				if(!empty($output)){ //jika fuzzy outputnya nol maka peramalannya nol

				// } else {

					if($countmax == 1){ //jika terdapat tepat 1 maximum (1) ambil midpointnya
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
						$sumx = $sumvalue = 0;

						if($consecutive){ //jika terdapat 2 atau lebih maximum (1) dan semuanya berurutan maka ambil rata-rata midpoint dari nilai maximum
							foreach($output as $i => $x){
								if($x==$max){
									$sumvalue += $x*$universe[$i]['midpoint'];
									$sumx += $x;
								}
							}
							$newvar = $sumvalue/$sumx;
						} else{ //selain itu ambil rata-rata dari fuzzy output yg distandarisasi x midpoint semestanya
							foreach($standartoutput[$pastvalue['fs']] as $i => $x){
									$sumvalue += $x*$universe[$i]['midpoint'];
									$sumx += $x;
							}
							$newvar = round($sumvalue/$sumx,0);

						}
						
					}
				}

				$forecastedvar[$value['periode']] = $newvar;
				$forecast[$value['periode']] = ($forecastedvar[$value['periode']]+$pastvalue['jumlah'])<0?0:$forecastedvar[$value['periode']]+$pastvalue['jumlah'];
				// echo "<pre> forcasted value = ".$forecastedvar[$value['tahun']].'+'.$pastvalue['jumlah'].' = '; print_r($forecast[$value['tahun']]); echo "</pre>";
			}
			return array($forecastoutput,$forecastedvar, $forecast);
		}

		public function getnextperiod($t0, $basis = 12){ //untuk mendapatkan nilai periode t+1
			//$basis 12 merepresentasikan banyak bulan dalam tahun
			list($thn,$bln) = explode('-',$t0);
			$bln = (int)$bln;

			if($bln==$basis){
				$thn1 = $thn+1;
				$bln1 = 1;
			} else if($bln>0 && $bln<$basis){
				$thn1 = $thn;
				$bln1 = $bln+1;
			} else
				return null;

			return $thn1.'-'.str_pad($bln1, 2,'0',STR_PAD_LEFT);
		}

		public function forecastuji($data, $universe, $fuzzyoutput, $standartoutput, $periode, $nperiod){
			// echo "<pre>";print_r($data); echo "</pre>";
			$max = 1;
			$cnt = 0;
			$n = count($universe);
			$m = count($data);
			$forecast = $forecastoutput = array();

			if(empty($fuzzyoutput))
				return array (null,null,null);

			for ($i=1;$i<=$nperiod;$i++) {
				$nextperiod = $this->sevimapay_model->getnextperiod($data['periode']);

				$output = $this->sevimapay_model->HitungOutput($data,$fuzzyoutput, $nextperiod);
				$forecastoutput[$nextperiod] = $output;
				$countmax = $this->sevimapay_model->countOccurrences($output, $n, $max);
				// echo "<pre>"; print_r($countmax); echo "</pre>";
				echo "<pre> output";print_r($output); echo "</pre>";
				$newvar =  0;
				if(empty($output)){
					//jika Ai null karena FLRG untuk Ai null

				} else {
					//jika FLRG Ai one to one maka 


					//jika FLRG Ai one to many
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
				}
			// echo "<pre>";print_r($newvar); echo "</pre>";
			// die;
				$forecastedvar[$nextperiod] = ($newvar<0)?0:$newvar;
				$forecast[$value['periode']] = ($forecastedvar[$nextperiod]+$pastvalue['jumlah'])<0?0:$forecastedvar[$nextperiod]+$pastvalue['jumlah'];
				$forecast[$nextperiod] = $forecastedvar[$nextperiod]+$data['jumlah'];
			}
			return array($forecastoutput,$forecastedvar, $forecast);
		}
	}
?>