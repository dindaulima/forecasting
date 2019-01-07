<?php
	class Pages extends CI_Controller{
		public function view($page = 'home'){
			if(!file_exists(APPPATH.'views/pages/'.$page.'.php')){
				show_404();
			}

			// $thnawal = $this->input->post('thnawal');
			// $thnakhir = $this->input->post('thnakhir');
			if($page=='home')
				$object = 'scsi';
			else
				$object = $page;


			$data['title'] = ucfirst($page);

			if($object=='sevimapay'){

				$data['sevimapay'] = $this->sevimapay_model->MappingData($this->sevimapay_model->getData($object), 'periode', 'kelas');
				$data['kelas'] = $this->sevimapay_model->SortKelas($this->sevimapay_model->DistinctField('kelas'));
				$data['a_periode'] = $this->sevimapay_model->getCombo($this->sevimapay_model->DistinctField('periode'), 'periode');
			} else {
				$data['scsi'] = $this->scsi_model->getData($object);
				$data['a_tahun'] = $this->scsi_model->ListTahun();
			}
			// echo "<pre>"; print_r($data['sevimapay']); echo "</pre>";

			$this->load->view('templates/header');
			$this->load->view('pages/'.$page,$data);
			$this->load->view('templates/footer');
			
		}

		public function forecast($page = 'home'){

			$thnawal = $this->input->post('thnawal');
			$thnakhir = $this->input->post('thnakhir');

			$data['title'] = ucfirst($page);
			$data['scsi'] = $this->scsi_model->getData($thnawal, $thnakhir);
			$data['a_tahun'] = $this->scsi_model->ListTahun();

			$this->load->view('templates/header');
			$this->load->view('scsi/'.$page,$data);
			$this->load->view('templates/footer');
		}

	}
?>