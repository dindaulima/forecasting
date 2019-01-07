<?php
	class Alabama extends CI_Controller{

		public function index(){
			$data['title'] = 'Alabama University';

			$data['alabama'] = $this->alabama_model->getData();
			$data['a_tahun'] = array('' => '-- Pilih Tahun --')+$this->alabama_model->ListTahun();

			$this->load->view('templates/header');
			$this->load->view('alabama/index',$data);
			$this->load->view('templates/footer');
		}

		public function view($page){
			if(!file_exists(APPPATH.'views/alabama/'.$page.'.php')){
				show_404();
			}

			$data['title'] = ucfirst($page);

			$this->load->view('templates/header');
			$this->load->view('alabama/view',$data); 
			$this->load->view('templates/footer');
		}

		public function getByYear(){
			$thnawal = $this->input->post('thnawal');
			$thnakhir = $this->input->post('thnakhir');

			$data['alabama'] = $this->alabama_model->getData($thnawal, $thnakhir);
			$data['a_tahun'] = array('' => '-- Pilih Tahun --')+$this->alabama_model->ListTahun();

			$this->load->view('templates/header');
			$this->load->view('pages',$data);
			$this->load->view('templates/footer');
		}

		public function forecast(){
			$data['title'] = 'Alabama University';

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

			if($period == 'tahun'){
				$thnawal = $this->input->post('thnawal');
				$thnakhir = $this->input->post('thnakhir');
				$data[$object] = $this->alabama_model->getDataByYear($thnawal, $thnakhir, $object);
				// $data[$object.'uji'] = $this->alabama_model->getDataByYear($thnawal, $thnakhir, $object);
			}
			
			$n = count($data[$object]);
			$data = $this->alabama_model->getDifference($data, $object, $var, $abs);

			//data description
			list($data['desc']['min'], $data['desc']['max'], $data['desc']['mean']) = $this->alabama_model->DataDescription($data[$object], $param);
			list($data['desc']['mindiff'], $data['desc']['maxdiff'], $data['desc']['meandiff']) = $this->alabama_model->DataDescription($data[$object], $param);

			list($data['desc']['vmin'], $data['desc']['vmax'], $data['desc']['length']) = $this->alabama_model->getMinMaxInterval($data['desc']['min'.$variation],$data['desc']['max'.$variation], $basis, $partisi,$panjang);
			
			//semesta
			$data['universe'] = $this->alabama_model->getUniverse($data['desc']['vmin'], $data['desc']['vmax'], $data['desc']['length']);

			//menentukan fuzzy set dan fuzzy logic relationship
			$fuzzy = $this->alabama_model->getFuzzySet($data[$object], $data['universe'], $param);

			foreach ($data[$object] as $k => $value) {
				$data[$object][$k]['fs'] = $fuzzy[$k]['fs'];
				$data[$object][$k]['flr'] = $fuzzy[$k]['flr'];
			}

			//menentukan fuzzy logic relationship group
			list($flr, $flrg) = $this->alabama_model->getFLRG($data[$object]);
			uksort($flrg,"strnatcmp");
			foreach ($flrg as $i => $row) {
				$data['flrg'][$i] = implode(', ',$row);
			}
			//fuzzy rule
			$data['rule'] = $this->alabama_model->getFuzzyRule($flrg);

			//fuzzy output
			$data['fuzzyoutput'] = $this->alabama_model->getFuzzyOutput($data['rule'],$flrg, $partisi);
			//standaridize fuzzy output
			$data['sfuzzyoutput'] = $this->alabama_model->getStandardizeFuzzyOutput($data['fuzzyoutput'], $partisi);

			//hitung output
			// $data['output'] = $this->alabama_model->HitungOutput($data[$object],$data['fuzzyoutput']);

			list($data['output'],$data['forecastedvar'], $data['forecast']) = $this->alabama_model->Forecast($data[$object], $data['universe'], $data['fuzzyoutput'], $data['sfuzzyoutput']);

			$data['object'] = $object;
			// if(!empty($data[$object.'uji']))
				$data['forecastuji'] = $this->alabama_model->Forecastuji($data[$object][count($data[$object])-1], $data['universe'], $data['fuzzyoutput'], $data['sfuzzyoutput'], $forecastingperiod);
// echo "<pre>";print_r($data['output']);echo "</pre>";
			$this->load->view('templates/header');
			$this->load->view('alabama/forecast',$data);
			$this->load->view('templates/footer');
		}
	}
?>