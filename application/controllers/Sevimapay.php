<?php
	class sevimapay extends CI_Controller{

		public function index(){
			$data['title'] = 'Sevimapay';

			$data['sevimapay'] = $this->sevimapay_model->getData();
			$data['a_tahun'] = array('' => '-- Pilih Tahun --')+$this->sevimapay_model->ListTahun();

			$this->load->view('templates/header');
			$this->load->view('sevimapay/index',$data);
			$this->load->view('templates/footer');
		}

		public function view($page){
			if(!file_exists(APPPATH.'views/sevimapay/'.$page.'.php')){
				show_404();
			}

			$data['title'] = ucfirst($page);

			$this->load->view('templates/header');
			$this->load->view('sevimapay/view',$data); 
			$this->load->view('templates/footer');
		}

		public function getByYear(){
			$thnawal = $this->input->post('thnawal');
			$thnakhir = $this->input->post('thnakhir');

			$data['sevimapay'] = $this->sevimapay_model->getData($thnawal, $thnakhir);
			$data['a_tahun'] = array('' => '-- Pilih Tahun --')+$this->sevimapay_model->ListTahun();

			$this->load->view('templates/header');
			$this->load->view('pages',$data);
			$this->load->view('templates/footer');
		}

		public function forecast(){
			$data['title'] = 'Sevimapay';

			$forecastingperiod = $this->input->post('forecastingperiod');
			$period = $this->input->post('period');
			$object = $this->input->post('object');
			$var = $this->input->post('var');
			$param = $this->input->post('param');

			if(empty($param))
				$param = $var;

			$abs = false;
			$panjang = 0;
			$partisi = 6;
			$basis = 100;
			$variation = 'diff';

			$tawal = $this->input->post('periodeawal');
			$takhir = $this->input->post('periodeakhir');
			$sevimapay = $this->sevimapay_model->getDataByField($tawal, $takhir, $period, $object);
			$a_data = $this->sevimapay_model->MappingData($sevimapay, 'kelas');
			$a_kelas = $this->sevimapay_model->SortKelas($this->sevimapay_model->DistinctField('kelas'));
			$a_periode = $this->sevimapay_model->getCombo($this->sevimapay_model->DistinctField('periode'), 'periode');
			$data['a_kelas'] = $a_kelas;

			foreach ($a_kelas as $k) {

				// print_r($k); echo "<br>";

				$n = count($a_data[$k]);
				$a_diff = $this->sevimapay_model->getDifference($a_data[$k], $var, $abs);

				//data description
				list($min, $max, $mean) = $this->sevimapay_model->DataDescription($a_diff, $param);
				list($minvar, $maxvar, $meanvar) = $this->sevimapay_model->DataDescription($a_diff, $param);
				list($vmin, $vmax, $length) = $this->sevimapay_model->getMinMaxInterval($minvar,$maxvar, $basis, $partisi,$panjang);

				//semesta
				$a_universe = $this->sevimapay_model->getUniverse($vmin, $vmax, $length);
				$partisi = count($a_universe);

				//fuzzy rule
				$a_rule = $this->sevimapay_model->getFuzzyRule($partisi);

				//menentukan fuzzy set dan fuzzy logic relationship
				$fuzzy = $this->sevimapay_model->getFuzzySet($a_diff, $a_universe, $param);
				foreach ($a_diff as $i => $value) {
					$a_diff[$i]['fs'] = $fuzzy[$i]['fs'];
					$a_diff[$i]['flr'] = $fuzzy[$i]['flr'];
				}
				
				//menentukan fuzzy logic relationship group
				list($a_flr, $a_flrg) = $this->sevimapay_model->getFLRG($a_diff, $a_rule);

				uksort($a_flrg,"strnatcmp"); //sorting by natural order

				//fuzzy output
				$a_fuzzyoutput = $this->sevimapay_model->getFuzzyOutput($a_rule,$a_flrg, $partisi);
				//standaridize fuzzy output
				$a_sfuzzyoutput = $this->sevimapay_model->getStandardizeFuzzyOutput($a_fuzzyoutput, $partisi);
// echo "<pre> fuzzyoutput "; print_r($a_fuzzyoutput); echo "</pre>";
// echo "<pre> standardfoutput"; print_r($a_sfuzzyoutput); echo "</pre>";
				
				list($a_output,$a_forecastedvar, $a_forecast) = $this->sevimapay_model->Forecast($a_diff, $a_universe, $a_fuzzyoutput, $a_sfuzzyoutput, $forecastingperiod);
// echo "<pre> output "; print_r($a_output); echo "</pre>";
				$data[$object][$k] = $a_diff;
				$data['a_desc'][$k] = array(
							"min" => $min,
							"max" => $max,
							"mean" => $mean,
							"min".$variation => $minvar,
							"max".$variation => $maxvar,
							"mean".$variation => $meanvar,
							"vmin" => $vmin,
							"vmax" => $vmax,
							"length" => $length,
					);
				$data['a_universe'][$k] = $a_universe;
				$data['a_rule'][$k] = $a_rule;
				$data['a_fuzzyoutput'][$k] = $a_fuzzyoutput;
				$data['a_sfuzzyoutput'][$k] = $a_sfuzzyoutput;
				$data['a_output'][$k] = $a_output;
				$data['a_forecastedvar'][$k] = $a_forecastedvar;
				$data['a_forecast'][$k] = $a_forecast;
				foreach ($a_flrg as $i => $row) {
					if(empty($row))
						$data['a_flrg'][$k][$i] = null;
					else
						$data['a_flrg'][$k][$i] = implode(', ',$row);
				}
				// $data['forecastuji'] = $this->alabama_model->Forecastuji($data[$object][count($data[$object])-1], $data['universe'], $data['fuzzyoutput'], $data['sfuzzyoutput'], $forecastingperiod);
// echo "<pre> data "; print_r($a_diff[count($a_diff)-1]); echo "</pre>";
// echo "<pre> universe "; print_r($a_output); echo "</pre>";
// echo "<pre> foutput"; print_r($a_fuzzyoutput); echo "</pre>";
// echo "<pre> standardfoutput"; print_r($a_sfuzzyoutput); echo "</pre>";

				// $a_datauji = $this->sevimapay_model->Forecastuji($a_diff[count($a_diff)-1], $a_universe, $a_fuzzyoutput, $a_sfuzzyoutput, $a_periode, $forecastingperiod);
// echo "<pre>"; print_r($a_datauji); echo "</pre>";
			}
			
			$this->load->view('templates/header');
			$this->load->view('sevimapay/forecast',$data);
			$this->load->view('templates/footer');
		}
	}
?>