<?php
	class Alabamauji extends CI_Controller{

		public function index(){
			$data['title'] = 'Shanghai Compound Stock Index';

			$data['alabama'] = $this->alabamauji_modeluji->getData();
			$data['a_tahun'] = array('' => '-- Pilih Tahun --')+$this->alabamauji_modeluji->ListTahun();

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

			$data['alabama'] = $this->alabamauji_model->getData($thnawal, $thnakhir);
			$data['a_tahun'] = array('' => '-- Pilih Tahun --')+$this->alabamauji_model->ListTahun();

			$this->load->view('templates/header');
			$this->load->view('pages',$data);
			$this->load->view('templates/footer');

		}

		public function forecast(){
			$data['title'] = 'Alabama University';

			$period = $this->input->post('period');

			if($period == 'tahun'){
				$thnawal = $this->input->post('thnawal');
				$thnakhir = $this->input->post('thnakhir');
				$data['alabama'] = $this->alabamauji_model->getData($thnawal, $thnakhir);
			} else if ($period == 'tanggal'){
				$tglawalin = $this->input->post('tglawalin');
				$tglakhirin = $this->input->post('tglakhirin');
				$tglawalout = $this->input->post('tglawalout');
				$tglakhirout = $this->input->post('tglakhirout');
				if(!(empty($tglawalin) or empty($tglakhirin)))
					$data['alabama'] = $this->alabamauji_model->getDataByTgl($tglawalin, $tglakhirin);
				if(!(empty($tglawalout) or empty($tglakhirout)))
					$data['alabamauji'] = $this->alabamauji_model->getDataByTgl($tglawalout, $tglakhirout);
			}
			
			$n = count($data['alabama']);
			$totaldiff = 0;
			for ($i=0;$i<=$n-1;$i++) {
				if($i==0 or $i==$n-1)
					$data['alabama'][$i]['diff'] = 0;
				else
					$data['alabama'][$i]['diff'] = round(abs($data['alabama'][$i+1]['jumlah']-$data['alabama'][$i]['jumlah']),3);

				$totaldiff += $data['alabama'][$i]['diff'];
			}

			//data description
			list($data['min'], $data['max'], $data['mean']) = $this->alabamauji_model->DataDescription($data['alabama'], 'jumlah');
			list($data['mindiff'], $data['maxdiff'], $data['meandiff']) = $this->alabamauji_model->DataDescription($data['alabama'], 'diff');

			//himpunan fuzzy
			$data['umin'] = round($data['min']/10, 0, PHP_ROUND_HALF_UP)*10;
			$data['umax'] = round($data['max']/10, 0, PHP_ROUND_HALF_UP)*10;
			$data['panjanginterval'] = 20;

			//semesta
			$data['universe'] = $this->alabamauji_model->getUniverse($data['umin'], $data['umax'], $data['panjanginterval']);

			//menentun fuzzy set dan fuzzy logic relationship
			$fuzzy = $this->alabamauji_model->getFuzzySet($data['alabama'], $data['universe']);
			foreach ($data['alabama'] as $k => $value) {
				$data['alabama'][$k]['fs'] = $fuzzy[$k]['fs'];
				$data['alabama'][$k]['flr'] = $fuzzy[$k]['flr'];
			}

			//menentukan fuzzy logic relationship group
			list($flr, $flrg) = $this->alabamauji_model->getFLRG($data['alabama']);
			uksort($flrg,"strnatcmp");
			foreach ($flrg as $i => $row) {
				$data['flrg'][$i] = implode(', ',$row);
			}

			$data['rule'] = $this->alabamauji_model->getFuzzyRule($flrg);

			$data['forecast'] = $this->alabamauji_model->Forecast($data['alabama'], $flrg, $data['universe']);
			if(!empty($data['alabamauji']))
				$data['forecastuji'] = $this->alabamauji_model->Forecast($data['alabamauji'], $flrg, $data['universe'], $data['alabama'][$n-1]['fs']);

			$this->load->view('templates/header');
			$this->load->view('alabamauji/forecast',$data);
			$this->load->view('templates/footer');
		}
	}
?>