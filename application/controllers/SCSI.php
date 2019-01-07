<?php
	class SCSI extends CI_Controller{

		public function index(){
			$data['title'] = 'Shanghai Compound Stock Index';

			$data['scsi'] = $this->scsi_model->getData();
			$data['a_tahun'] = array('' => '-- Pilih Tahun --')+$this->scsi_model->ListTahun();

			$this->load->view('templates/header');
			$this->load->view('scsi/index',$data);
			$this->load->view('templates/footer');
		}

		public function view($page){
			if(!file_exists(APPPATH.'views/scsi/'.$page.'.php')){
				show_404();
			}

			$data['title'] = ucfirst($page);

			$this->load->view('templates/header');
			$this->load->view('scsi/view',$data); 
			$this->load->view('templates/footer');
		}

		public function getByYear(){
			$thnawal = $this->input->post('thnawal');
			$thnakhir = $this->input->post('thnakhir');

			$data['scsi'] = $this->scsi_model->getData($thnawal, $thnakhir);
			$data['a_tahun'] = array('' => '-- Pilih Tahun --')+$this->scsi_model->ListTahun();

			$this->load->view('templates/header');
			$this->load->view('pages',$data);
			$this->load->view('templates/footer');

		}

		public function forecast(){
			$data['title'] = 'Shanghai Compound Stock Index';

			$period = $this->input->post('period');
			$object = $this->input->post('object');
			$var = $this->input->post('var');
			$param = $this->input->post('param');
			if(empty($param))
				$param = $var;
			if($object == 'scsi'){
				$abs = true;
				$panjang = 20;
				$basis = 10;
				$variation = '';
			}
			
			if($period == 'tahun'){
				$thnawal = $this->input->post('thnawal');
				$thnakhir = $this->input->post('thnakhir');
				$data[$object] = $this->scsi_model->getDataByYear($thnawal, $thnakhir, $object);
				$data[$object.'uji'] = $this->scsi_model->getDataByYear($thnawal, $thnakhir, $object);
			} else if ($period == 'tanggal'){
				$tglawalin = $this->input->post('tglawalin');
				$tglakhirin = $this->input->post('tglakhirin');
				$tglawalout = $this->input->post('tglawalout');
				$tglakhirout = $this->input->post('tglakhirout');
				if(!(empty($tglawalin) or empty($tglakhirin)))
					$data[$object] = $this->scsi_model->getDataByTgl($tglawalin, $tglakhirin, $object);
				if(!(empty($tglawalout) or empty($tglakhirout)))
					$data[$object.'uji'] = $this->scsi_model->getDataByTgl($tglawalout, $tglakhirout, $object);
			
			}
			
			$n = count($data[$object]);
			$data = $this->scsi_model->getDifference($data,$object,$param, $abs);

			//data description
			list($data['desc']['min'], $data['desc']['max'], $data['desc']['mean']) = $this->scsi_model->DataDescription($data[$object], $param);
			list($data['desc']['mindiff'], $data['desc']['maxdiff'], $data['desc']['meandiff']) = $this->scsi_model->DataDescription($data[$object], 'diff');

			//himpunan fuzzy
			// $data['desc']['umin'] = round($data['desc']['min']/10, 0, PHP_ROUND_HALF_UP)*10;
			// $data['desc']['umax'] = round($data['desc']['max']/10, 0, PHP_ROUND_HALF_UP)*10;
			// $data['desc']['panjanginterval'] = 20;

			list($data['desc']['vmin'], $data['desc']['vmax'], $data['desc']['length']) = $this->scsi_model->getMinMaxInterval($data['desc']['min'.$variation],$data['desc']['max'.$variation], $basis, 6,$panjang);

			//semesta
			$data['universe'] = $this->scsi_model->getUniverse($data['desc']['vmin'], $data['desc']['vmax'], $data['desc']['length']);

			//menentukan fuzzy set dan fuzzy logic relationship
			$fuzzy = $this->scsi_model->getFuzzySet($data[$object], $data['universe'], $param);
			foreach ($data[$object] as $k => $value) {
				$data[$object][$k]['fs'] = $fuzzy[$k]['fs'];
				$data[$object][$k]['flr'] = $fuzzy[$k]['flr'];
			}
			/*echo "<pre> min ";print_r($data['desc']['vmin']);echo "</pre>";
			echo "<pre> max ";print_r($data['desc']['vmax']);echo "</pre>";
			echo "<pre> mean ";print_r($data['desc']['mean']);echo "</pre>";
			echo "<pre> min diff ";print_r($data['desc']['mindiff']);echo "</pre>";
			echo "<pre> max diff ";print_r($data['desc']['maxdiff']);echo "</pre>";
			echo "<pre> mean diff ";print_r($data['desc']['meandiff']);echo "</pre>";*/

			//menentukan fuzzy logic relationship group
			list($flr, $flrg) = $this->scsi_model->getFLRG($data[$object]);
			uksort($flrg,"strnatcmp");
			foreach ($flrg as $i => $row) {
				$data['flrg'][$i] = implode(', ',$row);
			}

			$data['rule'] = $this->scsi_model->getFuzzyRule($flrg);
			$data['forecast'] = $this->scsi_model->Forecast($data[$object], $flrg, $data['universe']);
			
			if(!empty($data[$object.'uji']))
				$data['forecastuji'] = $this->scsi_model->Forecast($data[$object.'uji'], $flrg, $data['universe'], $data[$object][$n-1]['fs']);

			$this->load->view('templates/header');
			$this->load->view('scsi/forecast',$data);
			$this->load->view('templates/footer');
		}
	}
?>