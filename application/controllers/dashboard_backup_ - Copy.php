<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->akses->cek_akses();
	}

	public function keluar()
	{
		$this->session->sess_destroy();
		redirect('','refresh');
	}
	public function home()
	{
		$dataDas['title'] = "Dashboard";
		$dataDas['desk'] = "App Retail";
		$dataDas['page'] = "home";
		$this->load->view('view_awal', $dataDas, FALSE);

	}

	/*USER MANAJEMEN*/

	public function dataUser()
	{
		if ($this->input->post()) {
			$this->load->model('datatable');
		// if ($this->input->post()) {
			$table = "sada_user";
			$column = array('nik','nama','akses');
			$odb = array("id_user"=>"desc");
			$list = $this->datatable->get_datatables($table." WHERE status='Y' ",$column,$odb);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $datatable) {
				$no++;
				$row = array();
				$row[] = $datatable->nik;
				$row[] = $datatable->nama;

				if ($datatable->stay == "Y") {
        			$stay = "Stay";
        			$x="";
        		} elseif ($datatable->stay == "N") {
        			$stay = "Mobile";
        		}else {
        			$stay = "??";
        			$x="";
        		}
				if ($datatable->akses == 0) {
                   $row[] = '<center><span class="label label-sm label-success"> TL </span></center>';
                }elseif ($datatable->akses == 1) {
                    $row[] = '<center><span class="label label-sm label-info"> SPG ( '. $stay.' )</span> <a  class="label label-sm label-danger" id="showToko" href="#page'.$datatable->id_user.'"><small>Show Toko</small></a></center>';
                }
                if ($datatable->akses == 2) {
                    $row[] = '<center><span class="label label-sm label-primary"> Admin </span></center>';
                }else{
                    // $row[] = '<center><span class="label label-sm label-danger"> ? </span></center>';
                }


			  	$row[] = '<div class="btn-group" >
	                        <button  class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
	                            <i class="fa fa-angle-down"></i>
	                        </button>
	                        <ul class="dropdown-menu" role="menu">
	                            <li>
	                                 '.anchor('users/edit/'.$datatable->id_user, '<i class="icon-pencil"></i>Edit', 'attributes').'
	                            </li>
	                            <li>
	                                  '.anchor('users/delete/'.$datatable->id_user, '<i class="icon-trash"></i>Delete', 'onclick = "if (! confirm(\'Apakah Anda Yakin Untuk Menghapus '.$datatable->nama.'?\')) { return false; }"').'
	                            </li>
	                        </ul>
	                    </div>';

				$data[] = $row;
			}

			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->datatable->count_all($table,$column,$odb),
							"recordsFiltered" => $this->datatable->count_filtered($table,$column,$odb),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		} else {
			$dataDas['title'] = "Data User";
			$dataDas['desk'] = "App Retail";
			$dataDas['page'] = "users/data";

			$dataDas['css'] = $this->sada->CssdataTable();
			$dataDas['js']	= $this->sada->JsdataTable();

			$dataDas['js'][]	= "assets/custom/DataUsers.js";

	    	$this->db->order_by("status","desc");
			$dataDas['query'] = $this->sada->getUser();
			$this->load->view('view_awal', $dataDas, FALSE);
		}

	}
	public function deleteDataUser()
	{
		$idParam = $this->uri->segment(3);
		$data['status'] = "N";

		if ($this->sada->deletUser($idParam,$data)) {
			$this->session->set_flashdata('msg', 'User deleted');
			redirect('', 'refresh');
		}
	}
	public function getTokoo()
	{
		if ($this->input->post()) {
			$id = $this->input->post("id");
			$select = $this->db->select("id_toko")->where("id_user",$id)->get("sada_tokoinuser")->row();
			$exp = explode(",", $select->id_toko);
			foreach ($exp as $key => $value) {
				$toko = $this->db->select("store_id,nama")->where("id_toko",$value)->get("sada_toko");
				foreach ($toko->result() as $key => $val_toko) {
					$data[] = $val_toko;
				}
			}
			echo json_encode($data,JSON_PRETTY_PRINT);
		} else {
			show_404();
		}


	}
	public function AdddataUser()
	{
		$this->load->library('form_validation');
		// $zz = array();
		if ($this->input->post()) {
			$dataInsert["nik"] =  htmlentities($this->input->post("nik",TRUE), ENT_QUOTES, 'utf-8');
			$dataInsert["nama"] =  htmlentities($this->input->post("nama",TRUE), ENT_QUOTES, 'utf-8');
			$dataInsert["password"] =  htmlentities(md5($this->input->post("password",TRUE)), ENT_QUOTES, 'utf-8');
			$dataInsert["akses"] =  htmlentities($this->input->post("akses",TRUE), ENT_QUOTES, 'utf-8');
			
			$this->form_validation->set_error_delimiters('<span class="error">', '</span>');
			$this->form_validation->set_rules('nik', 'Nik', 'required');
			$this->form_validation->set_rules('nama', 'Nama', 'required');
			$this->form_validation->set_rules('password', 'password', 'required');
			$this->form_validation->set_rules('akses', 'Akses', 'required');
			if ($this->form_validation->run() == FALSE)
			{
				$dataDas['title'] = "Tambah Data User";
				$dataDas['desk'] = "App Retail";
				$dataDas['page'] = "users/Adddata";
				$dataDas['query_toko'] = $this->db->get_where('sada_toko',array('status'=>"Y"))->result();

				// $dataDas['css'] = $this->sada->CssdataTable();
				// $dataDas['js']	= $this->sada->JsdataTable();

				 $dataDas['js'][]	= "assets/custom/addUser.js";

				$this->load->view('view_awal', $dataDas, FALSE);
			}
			else{
				if ($dataInsert['akses']==1) {

					$dataInsert["stay"] =  $this->input->post("stay",TRUE);
					$loop 				=  $this->input->post("toko",TRUE);
					foreach ($loop as $key => $value) {
						$exp = explode("|", $value);
						$zz[] = $value;
					}
				}
				if ($dataInsert['akses']==0) {
					// $dataInsert['cabang'] = $this->input->post("cabang",TRUE);
					$dataInsert["stay"] =  "N";
				}

				if ($this->sada->insertUser($dataInsert)) {
					$id=$this->db->select('id_user')->order_by('id_user',"desc")->limit(1)->get('sada_user')->row();

					if ($dataInsert['akses']==0) {
						$insertTL['id_user'] = $id->id_user;
						$insertTL['id_kota'] = $this->input->post("kota",TRUE);
						if ($this->db->insert("sada_tl_in_kota",$insertTL)) {
							$this->session->set_flashdata('msg', 'User added');
							redirect('', 'refresh');
						}
						else{
							$this->session->set_flashdata('msg', 'User not added');
							redirect('', 'refresh');
						}
					}
					if ($dataInsert['akses']==1) {
						$inserttInsus['id_user'] = $id->id_user;
						$inserttInsus['id_toko'] = implode(",", $zz);
						$inserttInsus['status'] = "Y";
						if ($this->db->insert("sada_tokoinuser",$inserttInsus)) {
							$this->session->set_flashdata('msg', 'User added');
							redirect('', 'refresh');
						}
					}
						$this->session->set_flashdata('msg', 'User added');
						redirect('', 'refresh');
				}
				else{
					echo "gagal";
				}
			}
		} else {
			$dataDas['title'] = "Tambah Data User";
			$dataDas['desk'] = "App Retail";
			$dataDas['page'] = "users/Adddata";
			$dataDas['query_toko'] = $this->db->get_where('sada_toko',array('status'=>"Y"))->result();

			// $dataDas['css'] = $this->sada->CssdataTable();
			// $dataDas['js']	= $this->sada->JsdataTable();

			 $dataDas['js'][]	= "assets/custom/addUser.js";

			$this->load->view('view_awal', $dataDas, FALSE);
		}
	}
	public function EditdataUser()
	{
		$dataDas['title'] = "Edit Data User";
		$dataDas['desk'] = "";
		$dataDas['page'] = "users/edit_user";
		$dataDas['paramId'] = $this->uri->segment(3);
		$dataDas['loopEditUser'] = $this->sada->editUser($dataDas['paramId']);
		$dataDas['query_toko'] = $this->db->get_where('sada_toko',array('status'=>"Y"))->result();
		$dataDas['js'][]	= "assets/custom/addUser.js";

		if ($dataDas['loopEditUser']->akses == 1) {
			$qry = $this->db->select('id_user,id_toko')->where('id_user',$dataDas['paramId'])->get('sada_tokoinuser')->row();
			$dataDas['id_toko'] = explode(",", $qry->id_toko);
		}
		elseif ($dataDas['loopEditUser']->akses == 0) {
			$dataDas['id_toko'] = null;
			$qry = $this->db->select('id_user,id_kota')->where('id_user',$dataDas['paramId'])->get('sada_tl_in_kota')->row();
			$qry2 = $this->db->select('id_cabang,id_kota,nama_kota')->where('id_kota',$qry->id_kota)->get('sada_kota')->row();
			$dataDas['id_kotas'] = $qry->id_kota;
			$data = $this->db->select('id_cabang,nama')->where('id_cabang',$qry2->id_cabang)->get('sada_cabang')->row();
				$dataDas['id_cabang'] = $data->id_cabang;
			}
		$this->load->view('view_awal', $dataDas, FALSE);
	}
	public function UpdateEditUser()
	{
		if ($this->input->post()) {
			$id_user = htmlentities($this->input->post("id_us",TRUE), ENT_QUOTES, 'utf-8');
			$dataUpdate["nik"] =  htmlentities($this->input->post("nik",TRUE), ENT_QUOTES, 'utf-8');
			$dataUpdate["nama"] =  htmlentities($this->input->post("nama",TRUE), ENT_QUOTES, 'utf-8');
			$dataUpdate["password"] =  htmlentities(md5($this->input->post("password",TRUE)), ENT_QUOTES, 'utf-8');
			$dataUpdate["akses"] =  htmlentities($this->input->post("akses",TRUE), ENT_QUOTES, 'utf-8');

			if ($dataUpdate['akses']==1) {

				$dataUpdate["stay"] =  $this->input->post("stay",TRUE);
				$loop 				=  $this->input->post("toko",TRUE);
				foreach ($loop as $key => $value) {
					$zz[] = $value;
				}
			}
			if ($dataUpdate['akses']==0) {
				// $dataInsert['cabang'] = $this->input->post("cabang",TRUE);
				$dataUpdate["stay"] =  "N";
			}

			if ($this->sada->updateEditUser($dataUpdate,$id_user)) {
				if ($dataUpdate['akses'] == 0) {
					$updateTL['id_user'] = htmlentities($this->input->post("id_us",TRUE), ENT_QUOTES, 'utf-8');
					$updateTL['id_kota'] = $this->input->post("kota",TRUE);
					if (count($this->db->select("id_user")->where("id_user",$updateTL['id_user'])->get("sada_tl_in_kota")->row()) == 0) {
						if ($this->db->insert("sada_tl_in_kota",$updateTL)) {
							if (count($this->db->select("id_user")->where("id_user",$updateTL['id_user'])->get("sada_tokoinuser")->row()) > 0) {
								if ($this->db->delete("sada_tokoinuser",array("id_user"=>htmlentities($this->input->post("id_us",TRUE), ENT_QUOTES, 'utf-8')))) {
									$this->session->set_flashdata('msg', 'User Success Updated');
									redirect('', 'refresh');
								}
							}
						}
					}
					else{
						if ($this->sada->updateEditTlinKota($updateTL,$id_user)) {
							$this->session->set_flashdata('msg', 'User Success Updated');
							redirect('', 'refresh');
						}
					}
				}
				elseif ($dataUpdate['akses'] == 1) {
					$updateTus['id_user'] = htmlentities($this->input->post("id_us",TRUE), ENT_QUOTES, 'utf-8');
					$updateTus['id_toko'] = implode(",", $zz);
					$updateTus['status'] = "Y";
					if (count($this->db->select("id_user")->where("id_user",$updateTus['id_user'])->get("sada_tokoinuser")->row()) == 0) {
						echo "ngga ada";
						if ($this->db->insert("sada_tokoinuser",$updateTus)) {
							if (count($this->db->select("id_user")->where("id_user",$updateTus['id_user'])->get("sada_tl_in_kota")->row()) > 0) {
								if ($this->db->delete("sada_tl_in_kota",array("id_user"=>htmlentities($this->input->post("id_us",TRUE), ENT_QUOTES, 'utf-8')))) {
									$this->session->set_flashdata('msg', 'User Success Updated');
									redirect('', 'refresh');
								}
							}
						}
					}
					else{
						if ($this->sada->updateEditTlin_user($updateTus,$id_user)) {
							$this->session->set_flashdata('msg', 'User Success Updated');
							redirect('', 'refresh');
						}
					}
				}
			}
			else{
			}
		}
	}
	public function assignStore($id)
	{
			$dataDas['dataUser'] = $this->db->get_where('sada_user',array('id_user'=> $id))->first_row();
			$dataDas['title'] = "Tugaskan BA pada Store";
			$dataDas['desk'] = "App Retail";
			$dataDas['page'] = "users/AssignStore";
			$dataDas['query_toko'] = $this->db->get_where('sada_toko',array('status'=>"Y"))->result();

			// $dataDas['css'] = $this->sada->CssdataTable();
			// $dataDas['js']	= $this->sada->JsdataTable();

			 // $dataDas['js'][]	= "assets/custom/addUser.js";

			$this->load->view('view_awal', $dataDas, FALSE);
	}


	public function addAssignStore($id)
	{
		$stores = $this->input->post('toko');
		$data = [];
		foreach ($stores as $toko) {
			$data[] = [
				'id_toko' => $toko,
				'id_user' => $id
			];
		}
		var_dump($data);
		$this->sada->insertAssignStore($data);
		echo "testing";
	}


	/*END USER MANAJEMEN*/

	/* SKU */
	public function dataSku()
	{
		if ($this->input->post()) {
			$this->load->model('datatable');
		// if ($this->input->post()) {
			$table = "sada_produk";
			$column = array('id_produk','id_store','id_kategori','nama_produk');
			$odb = array("id_produk"=>"desc");
			$list = $this->datatable->get_datatables($table,$column,$odb);
			$data = array();
  			$no = 1;
			foreach ($list as $datatable) {
				$row = array();
				$row[] = $no++;
				$select1 = $this->db->select('nama')->from('sada_toko')->where('id_toko',$datatable->id_store)->get()->row();
				$row[] = $select1->nama;
				$select = $this->db->select('nama')->from('sada_kategori')->where('id',$datatable->id_kategori)->get()->row();
				$row[] = $select->nama;
				$row[] = $datatable->nama_produk;
			  	$row[] = '<div class="btn-group" >
	                        <button  class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
	                            <i class="fa fa-angle-down"></i>
	                        </button>
	                        <ul class="dropdown-menu" role="menu">
	                            <li>
	                                 '.anchor('sku/edit/'.$datatable->id_produk, '<i class="icon-pencil"></i>Edit', 'attributes').'
	                            </li>
	                            <li>
	                                  '.anchor('sku/delete/'.$datatable->id_produk, '<i class="icon-trash"></i>Delete', 'onclick = "if (! confirm(\'Apakah Anda Yakin Untuk Menghapus '.$datatable->nama_produk.'? Data Yang Sudah Di Hapus Tidak Bisa Di Kembalikan\')) { return false; }"').'
	                            </li>
	                            
	                        </ul>
	                    </div>';

				$data[] = $row;
			}

			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->datatable->count_all($table,$column,$odb),
							"recordsFiltered" => $this->datatable->count_filtered($table,$column,$odb),
							"data" => $data,
					);
			echo json_encode($output);
		} else {
			$dataDas['title'] = "Data SKU";
			$dataDas['desk'] = "App Retail";
			$dataDas['page'] = "sku/data_sku";

			$dataDas['css'] = $this->sada->CssdataTable();
			$dataDas['js']	= $this->sada->JsdataTable();

			$dataDas['js'][]	= "assets/custom/dataProduk.js";
	    	$this->db->order_by("id_produk","desc");
			$dataDas['query'] = $this->sada->getProduk();

			$this->load->view('view_awal', $dataDas, FALSE);
		}
	}

	public function AdddataSku()
	{
		$dataDas['title'] = "Tambah Data SKU";
		$dataDas['desk'] = "";
		$dataDas['page'] = "sku/add_sku";

		$this->load->view('view_awal', $dataDas, FALSE);
	}

	public function insertDataSku()
	{
		$this->load->library('form_validation');
		//$inCabang = htmlentities($this->input->post("nama-cabang",TRUE), ENT_QUOTES, 'utf-8');
		$cabangName = htmlentities($this->input->post("nama-cabang",TRUE), ENT_QUOTES, 'utf-8');
		$skuName = htmlentities($this->input->post("nama-sku",TRUE), ENT_QUOTES, 'utf-8');
		$skuCategory = htmlentities($this->input->post("kategori-sku",TRUE), ENT_QUOTES, 'utf-8');
		$skuStore = htmlentities($this->input->post("kategori-store",TRUE), ENT_QUOTES, 'utf-8');
		$idCategory = $this->db->get_where('sada_kategori',['nama' => $skuCategory])->first_row();
		
			$this->form_validation->set_error_delimiters('<span class="error">', '</span>');
			$this->form_validation->set_rules('nama-sku', 'Nama Sku', 'required');
			$this->form_validation->set_rules('kategori-sku', 'Kategori Sku', 'required');
			$this->form_validation->set_rules('kategori-store', 'Kategori Store', 'required');
		
		//$idCabang = $this->db->get_where('sada_cabang',['nama' => $inCabang])->first_row();
		if ($this->form_validation->run() == FALSE)
		{
			$this->AdddataSku();
		}
		else{
			$idStore = $this->db->get_where('sada_toko',['nama' => $skuStore])->first_row();
			$this->sada->addNewSku([
					'nama_produk' => $skuName,
					'id_kategori' => $idCategory->id,
					'id_store' 	=> $idStore->id_toko
				]);
			$this->session->set_flashdata('msg', 'Sku product added');
			redirect('', 'refresh');
		}
	}

	public function EditdataSku()
	{
		$dataDas['title'] = "Edit Data SKU";
		$dataDas['desk'] = "";
		$dataDas['page'] = "sku/edit_sku";
		$dataDas['paramId'] = $this->uri->segment(3);
		$dataDas['loopEditSku'] = $this->sada->editSku($dataDas['paramId']);
		$dataDas['s'] = var_dump($dataDas['loopEditSku']);
		$this->load->view('view_awal', $dataDas, FALSE);
	}
	public function updateDataSku()
	{
		$skuId = htmlentities($this->input->post("id-sku",TRUE), ENT_QUOTES, 'utf-8');

		$skuName = htmlentities($this->input->post("nama-sku",TRUE), ENT_QUOTES, 'utf-8');
		$skuCategory = htmlentities($this->input->post("kategori-sku",TRUE), ENT_QUOTES, 'utf-8');
		$skuStore = htmlentities($this->input->post("kategori-store",TRUE), ENT_QUOTES, 'utf-8');
		$idCategory = $this->db->get_where('sada_kategori',['nama' => $skuCategory])->first_row();
		$idStore = $this->db->get_where('sada_toko',['nama' => $skuStore])->first_row();


		$field = array('id_store' => $idStore->id_toko , 'id_kategori' => $idCategory->id, 'nama_produk' => $skuName);

		$this->sada->updateSku($skuId,$field);
		$this->session->set_flashdata('msg','Sku product success edited');
		redirect('', 'refresh');
	}

	public function deleteDataSku()
	{
		$skuId = $this->uri->segment(3);
		$this->db->delete('sada_produk',array('id_produk'=>$skuId));
		$this->session->set_flashdata('msg','SKU product success deleted');
		redirect('', 'refresh');
	}
	/* END SKU */

	//<CABANG>
	public function dataCabang()
	{if ($this->input->post()) {
			$this->load->model('datatable');
		// if ($this->input->post()) {
			$table 	= "sada_cabang";
			$column = array('id_cabang','nama');
			$odb 	= array("id_cabang"=>"asc");
			$list 	= $this->datatable->get_datatables($table,$column,$odb);
			$data 	= array();
  			$no 	= 1;
			foreach ($list as $datatable) {
				$row 		= array();
				$row[] 		= $no++;
				$row[] 		= $datatable->nama;
			  	$row[] 		= '<div class="btn-group" >
			                        <button  class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
			                            <i class="fa fa-angle-down"></i>
			                        </button>
			                        <ul class="dropdown-menu" role="menu">
			                            <li>
			                                 '.anchor('cabang/edit/'.$datatable->id_cabang, '<i class="icon-pencil"></i>Edit', 'attributes').'
			                            </li>
			                            <li>
			                                  '.anchor('cabang/delete/'.$datatable->id_cabang, '<i class="icon-trash"></i>Delete', 'onclick = "if (! confirm(\'Apakah Anda Yakin Untuk Menghapus '.$datatable->nama.'? Data Yang Sudah Di Hapus Tidak Bisa Di Kembalikan\')) { return false; }"').'
			                            </li>
			                        </ul>
			                    </div>';
				$data[] = $row;
			}	
			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->datatable->count_all($table,$column,$odb),
							"recordsFiltered" => $this->datatable->count_filtered($table,$column,$odb),
							"data" => $data,
					);
			echo json_encode($output);
		} else {
			$dataDas['title'] 	= "Data Cabang";
			$dataDas['desk']  	= "";
			$dataDas['page']  	= "cabang/data_cabang";

			$dataDas['css'] 	= $this->sada->CssdataTable();
			$dataDas['js']		= $this->sada->JsdataTable();

			$dataDas['js'][]	= "assets/custom/dataCabang.js";
	    	$dataDas['query'] 	= $this->sada->getCabang();

			$this->load->view('view_awal', $dataDas, FALSE);
		}
	}

	public function addDataCabang()
	{
		$dataDas['title'] = "Tambah Cabang";
		$dataDas['desk'] = "";
		$dataDas['page'] = "cabang/add_cabang";

		$this->db->where('status','Y');
		$dataDas['q_cabang'] = $this->db->get('sada_cabang');

		$this->load->view('view_awal', $dataDas, FALSE);
	}

	public function insertDataCabang()
	{
		$this->load->library('form_validation');
		//$inCabang = htmlentities($this->input->post("nama-cabang",TRUE), ENT_QUOTES, 'utf-8');
		$cabangName = htmlentities($this->input->post("nama-cabang",TRUE), ENT_QUOTES, 'utf-8');
		
			$this->form_validation->set_error_delimiters('<span class="error">', '</span>');
			$this->form_validation->set_rules('nama-cabang', 'Nama Cabang', 'required');
		//$idCabang = $this->db->get_where('sada_cabang',['nama' => $inCabang])->first_row();
		if ($this->form_validation->run() == FALSE)
		{
			$this->addDataCabang();
		}
		else{
			$this->sada->addNewCabang([
				//'id_cabang' => $idCabang->id_cabang,
				'nama' => $cabangName
			]);
			$this->session->set_flashdata('msg', 'Cabang '.$cabangName.' has been ADDED');
			redirect('', 'refresh');
		}
	}

	public function editDataCabang()
	{
		$dataDas['title'] = "Edit Cabang";
		$dataDas['desk'] = "";
		$dataDas['page'] = "cabang/edit_cabang";

		$dataDas['paramId'] = $this->uri->segment(3);
		$dataDas['loopEditCabang'] = $this->sada->editCabang($dataDas['paramId']);
		$dataDas['s'] = var_dump($dataDas['loopEditCabang']);

		$this->load->view('view_awal', $dataDas, FALSE);
	}

	public function updateDataCabang()
	{
		$cabangId = htmlentities($this->input->post("id-cabang",TRUE), ENT_QUOTES, 'utf-8');
		$cabangName = htmlentities($this->input->post("nama-cabang",TRUE), ENT_QUOTES, 'utf-8');

		$field = array('nama' => $cabangName);

		$this->sada->updateCabang($cabangId,$field);
		$this->session->set_flashdata('msg','Cabang '.$cabangName.' has been EDITED');
		redirect('', 'refresh');
	}

	public function deleteDataCabang()
	{
		$cabangId = $this->uri->segment(3);
		$this->db->delete('sada_cabang',array('id_cabang'=>$cabangId));
		$this->session->set_flashdata('msg','Cabang has been DELETED');
		redirect('', 'refresh');
	}
	//<CABANG/>

	//<KOTA>
	public function dataKota()
	{if ($this->input->post()) {
			$this->load->model('datatable');
		// if ($this->input->post()) {
			$table 	= "sada_kota";
			$column = array('id_kota','nama_kota');
			$odb 	= array("id_kota"=>"asc");
			$list 	= $this->datatable->get_datatables($table,$column,$odb);
			$data 	= array();
  			$no 	= 1;
			foreach ($list as $datatable) {
				$row 		= array();
				$row[] 		= $no++;
				$row[] 		= $datatable->nama_kota;
			  	$row[] 		= '<div class="btn-group" >
			                        <button  class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
			                            <i class="fa fa-angle-down"></i>
			                        </button>
			                        <ul class="dropdown-menu" role="menu">
			                            <li>
			                                 '.anchor('kota/edit/'.$datatable->id_kota, '<i class="icon-pencil"></i>Edit', 'attributes').'
			                            </li>
			                            <li>
			                                  '.anchor('kota/delete/'.$datatable->id_kota, '<i class="icon-trash"></i>Delete', 'onclick = "if (! confirm(\'Apakah Anda Yakin Untuk Menghapus '.$datatable->nama_kota.'? Data Yang Sudah Di Hapus Tidak Bisa Di Kembalikan\')) { return false; }"').'
			                            </li>
			                        </ul>
			                    </div>';
				$data[] = $row;
			}	

			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->datatable->count_all($table,$column,$odb),
							"recordsFiltered" => $this->datatable->count_filtered($table,$column,$odb),
							"data" => $data,
					);
			echo json_encode($output);
		} else {
			$dataDas['title'] = "Data Kota";
			$dataDas['desk'] = "Detail Kota";
			$dataDas['page'] = "kota/data_kota";

			$dataDas['css'] = $this->sada->CssdataTable();
			$dataDas['js']	= $this->sada->JsdataTable();
			$dataDas['css'][]  	= "assets/global/plugins/select2/css/select2-bootstrap.min.css";
			$dataDas['css'][]	= "assets/global/plugins/select2/css/select2.min.css";
			
			$dataDas['js'][]	= "assets/pages/scripts/components-select2.min.js";

		    // $dataDas['js'][] ="assets/custom/select2Filter.js";
			$dataDas['js'][]	= "assets/custom/dataKota.js";
			$dataDas['js'][]	= "assets/global/plugins/select2/js/select2.full.min.js";

	    	// $this->db->order_by("id_kota","asc");
			$dataDas['query'] = $this->sada->getKota();

			$this->load->view('view_awal', $dataDas, FALSE);
		}
	}

	public function addDataKota()
	{
		$dataDas['title'] = "Tambah Kota";
		$dataDas['desk'] = "";
		$dataDas['page'] = "kota/add_kota";

		$this->db->where('status','Y');
		$dataDas['q_cabang'] = $this->db->get('sada_cabang');

		$this->load->view('view_awal', $dataDas, FALSE);
	}

	public function insertDataKota()
	{
		$inCabang = htmlentities($this->input->post("nama-cabang",TRUE), ENT_QUOTES, 'utf-8');
		$kotaName = htmlentities($this->input->post("nama-kota",TRUE), ENT_QUOTES, 'utf-8');
		$idCabang = $this->db->get_where('sada_cabang',['nama' => $inCabang])->first_row();
		$this->sada->addNewKota([
			'id_cabang' => $idCabang->id_cabang,
			'nama_kota' => $kotaName
		]);
		$this->session->set_flashdata('msg', 'Kota '.$kotaName.' has been ADDED');
		redirect('', 'refresh');
	}

	public function editDataKota()
	{
		$dataDas['title'] = "Edit Kota";
		$dataDas['desk'] = "";
		$dataDas['page'] = "kota/edit_kota";

		$dataDas['paramId'] = $this->uri->segment(3);
		$dataDas['loopEditKota'] = $this->sada->editKota($dataDas['paramId']);
		$dataDas['s'] = var_dump($dataDas['loopEditKota']);

		$this->load->view('view_awal', $dataDas, FALSE);
	}

	public function updateDataKota()
	{
		$kotaId = htmlentities($this->input->post("id-kota",TRUE), ENT_QUOTES, 'utf-8');
		$data['nama_kota'] = htmlentities($this->input->post("nama-kota",TRUE), ENT_QUOTES, 'utf-8');
		$data['id_cabang'] = htmlentities($this->input->post("cabang",TRUE), ENT_QUOTES, 'utf-8');

		// $field = array('nama_kota' => $kotaName);

		$this->sada->updateKota($kotaId,$data);
		$this->session->set_flashdata('msg','Kota '.$data['nama_kota'].' has been EDITED');
		redirect('', 'refresh');
	}

	public function deleteDataKota()
	{
		$kotaId = $this->uri->segment(3);
		$this->db->delete('sada_kota',array('id_kota'=>$kotaId));
		$this->session->set_flashdata('msg','Kota has been DELETED');
		redirect('', 'refresh');
	}
	//<KOTA/>


	//<TOKO>

	public function dataToko()
	{
		if ($this->input->post()) {
			$this->load->model('datatable');
		// if ($this->input->post()) {
			$table = "sada_toko";
			$column = array('id_toko','store_id','nama');
			$odb = array("id_toko"=>"desc");
			$list = $this->datatable->get_datatables($table,$column,$odb);
			$data = array();
  			$no = 1;
			foreach ($list as $datatable) {
				$row = array();
				$row[] = $no++;
				$row[] = $datatable->store_id;
				$select1 = $this->db->select('nama_kota')->from('sada_kota')->where('id_kota',$datatable->id_kota)->get()->row();
				$row[] = $select1->nama_kota;
				$row[] = $datatable->nama;
			  	$row[] = '<div class="btn-group" >
	                        <button  class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
	                            <i class="fa fa-angle-down"></i>
	                        </button>
	                        <ul class="dropdown-menu" role="menu">
	                            <li>
	                                 '.anchor('toko/edit/'.$datatable->id_toko, '<i class="icon-pencil"></i>Edit', 'attributes').'
	                            </li>
	                            <li>
	                                  '.anchor('toko/delete/'.$datatable->id_toko, '<i class="icon-trash"></i>Delete', 'onclick = "if (! confirm(\'Apakah Anda Yakin Untuk Menghapus '.$datatable->nama.'? Data Yang Sudah Di Hapus Tidak Bisa Di Kembalikan\')) { return false; }"').'
	                            </li>
	                            <li>
	                                 '.anchor('toko/addAssignStore/'.$datatable->id_toko, '<i class="icon-pencil"></i>Assign Store', 'attributes').'
	                            </li>
	                        </ul>
	                    </div>';

				$data[] = $row;
			}

			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->datatable->count_all($table,$column,$odb),
							"recordsFiltered" => $this->datatable->count_filtered($table,$column,$odb),
							"data" => $data,
					);
			echo json_encode($output);
		} else {
			$dataDas['title'] = "Data Toko";
			$dataDas['desk'] = "App Retail";
			$dataDas['page'] = "toko/data";

			$dataDas['css'] = $this->sada->CssdataTable();
			$dataDas['js']	= $this->sada->JsdataTable();

	    	$this->db->order_by("status","desc");
	    	$dataDas['js'][]	= "assets/custom/DataToko.js";
			$dataDas['query'] = $this->db->get('sada_toko');
			$this->load->view('view_awal', $dataDas, FALSE);
		}
	}

	public function AdddataToko()
	{
		$dataDas['title'] = "Tambah Toko";
		$dataDas['desk'] = "App Retail";
		$dataDas['page'] = "toko/Adddata";
		$this->db->where('status','Y');
		$dataDas['q_kota'] = $this->db->get('sada_kota');

		$dataDas['css'][]  	= "assets/global/plugins/select2/css/select2-bootstrap.min.css";
		$dataDas['css'][]	= "assets/global/plugins/select2/css/select2.min.css";
		$dataDas['js'][]  = "assets/global/plugins/jquery-validation/js/jquery.validate.min.js";
		
		$dataDas['js'][]  = "assets/global/scripts/app.min.js";
	    $dataDas['js'][] ="assets/custom/addToko.js";
	    $dataDas['js'][] ="assets/custom/select2Filter.js";
		// $dataDas['js'][]	= "assets/pages/scripts/components-select2.min.js";
		$dataDas['js'][]	= "assets/global/plugins/select2/js/select2.full.min.js";
		
		$dataDas['js'][]  = "assets/pages/scripts/form-validation.min.js";
		$this->load->view('view_awal', $dataDas, FALSE);
	}
	public function InsertAdddataToko()
	{
		$data['store_id'] = htmlentities($this->input->post("store_id",TRUE), ENT_QUOTES, 'utf-8');
		$data['id_kota'] = htmlentities($this->input->post("kota",TRUE), ENT_QUOTES, 'utf-8');
		$data['nama'] = htmlentities($this->input->post("toko",TRUE), ENT_QUOTES, 'utf-8');
		if ($this->db->insert("sada_toko",$data)) {
			$this->session->set_flashdata('msg', 'Data '.$data['nama'].' has been ADDED');
			redirect('', 'refresh');
		}
	}
	public function editDataToko()
	{
		$dataDas['title'] = "Edit Toko";
		$dataDas['desk'] = "";
		$dataDas['page'] = "toko/edit_toko";

		$dataDas['paramId'] = $this->uri->segment(3);
		$dataDas['js'][]  = "assets/global/plugins/jquery-validation/js/jquery.validate.min.js";
		
		$dataDas['js'][]  = "assets/global/scripts/app.min.js";
	    $dataDas['js'][] ="assets/custom/editToko.js";
	    $dataDas['js'][] ="assets/custom/select2Filter.js";
		$dataDas['loopEditToko'] = $this->sada->editToko($dataDas['paramId']);
		$dataDas['s'] = var_dump($dataDas['loopEditToko']);

		$this->load->view('view_awal', $dataDas, FALSE);
	}
	public function updateDataToko()
	{
		$data['store_id'] = htmlentities($this->input->post("store_id",TRUE), ENT_QUOTES, 'utf-8');
		$data['id_kota'] = htmlentities($this->input->post("kota",TRUE), ENT_QUOTES, 'utf-8');
		$data['nama'] = htmlentities($this->input->post("toko",TRUE), ENT_QUOTES, 'utf-8');
		if ($this->sada->updateToko($this->uri->segment(3),$data)) {
			$this->session->set_flashdata('msg', 'Data '.$data['nama'].' has been Updated');
			redirect('', 'refresh');
		}
	}
