<?php
	class SCSIuji extends CI_Controller{

		public function index(){
			$data['title'] = 'Shanghai Compound Stock Index';

			$data['scsi'] = $this->scsiuji_modeluji->getData();
			$data['a_tahun'] = array('' => '-- Pilih Tahun --')+$this->scsiuji_modeluji->ListTahun();

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

			$data['scsi'] = $this->scsiuji_model->getData($thnawal, $thnakhir);
			$data['a_tahun'] = array('' => '-- Pilih Tahun --')+$this->scsiuji_model->ListTahun();

			$this->load->view('templates/header');
			$this->load->view('pages',$data);
			$this->load->view('templates/footer');

		}

		public function forecast(){
			$data['title'] = 'Shanghai Compound Stock Index';

			$period = $this->input->post('period');

			if($period == 'tahun'){
				$thnawal = $this->input->post('thnawal');
				$thnakhir = $this->input->post('thnakhir');
				$data['scsi'] = $this->scsiuji_model->getData($thnawal, $thnakhir);
			} else if ($period == 'tanggal'){
				$tglawalin = $this->input->post('tglawalin');
				$tglakhirin = $this->input->post('tglakhirin');
				$tglawalout = $this->input->post('tglawalout');
				$tglakhirout = $this->input->post('tglakhirout');
				if(!(empty($tglawalin) or empty($tglakhirin)))
					$data['scsi'] = $this->scsiuji_model->getDataByTgl($tglawalin, $tglakhirin);
				if(!(empty($tglawalout) or empty($tglakhirout)))
					$data['scsiuji'] = $this->scsiuji_model->getDataByTgl($tglawalout, $tglakhirout);
			}
			
			$n = count($data['scsi']);
			$totaldiff = 0;
			for ($i=0;$i<=$n-1;$i++) {
				if($i==0 or $i==$n-1)
					$data['scsi'][$i]['diff'] = 0;
				else
					$data['scsi'][$i]['diff'] = round(abs($data['scsi'][$i+1]['indexharga']-$data['scsi'][$i]['indexharga']),3);

				$totaldiff += $data['scsi'][$i]['diff'];
			}

			//data description
			list($data['min'], $data['max'], $data['mean']) = $this->scsiuji_model->DataDescription($data['scsi'], 'indexharga');
			list($data['mindiff'], $data['maxdiff'], $data['meandiff']) = $this->scsiuji_model->DataDescription($data['scsi'], 'diff');

			//himpunan fuzzy
			$data['umin'] = round($data['min']/10, 0, PHP_ROUND_HALF_UP)*10;
			$data['umax'] = round($data['max']/10, 0, PHP_ROUND_HALF_UP)*10;
			$data['panjanginterval'] = 20;

			//semesta
			$data['universe'] = $this->scsiuji_model->getUniverse($data['umin'], $data['umax'], $data['panjanginterval']);

			//menentun fuzzy set dan fuzzy logic relationship
			$fuzzy = $this->scsiuji_model->getFuzzySet($data['scsi'], $data['universe']);
			foreach ($data['scsi'] as $k => $value) {
				$data['scsi'][$k]['fs'] = $fuzzy[$k]['fs'];
				$data['scsi'][$k]['flr'] = $fuzzy[$k]['flr'];
			}

			//menentukan fuzzy logic relationship group
			list($flr, $flrg) = $this->scsiuji_model->getFLRG($data['scsi']);
			uksort($flrg,"strnatcmp");
			foreach ($flrg as $i => $row) {
				$data['flrg'][$i] = implode(', ',$row);
			}

			$data['rule'] = $this->scsiuji_model->getFuzzyRule($flrg);

			$data['forecast'] = $this->scsiuji_model->Forecast($data['scsi'], $flrg, $data['universe']);
			if(!empty($data['scsiuji']))
				$data['forecastuji'] = $this->scsiuji_model->Forecast($data['scsiuji'], $flrg, $data['universe'], $data['scsi'][$n-1]['fs']);

			$this->load->view('templates/header');
			$this->load->view('scsiuji/forecast',$data);
			$this->load->view('templates/footer');
		}
	}
?>