public function deleteDataToko()
	{
		$id = $this->uri->segment(3);

		if ($this->db->delete("sada_toko",array('id_toko'=>$id))) {
			$this->session->set_flashdata('msg','Data successfully deleted');
			redirect('','refresh');
		}
		else{
			echo "Gagal delete";
		}

	}
	//<TOKO/>


	public function testData()
	{
		$dataDas['title'] = "TEST Data Toko";
		$dataDas['desk'] = "App Retail";
		$dataDas['page'] = "toko/dataTEST";

		$dataDas['css'] = $this->sada->CssdataTable();
		$dataDas['js']	= $this->sada->JsdataTable();
		$dataDas['js'][]	= "assets/custom/DataToko.js";



		$this->load->view('view_awal', $dataDas, FALSE);
	}

	public function testingToko()
	{
		var_dump( $this->db->get_where('sada_tokoinuser',['id_user' => 2, 'id_toko' => 2])->first_row());
	}

	public function testData2()
	{
		$this->load->model('datatable');
		// if ($this->input->post()) {
			$table = "sada_toko";
			$column = array('store_id','id_cabang','nama','status');
			$odb = array("id_toko"=>"desc");
			$list = $this->datatable->get_datatables($table,$column,$odb);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $datatable) {
				$no++;
				$row = array();
				$row[] = $datatable->store_id;
				$row[] = $datatable->id_cabang;
				$row[] = $datatable->nama;
				$row[] = $datatable->status;

				//add html for action
				$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void()" title="Edit" onclick="edit_datatable('."'".$datatable->id_toko."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
					  <a class="btn btn-sm btn-danger" href="javascript:void()" title="Hapus" onclick="delete_datatable('."'".$datatable->id_toko."'".')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';

				$data[] = $row;
			}

			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->datatable->count_all($table,$column,$odb),
							"recordsFiltered" => $this->datatable->count_filtered($table,$column,$odb),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		// }

	}

	public function absensi()
	{
		$dataDas['title'] = "Absensi";
		$dataDas['desk'] = "App Retail";
		$dataDas['page'] = "absensi/data";
		$dataDas['css'][]  	= "assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css";
		$dataDas['css'][]  	= "assets/global/plugins/select2/css/select2-bootstrap.min.css";
		$dataDas['css'][]	= "assets/global/plugins/select2/css/select2.min.css";
		$dataDas['css'][]	= "assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css";
		$dataDas['css'][]	= "assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css";
		$dataDas['css'][]	= "assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css";
		$dataDas['css'][]	= "assets/global/plugins/datatables/datatables.min.css";
		$dataDas['css'][]	= "assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css";
		$dataDas['js'][]	= "assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js";
		$dataDas['js'][]	= "assets/global/scripts/datatable.js";
		$dataDas['js'][]	= "assets/global/plugins/datatables/datatables.min.js";
		$dataDas['js'][]	= "assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js";
		$dataDas['js'][]	= "assets/pages/scripts/table-datatables-managed.min.js";
		$dataDas['js'][]	= "assets/custom/tagSelection.js";
		$dataDas['js'][]	= "assets/custom/tagDate.js";
		$dataDas['js'][]	= "assets/pages/scripts/components-select2.min.js";
		$dataDas['js'][]	= "assets/global/plugins/select2/js/select2.full.min.js";
		$dataDas['js'][]	= "assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js";
		$dataDas['js'][]	= "assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js";
		$dataDas['js'][]	= "assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js";
		$dataDas['js'][]	= "assets/pages/scripts/components-date-time-pickers.min.js";
		$dataDas['js'][] ="assets/custom/select2FilterAbsensi.js";

		$this->load->view('view_awal', $dataDas, FALSE);
	}

	public function absensiizin()
	{
		$dataDas['title'] = "Ini Title";
		$dataDas['desk'] = "App Retail";
		$dataDas['page'] = "initempatfile";

		$this->load->view('view_awal', $dataDas, FALSE);
	}

	public function kategori()
	{
		$dataDas['title'] = "Ini Title";
		$dataDas['desk'] = "App Retail";
		$dataDas['page'] = "initempatfile";

		$this->load->view('view_awal', $dataDas, FALSE);
	}


	//a

	public function kategoriadd()
	{
		$dataDas['title'] = "Ini Title";
		$dataDas['desk'] = "App Retail";
		$dataDas['page'] = "initempatfile";

		$this->load->view('view_awal', $dataDas, FALSE);
	}


	//a

	public function oos()
	{
		$dataDas['title'] = "Ini Title";
		$dataDas['desk'] = "App Retail";
		$dataDas['page'] = "initempatfile";

		$this->load->view('view_awal', $dataDas, FALSE);
	}


	//a

	public function reportSku()
	{
		$dataDas['title'] = "Sku Report";
		$dataDas['desk'] = "App Retail";
		$dataDas['page'] = "report/sku";
		$dataDas['baName'] = $this->sada->getBaName();
		$dataDas['tlName'] = $this->sada->getTlName();
		$dataDas['toko'] = $this->sada->getToko();

		$dataDas['js'][]  = "assets/global/plugins/bootstrap/js/bootstrap.min.js";
		$dataDas['js'][]  = "assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js";
		$dataDas['js'][]  = "assets/global/plugins/jquery-validation/js/jquery.validate.min.js";
		
		$dataDas['js'][]  = "assets/global/scripts/app.min.js";
		$dataDas['js'][]  = "assets/pages/scripts/form-validation.min.js";
		$dataDas['css'][]  	= "assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css";
		$dataDas['css'][]  	= "assets/global/plugins/select2/css/select2-bootstrap.min.css";
		$dataDas['css'][]	= "assets/global/plugins/select2/css/select2.min.css";
		$dataDas['css'][]	= "assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css";
		$dataDas['css'][]	= "assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css";
		$dataDas['css'][]	= "assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css";
		$dataDas['css'][]	= "assets/global/plugins/datatables/datatables.min.css";
		$dataDas['css'][]	= "assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css";
		$dataDas['js'][]	= "assets/global/scripts/datatable.js";
		$dataDas['js'][]	= "assets/global/plugins/datatables/datatables.min.js";
		$dataDas['js'][]	= "assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js";
		$dataDas['js'][]	= "assets/pages/scripts/table-datatables-managed.min.js";
		$dataDas['js'][]	= "assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js";
		$dataDas['js'][]	= "assets/pages/scripts/components-bootstrap-select.min.js";
		$dataDas['js'][]	= "assets/custom/tagSelection.js";
		$dataDas['js'][]	= "assets/custom/tagDate.js";
		$dataDas['js'][]	= "assets/pages/scripts/components-select2.min.js";
		$dataDas['js'][]	= "assets/global/plugins/select2/js/select2.full.min.js";
		$dataDas['js'][]	= "assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js";
		$dataDas['js'][]	= "assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js";
		$dataDas['js'][]	= "assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js";
		$dataDas['js'][]	= "assets/pages/scripts/components-date-time-pickers.min.js";
		$dataDas['js'][] ="assets/custom/select2Filter.js";


		$this->load->view('view_awal', $dataDas, FALSE);
	}

	public function skuExcel()
	{
		echo 'tes';
	}
	public function insertReportSku()
	{
		$filterTl = ($this->input->post('tl') == "0") ? "" : $this->input->post('tl');
		$filterName = ($this->input->post('ba') == "0") ? "" : $this->input->post('ba');
		$filterToko = (null != $this->input->post('toko') && $this->input->post('toko') !=0) ? $this->input->post('toko') : "";
		$filterCabang = ($this->input->post('cabang') == "0") ? "" : $this->input->post('cabang');
		$filterKota = ($this->input->post('kota') == "0") ? "" : $this->input->post('kota');
		$startDate =  date('Y-m-d H:i:s', strtotime($this->input->post('startDate')));
		$endDate = $this->input->post('endDate');


		// var_dump($this->input->post('toko'));
		// $a = '2016-05-03 14:11:45';
		// if(strtotime($a) >	 strtotime($startDate)){
		// 	echo 'timestamp win';
		// 	return;
		// }
		// echo 'Start date win';
		// var_dump(strtotime('2016-05-03 14:11:45'));

	}

	//a

	public function reportdetailcontact()
	{
		

  if ($this->input->post()) {
  	$arr['tl'] = $this->input->post("tl");
  $arr['ba'] = $this->input->post("ba");
  $arr['toko'] = $this->input->post("toko");
  $arr['cabang'] = $this->input->post("cabang");
  $arr['kota'] = $this->input->post("kota");
  $arr['startDate'] = date('Y-m-d H:i:s', strtotime($this->input->post("startDate")));
  $arr['endDate'] = date('Y-m-d H:i:s', strtotime($this->input->post("endDate")));
			$this->load->model('datatable');
      $select = "SELECT sada_form_contact.namaibu,sada_form_contact.tgl_contact,sada_form_contact.ttl,sada_form_contact.telp,sada_form_contact.tipe,sada_form_contact.beli,sada_form_contact.oldProduct,sada_form_contact.sampling,sada_form_contact.segmen,
  (SELECT sada_kategori.nama FROM sada_kategori where sada_kategori.id=sada_form_contact.kategori_id) AS 'sada_kategori_label',
  (SELECT sada_kategori.id FROM sada_kategori where sada_kategori.id=sada_form_contact.kategori_id AND sada_form_contact.user_id=sada_user.id_user) AS 'count_sampling',
  (SELECT COUNT(*) FROM sada_form_contact WHERE sada_form_contact.user_id=sada_user.id_user AND sada_form_contact.store_id=toko.id_toko) AS 'contact_count',
  (SELECT COUNT(*) FROM sada_form_contact WHERE sada_form_contact.tipe='newRecruit' AND sada_form_contact.user_id=sada_user.id_user AND sada_form_contact.store_id=toko.id_toko) AS 'count_recruit',
  (SELECT COUNT(*) FROM sada_form_contact WHERE sada_form_contact.tipe='switching' AND sada_form_contact.user_id=sada_user.id_user AND sada_form_contact.store_id=toko.id_toko) AS 'count_switching',
  (SELECT COUNT(*) FROM sada_form_contact WHERE sada_form_contact.kategori_id='1' AND sada_form_contact.user_id=sada_user.id_user AND sada_form_contact.store_id=toko.id_toko) AS 'BC',
  (SELECT COUNT(*) FROM sada_form_contact WHERE sada_form_contact.kategori_id='2' AND sada_form_contact.user_id=sada_user.id_user AND sada_form_contact.store_id=toko.id_toko) AS 'BTI',
  (SELECT COUNT(*) FROM sada_form_contact WHERE sada_form_contact.kategori_id='3' AND sada_form_contact.user_id=sada_user.id_user AND sada_form_contact.store_id=toko.id_toko) AS 'Rusk',
  (SELECT COUNT(*) FROM sada_form_contact WHERE sada_form_contact.kategori_id='4' AND sada_form_contact.user_id=sada_user.id_user AND sada_form_contact.store_id=toko.id_toko) AS 'Pudding',
  (SELECT COUNT(*) FROM sada_form_contact WHERE sada_form_contact.kategori_id='5' AND sada_form_contact.user_id=sada_user.id_user AND sada_form_contact.store_id=toko.id_toko) AS 'Others',
  (SELECT COUNT(*) FROM sada_form_contact WHERE sada_form_contact.beli='Y' AND sada_form_contact.sampling='Y') AS 'strike_sampling',
  ";
  $select .= "toko.id_toko,
                  toko.store_id,
                  toko.id_kota,
                  toko.nama AS 'nama_toko',
                  sada_user.id_user AS 'id_user',
                  sada_user.nama AS 'nama_user',
                  sada_user.stay AS 'stay_user',";
  $where = "";
  if ($arr['startDate'] != "1970-01-01 07:00:00" && $arr['endDate'] != "1970-01-01 07:00:00") {
      $where = "WHERE sada_form_contact.tgl_contact BETWEEN '".$arr['startDate']."' and '".$arr['endDate']."'";
      if ($arr['tl'] != 0) {
        $where .= " AND sada_user.id_user='".$arr['tl']."'";
      }
      elseif ($arr['ba'] != 0) {
        $where .= " AND sada_user.id_user='".$arr['ba']."'";
      }
  }
  else{
     if ($arr['tl'] != 0) {
        $where = " WHERE sada_user.id_user='".$arr['tl']."'";
      }
      if ($arr['ba'] != 0) {
        $where = " WHERE sada_user.id_user='".$arr['ba']."'";
      }
  }
  $join = "";
  if ($arr['tl'] == 0) {
    if ($arr['ba'] !=0) {
      if ($arr['toko'] != 0) {
        $where .= " AND toko.id_toko='".$arr['toko']."'";
        if ($arr['cabang'] !=0) {
          if ($arr['kota'] !=0) {
              $where .= " AND cabang.id_cabang in (SELECT id_cabang FROM sada_kota WHERE id_cabang='".$arr['cabang']."')";
          }
          else{
              $where .= " AND cabang.id_cabang='".$arr['cabang']."'";
          }
        }
      }
      else{
        if ($arr['cabang'] != 0) {
           $where .= " AND cabang.id_cabang in (SELECT id_cabang FROM sada_kota WHERE id_cabang='".$arr['cabang']."')";
        }
      }
    }
  }
  else{
    if ($arr['ba']==0) {
      $select .= "                                                                               ";
    }
  }

  $join .= " LEFT JOIN sada_toko toko ON sada_form_contact.store_id=toko.id_toko";
  $join .= " LEFT JOIN sada_kota kota ON toko.id_kota=kota.id_kota";
  $join .= " LEFT JOIN sada_cabang cabang ON kota.id_cabang=cabang.id_cabang";

  $select .= "
  cabang.nama AS 'nama_cabang',
  kota.nama_kota 'nama_kota'
  FROM sada_form_contact LEFT JOIN sada_user ON sada_form_contact.user_id=sada_user.id_user ".$join." ".$where." ";
  // echo $select;
  $data = $this->db->query($select);
  $no = 1;
  foreach ($data->result() as $key => $value) {
    $row = array();
    $row[] = $no++;
    $row[] = $value->nama_cabang;
    $row[] = $value->nama_kota;
    $row[] = $value->tgl_contact;
    $row[] = $value->nama_user;
    $row[] = $value->nama_toko;
    $row[] = $value->namaibu;
    $row[] = $value->telp;
    $row[] = $value->ttl;
    $row[] = $value->tipe;
    if ($value->beli == "Y") {
      $row[] = "Beli";
    }
    if ($value->beli == "N") {
      $row[] = "Tidak Beli";
    }
    $row[] = $value->oldProduct;
    $row[] = $value->sampling;
    $row[] = $value->segmen;
    $row[] = $value->sada_kategori_label;
    $datsa[] = $row;
    // echo $value->count_sampling;
    // $sel = "SELECT COUNT(*) AS 'coun_sampling' FROM sada_form_contact WHERE kategori_id='".$value->count_sampling."' AND user_id='".$value->id_user."'";
    // $selects = $this->db->query("SELECT SUM(DISTINCT kategori_id) AS 'coun_sampling' FROM sada_form_contact WHERE kategori_id='".$value->count_sampling."' AND user_id='".$value->id_user."'")->row();
    // foreach ($selects->result() as $key => $valuew) {
    //     echo $valuew->coun_sampling;
    // }
    // echo $selects->coun_sampling;
    // echo $sel;
  }
  if (count($datsa) == 0) {
    $output = array(
              "success" => "0",
              // "draw" => $_POST['draw'],
              // "recordsTotal" => $this->datatable->count_all($table,$column,$odb),
              // "recordsFiltered" => $this->datatable->count_filtered($table,$column,$odb),
             
          );
  }
  else{
  	$output = array(
              "success" => "1",
              // "draw" => $_POST['draw'],
              // "recordsTotal" => $this->datatable->count_all($table,$column,$odb),
              // "recordsFiltered" => $this->datatable->count_filtered($table,$column,$odb),
              "data" => $datsa,
          );
  }

  echo json_encode($output);
  }
  else{
    $dataDas['title'] = "contact_detail contact_detail";
      $dataDas['desk'] = "App Retail";
      $dataDas['page'] = "contact/contact_detail";

      $dataDas['css'] = $this->sada->CssdataTable();
      $dataDas['js']  = $this->sada->JsdataTable();

		$dataDas['js'][]  = "assets/global/plugins/bootstrap/js/bootstrap.min.js";
		$dataDas['js'][]  = "assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js";
		$dataDas['js'][]  = "assets/global/plugins/jquery-validation/js/jquery.validate.min.js";
		
		$dataDas['js'][]  = "assets/global/scripts/app.min.js";
		$dataDas['js'][]  = "assets/pages/scripts/form-validation.min.js";
      $dataDas['css'][]   = "assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css";
      $dataDas['css'][]   = "assets/global/plugins/select2/css/select2-bootstrap.min.css";
      $dataDas['css'][] = "assets/global/plugins/select2/css/select2.min.css";
      $dataDas['css'][] = "assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css";
      $dataDas['css'][] = "assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css";
      $dataDas['css'][] = "assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css";
      $dataDas['js'][]  = "assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js";
      $dataDas['js'][]  = "assets/pages/scripts/components-bootstrap-select.min.js";
      $dataDas['js'][]  = "assets/custom/tagSelection.js";

      $dataDas['js'][]  = "assets/custom/tagDate.js";
      $dataDas['js'][]  = "assets/pages/scripts/components-select2.min.js";
      $dataDas['js'][]  = "assets/global/plugins/select2/js/select2.full.min.js";
      $dataDas['js'][]  = "assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js";
      $dataDas['js'][]  = "assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js";
      $dataDas['js'][]  = "assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js";
      $dataDas['js'][]  = "assets/pages/scripts/components-date-time-pickers.min.js";
      $dataDas['js'][]  = "https://code.jquery.com/jquery-1.10.2.js";
      $dataDas['js'][] ="assets/custom/select2Filter.js";
      $dataDas['js'][] ="assets/custom/CountTotal.js";

      $dataDas['js'][]  = "assets/custom/rDetailContact.js";
      $this->load->view('view_awal', $dataDas, FALSE);
  }
		// $dataDas['title'] = "Detail Contact";
		// $dataDas['desk'] = "";
		// $dataDas['page'] = "contact/contact_detail";
		// $dataDas['css'] = $this->sada->CssdataTable();
		// $dataDas['js']	= $this->sada->JsdataTable();
		// $dataDas['css'][]  	= "assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css";
		// $dataDas['css'][]  	= "assets/global/plugins/select2/css/select2-bootstrap.min.css";
		// $dataDas['css'][]	= "assets/global/plugins/select2/css/select2.min.css";
		// $dataDas['css'][]	= "assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css";
		// $dataDas['css'][]	= "assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css";
		// $dataDas['css'][]	= "assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css";
		// $dataDas['js'][]	= "assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js";
		// $dataDas['js'][]	= "assets/pages/scripts/components-bootstrap-select.min.js";
		// $dataDas['js'][]	= "assets/custom/tagSelection.js";
		// $dataDas['js'][]	= "assets/custom/tagDate.js";
		// $dataDas['js'][]	= "assets/pages/scripts/components-select2.min.js";
		// $dataDas['js'][]	= "assets/global/plugins/select2/js/select2.full.min.js";
		// $dataDas['js'][]	= "assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js";
		// $dataDas['js'][]	= "assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js";
		// $dataDas['js'][]	= "assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js";
		// $dataDas['js'][]	= "assets/pages/scripts/components-date-time-pickers.min.js";
		// $dataDas['js'][] ="assets/custom/select2Filter.js";
		// $dataDas['js'][] ="assets/custom/rDetailContact.js";

		// $this->load->view('view_awal', $dataDas, FALSE);
	}


	//a

	public function reporttotalcontact()
	{
		$dataDas['title'] 	= "Total Contact";
		$dataDas['desk'] 	= "";
		$dataDas['page'] 	= "contact/contact_total";
		// $dataDas['css'] = $this->sada->CssdataTable();
		// $dataDas['js']	= $this->sada->JsdataTable();
		$dataDas['js'][]  = "assets/global/plugins/jquery-validation/js/jquery.validate.min.js";
		
		$dataDas['js'][]  = "assets/global/scripts/app.min.js";
		$dataDas['js'][]  = "assets/pages/scripts/form-validation.min.js";
		$dataDas['css'][]  	= "assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css";
		$dataDas['css'][]  	= "assets/global/plugins/select2/css/select2-bootstrap.min.css";
		$dataDas['css'][]	= "assets/global/plugins/select2/css/select2.min.css";
		$dataDas['css'][]	= "assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css";
		$dataDas['css'][]	= "assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css";
		$dataDas['css'][]	= "assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css";
		$dataDas['js'][]	= "assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js";
		$dataDas['js'][]	= "assets/pages/scripts/components-bootstrap-select.min.js";
		$dataDas['js'][]	= "assets/custom/tagSelection.js";
		$dataDas['js'][]	= "assets/custom/tagDate.js";
		$dataDas['js'][]	= "assets/pages/scripts/components-select2.min.js";
		$dataDas['js'][]	= "assets/global/plugins/select2/js/select2.full.min.js";
		$dataDas['js'][]	= "assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js";
		$dataDas['js'][]	= "assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js";
		$dataDas['js'][]	= "assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js";
		$dataDas['js'][]	= "assets/pages/scripts/components-date-time-pickers.min.js";
		$dataDas['js'][]	= "https://code.jquery.com/jquery-1.10.2.js";
		$dataDas['js'][] ="assets/custom/select2Filter.js";
		$dataDas['js'][] ="assets/custom/CountTotal.js";
		$this->load->view('view_awal', $dataDas, FALSE);
	}


	//a

	public function reportpromo()
	{
		if ($this->input->post()) {
			$this->load->model('datatable');
		// if ($this->input->post()) {

			$arr['tl'] = $this->input->post("tl");
			$arr['ba'] = $this->input->post("ba");
			$arr['toko'] = $this->input->post("toko");
			$arr['cabang'] = $this->input->post("cabang");
			$arr['kota'] = $this->input->post("kota");
			$arr['startDate'] = date('Y-m-d H:i:s', strtotime($this->input->post("startDate")));
			$arr['endDate'] = date('Y-m-d H:i:s', strtotime($this->input->post("endDate")));

			$select = "SELECT DISTINCT sada_promo.tipe,sada_promo.jenis,sada_promo.keterangan,sada_promo.awalTanggal,sada_promo.selesaiTanggal,
				(
						SELECT
							GROUP_CONCAT(prom.foto SEPARATOR ',')
						FROM
							sada_promo AS prom
						WHERE
							prom.merk LIKE '%romina%'
						AND prom.user_id = sada_user.id_user
						AND prom.store_id = toko.id_toko
						AND date(prom.timestamp) = date(sada_promo.timestamp)
					) AS 'promina_foto',
				(
						SELECT
							GROUP_CONCAT(prom.foto SEPARATOR ',')
						FROM
							sada_promo AS prom
						WHERE
							prom.merk NOT LIKE '%romina%'
						AND prom.user_id = sada_user.id_user
						AND prom.store_id = toko.id_toko
						AND date(prom.timestamp) = date(sada_promo.timestamp)
					) AS 'kompetitor_foto',
";
$select .= "toko.id_toko,
		                  toko.store_id,
		                  toko.id_kota,
		                  toko.nama AS 'nama_toko',
		                  sada_user.id_user AS 'id_user',
		                  sada_user.nama AS 'nama_user',
		                  sada_user.stay AS 'stay_user',";
		  $where = "";
		  if ($arr['startDate'] != "1970-01-01 07:00:00" && $arr['endDate'] != "1970-01-01 07:00:00") {
		      $where = "WHERE sada_promo.timestamp BETWEEN '".$arr['startDate']."' and '".$arr['endDate']."'";
		      if ($arr['tl'] != 0) {
		        $where .= " AND sada_user.id_user='".$arr['tl']."'";
		      }
		      elseif ($arr['ba'] != 0) {
		        $where .= " AND sada_user.id_user='".$arr['ba']."'";
		      }
		  }
		  else{
		     if ($arr['tl'] != 0) {
		        $where = " WHERE sada_user.id_user='".$arr['tl']."'";
		      }
		      if ($arr['ba'] != 0) {
		        $where = " WHERE sada_user.id_user='".$arr['ba']."'";
		      }
		  }
		  $join = "";
		  if ($arr['tl'] == 0) {
		    if ($arr['ba'] !=0) {
		      if ($arr['toko'] != 0) {      
		        $where .= " AND toko.id_toko='".$arr['toko']."'";
		        if ($arr['cabang'] !=0) {
		          if ($arr['kota'] !=0) {
		              $where .= " AND cabang.id_cabang in (SELECT id_cabang FROM sada_kota WHERE id_cabang='".$arr['cabang']."')";
		          }
		          else{
		              $where .= " AND cabang.id_cabang='".$arr['cabang']."'";
		          }
		        }
		      }
		      else{
		        if ($arr['cabang'] != 0) {
		           $where .= " AND cabang.id_cabang in (SELECT id_cabang FROM sada_kota WHERE id_cabang='".$arr['cabang']."')"; 
		        }
		      }
		    }
		  }
		  else{
		    if ($arr['ba']==0) {
		      $select .= "                                                                               ";
		    }
		  }
		  
		  $join .= " LEFT JOIN sada_toko toko ON sada_promo.store_id=toko.id_toko";
		  $join .= " LEFT JOIN sada_kota kota ON toko.id_kota=kota.id_kota";
		  $join .= " LEFT JOIN sada_cabang cabang ON kota.id_cabang=cabang.id_cabang";

		  $select .= "
		  cabang.nama AS 'nama_cabang',
		  kota.nama_kota 'nama_kota'
		  FROM sada_promo LEFT JOIN sada_user ON sada_promo.user_id=sada_user.id_user ".$join." ".$where." ";
		  // echo $select;
		  

			$table = "sada_user";
			$column = array('nik','nama','akses');
			$odb = array("id_user"=>"desc");
			$datas = $this->db->query($select);
			$data = array();
			$no = 1;
			// $no = $_POST['start'];
			foreach ($datas->result() as $datatable) {
				$row = array();
				$row[] = $no++;
				$row[] = $datatable->nama_cabang;
				$row[] = $datatable->nama_kota;
				$row[] = $datatable->store_id;
				$row[] = $datatable->nama_toko;
				$row[] = $datatable->nama_user;
				if ($datatable->tipe == "consumerPromo") {
					$row[] = "Consumer Promo";
				}
				if ($datatable->tipe == "secondaryDisplay") {
					$row[] = "Secondary Display";
				}
				if ($datatable->tipe == "activation") {
					$row[] = "Activation";
				}
				$row[] = $datatable->jenis;
				$row[] = $datatable->keterangan;
				$row[] = $datatable->awalTanggal;
				$row[] = $datatable->selesaiTanggal;
				$foto_promina = explode(',', $datatable->promina_foto);
				foreach ($foto_promina as $key => $value) {
					// echo $value;
					$promina[] = "<img src='assets/upload/".$value."' class='img-thumbnail'>";
				}
				$row[] = $promina;
				$foto_kompetitor = explode(',', $datatable->kompetitor_foto);
				foreach ($foto_kompetitor as $key => $values) {
					// echo $value;
					$kompetitor[] = "<img src='assets/upload/".$values."' class='img-thumbnail'>";
				}
				$row[] = $kompetitor;
				// $row[] = $datatable->nama;
				// $row[] = $datatable->nama;
				// $row[] = $datatable->nama;

				

				$data[] = $row;
			}

			$output = array(
							// "draw" => $_POST['draw'],
							// "recordsTotal" => $this->datatable->count_all($table,$column,$odb),
							// "recordsFiltered" => $this->datatable->count_filtered($table,$column,$odb),
							"data" => $data,
					);
			echo json_encode($output);
		}
		else{
			$dataDas['title'] = "Report Promo";
			$dataDas['desk'] = "App Retail";
			$dataDas['page'] = "promo/reportpromo";

			$dataDas['css'] = $this->sada->CssdataTable();
			$dataDas['js']	= $this->sada->JsdataTable();

			$dataDas['js'][]  = "assets/global/plugins/jquery-validation/js/jquery.validate.min.js";
			
			$dataDas['js'][]  = "assets/global/scripts/app.min.js";
			$dataDas['js'][]  = "assets/pages/scripts/form-validation.min.js";

			$dataDas['css'][]  	= "assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css";
			$dataDas['css'][]  	= "assets/global/plugins/select2/css/select2-bootstrap.min.css";
			$dataDas['css'][]	= "assets/global/plugins/select2/css/select2.min.css";
			$dataDas['css'][]	= "assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css";
			$dataDas['css'][]	= "assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css";
			$dataDas['css'][]	= "assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css";
			$dataDas['js'][]	= "assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js";
			$dataDas['js'][]	= "assets/pages/scripts/components-bootstrap-select.min.js";
			$dataDas['js'][]	= "assets/custom/tagSelection.js";
			$dataDas['js'][]	= "assets/custom/tagDate.js";
			$dataDas['js'][]	= "assets/pages/scripts/components-select2.min.js";
			$dataDas['js'][]	= "assets/global/plugins/select2/js/select2.full.min.js";
			$dataDas['js'][]	= "assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js";
			$dataDas['js'][]	= "assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js";
			$dataDas['js'][]	= "assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js";
			$dataDas['js'][]	= "assets/pages/scripts/components-date-time-pickers.min.js";
			$dataDas['js'][]	= "https://code.jquery.com/jquery-1.10.2.js";
			$dataDas['js'][] ="assets/custom/select2Filter.js";
			$dataDas['js'][] ="assets/custom/CountTotal.js";

			$dataDas['js'][]	= "assets/custom/DataPromo.js";
			$this->load->view('view_awal', $dataDas, FALSE);
		}
	}


	//report Out Of Stock
	public function reportOutOfStock()
	{
		$dataDas['title'] = "Out Of Stock Report";
		$dataDas['desk'] = "App Retail";
		$dataDas['page'] = "report/outOfStock";
		$dataDas['js'][]  = "assets/global/plugins/jquery-validation/js/jquery.validate.min.js";
			
		$dataDas['js'][]  = "assets/global/scripts/app.min.js";
		$dataDas['js'][]  = "assets/pages/scripts/form-validation.min.js";
		$dataDas['css'][]  	= "assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css";
		$dataDas['css'][]  	= "assets/global/plugins/select2/css/select2-bootstrap.min.css";
		$dataDas['css'][]	= "assets/global/plugins/select2/css/select2.min.css";
		$dataDas['css'][]	= "assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css";
		$dataDas['css'][]	= "assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css";
		$dataDas['css'][]	= "assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css";
		$dataDas['css'][]	= "assets/global/plugins/datatables/datatables.min.css";
		$dataDas['css'][]	= "assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css";
		$dataDas['js'][]	= "assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js";
		$dataDas['js'][]	= "assets/global/scripts/datatable.js";
		$dataDas['js'][]	= "assets/global/plugins/datatables/datatables.min.js";
		$dataDas['js'][]	= "assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js";
		$dataDas['js'][]	= "assets/pages/scripts/table-datatables-managed.min.js";
		$dataDas['js'][]	= "assets/custom/tagSelection.js";
		$dataDas['js'][]	= "assets/custom/tagDate.js";
		$dataDas['js'][]	= "assets/pages/scripts/components-select2.min.js";
		$dataDas['js'][]	= "assets/global/plugins/select2/js/select2.full.min.js";
		$dataDas['js'][]	= "assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js";
		$dataDas['js'][]	= "assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js";
		$dataDas['js'][]	= "assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js";
		$dataDas['js'][]	= "assets/pages/scripts/components-date-time-pickers.min.js";
		$dataDas['js'][] ="assets/custom/select2FilterOutOfStock.js";


		$this->load->view('view_awal', $dataDas, FALSE);
	}





/*END*/








}

/* End of file Dashboard.php */
/* Location: ./application/controllers/Dashboard.php */