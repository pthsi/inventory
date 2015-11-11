<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Decal_prod extends CI_Controller {

	/**
	 * @author      : Mpod Schuzatcky    
	 * @web         : http://itmov.wordpress.com
	 * @keterangan  : Controller halaman master glasir
	 **/
	
	public function index()
	{
		$cek = $this->session->userdata('logged_in');
		if(!empty($cek)){
			$cari = $this->input->post('txt_cari');
			$tgl = $this->dclModel->tgl_sql($this->input->post('cari_tgl'));
			
                        $where = " WHERE a.id_related <>'' AND a.deleted <> 1";
			if(!empty($cari)){
				$where .= " AND a.id_related LIKE '%$cari%' OR a.inputer LIKE '%$cari%' OR a.parent_id LIKE '%$cari%' OR b.nama LIKE '%$cari%' AND a.deleted = 0";
			}
			if(!empty($tgl)){
				$where .= " AND a.tgl_input LIKE '%$tgl%' AND a.deleted = 0";
			}
			
			$d['prg']= $this->config->item('prg');
			$d['web_prg']= $this->config->item('web_prg');
			
			$d['nama_program']= $this->config->item('nama_program');
			$d['instansi']= $this->config->item('instansi');
			$d['usaha']= $this->config->item('usaha');
			$d['alamat_instansi']= $this->config->item('alamat_instansi');

			
			$d['judul']="Daftar input stock produksi decal";
			
			//paging
			$page=$this->uri->segment(3);
			$limit=$this->config->item('limit_data');
			if(!$page):
			$offset = 0;
			else:
			$offset = $page;
			endif;
			
			$text = "SELECT a.id_related,a.tgl_input, a.parent_id, b.nama, a.inputer
                                    FROM decal_phd a
                                    LEFT JOIN decal_items b ON b.id = a.parent_id
                                $where ";
                        
			$tot_hal = $this->dclModel->manualQuery($text);		
			
			$d['tot_hal'] = $tot_hal->num_rows();
			
			$config['base_url'] = site_url() . '/decal_prod/index/';
			$config['total_rows'] = $tot_hal->num_rows();
			$config['per_page'] = $limit;
			$config['uri_segment'] = 3;
			$config['next_link'] = 'Lanjut &raquo;';
			$config['prev_link'] = '&laquo; Kembali';
			$config['last_link'] = '<b>Terakhir &raquo; </b>';
			$config['first_link'] = '<b> &laquo; Pertama</b>';
			$this->pagination->initialize($config);
			$d["paginator"] =$this->pagination->create_links();
			$d['hal'] = $offset;
			

			$text = "SELECT a.id_related,a.tgl_input, a.parent_id, b.nama, a.inputer
                                    FROM decal_phd a
                                    LEFT JOIN decal_items b ON b.id = a.parent_id
                                        $where
                                        GROUP BY a.id_related
					ORDER BY a.id_related DESC
					LIMIT $limit OFFSET $offset";
                        
			$d['data'] = $this->dclModel->manualQuery($text);
			
			
			$d['content'] = $this->load->view('decal_prod/view', $d, true);		
			$this->load->view('home',$d);
		}else{
			header('location:'.base_url());
		}
	}
	
	public function tambah()
	{
		$cek = $this->session->userdata('logged_in');
		if(!empty($cek)){
			$d['prg']= $this->config->item('prg');
			$d['web_prg']= $this->config->item('web_prg');
			
			$d['nama_program']= $this->config->item('nama_program');
			$d['instansi']= $this->config->item('instansi');
			$d['usaha']= $this->config->item('usaha');
			$d['alamat_instansi']= $this->config->item('alamat_instansi');

			$d['judul']="Input stock produksi decal";
			
			$id         = $this->dclModel->MaxPhDecal();
			
			$d['id']                = $id;
                        $d['batch']             = '';
                        $d['no_po']             = '';
                        $d['parent_id']         = '';
                        $d['nama_decal']        = '';
                        $d['nama_decal']        = '';
                        $d['tgli']              = '';
                        $d['jam']               = '';
                        $d['jenis_decal']       = '';
                        $d['shift']             = 1;
                        $d['id_bm']             = 1;
                        $d['id_bmt']            = 1;
                        $d['size_kertas']       = 0;
                        $d['size_kat']          = 1;
                        $d['warna']             = '';
                        $d['komposisi']         = '';
                        $d['kw1']               = '';
                        $d['kw2']               = '';
                        $d['kw3']               = '';
                        $d['petugas']           = '';
			
                        $jd = "SELECT * FROM global_jenis_decal";
			$d['l_jd'] = $this->dclModel->manualQuery($jd);
			$bm = "SELECT * FROM global_mesin where jns_bm like '%decal_cetak%' OR jns_bm like '%Tidak Ada%'";
			$d['l_bm'] = $this->dclModel->manualQuery($bm);
                        $xbm = "SELECT * FROM global_mesin where jns_bm like '%Tidak Ada%'";
			$d['x_bm'] = $this->dclModel->manualQuery($xbm);
                        $sft = "SELECT * FROM global_shift";
			$d['l_sft'] = $this->dclModel->manualQuery($sft);
                        $uk = "SELECT * FROM global_size where nama like '%decal_size_paper%' OR nama like '%Tidak Ada%'";
			$d['l_uk'] = $this->dclModel->manualQuery($uk);
                        $ul = "SELECT * FROM global_size where nama like '%decal_size_kat%' OR nama like '%Tidak Ada%'";
			$d['l_ul'] = $this->dclModel->manualQuery($ul);
			
			$d['content'] = $this->load->view('decal_prod/form', $d, true);		
			$this->load->view('home',$d);
		}else{
			header('location:'.base_url());
		}
	}
        
        public function simpan()
	{
		
		$cek = $this->session->userdata('logged_in');
		if(!empty($cek)){
				$up['id']		= $this->input->post('id');
				$up['inputer']          = $this->session->userdata('username');
                                $idx    		= $this->input->post('id');
				$inputerx               = $this->session->userdata('username');
				
				$ud['id_related']       = $this->input->post('id');
                                $ud['shift']            = $this->input->post('shift');
                                $ud['jam']              = $this->input->post('jam');
                                $ud['id_bm']            = $this->input->post('id_bm');
                                $ud['id_bmt']           = $this->input->post('id_bmt');
                                $ud['petugas']          = $this->input->post('petugas');
                                $ud['tgli']             = $this->dclModel->tgl_sql($this->input->post('tgli'));
                                $ud['inputer']          = $this->session->userdata('username');
                                $parent_idx             = $this->input->post('parent_id');
                                $jmlx                   = $this->input->post('jml');
                                $shiftx                 = $this->input->post('shift');
                                $jamx                   = $this->input->post('jam');
                                $tgli                   = $this->input->post('tgli');
                                $id_bmx                 = $this->input->post('id_bm');
                                $id_bmtx                = $this->input->post('id_bmt');
                                $petugasx               = $this->input->post('petugas');
				
				$id['id']               = $this->input->post('id');
				
				$id_d['id_related']     = $this->input->post('id');
				$id_d['parent_id'] = $this->input->post('parent_id');
                                $id_d['id']             = $this->input->post('batch');
				
				$data = $this->dclModel->getSelectedData("decal_ph",$id);
				if($data->num_rows()>0){
                                                $this->dclModel->updateData("decal_ph",$up,$id);
						$datax = $this->dclModel->getSelectedData("decal_phd",$id_d);
						if($datax->num_rows()>0){
                                                        //$ud['tgl_update']   = date('Y-m-d h:i:s');
							//$this->dclModel->updateData("decal_phd",$ud,$id_d);
                                                        echo 'Update data Sukses';
						}else{
                                                        $tgl_inputx		        = date('Y-m-d h:i:s');
                                                        $sql = "insert into decal_phd (id,id_related,parent_id, item_code, isi_motif,jml,rusak,shift,id_bm,id_bmt,tgli,jam,petugas,inputer,
                                                                tgl_input,tgl_update,tgl_delete,deleted)
                                                                select NULL,'$idx',parent_id,item_code,isi_motif,isi_motif*$jmlx,0,'$shiftx','$id_bmx','$id_bmtx','$tgli','$jamx','$petugasx','$inputerx',
                                                                '$tgl_inputx','0000-00-00 00:00:00','0000-00-00 00:00:00',0 from decal_items_detail where parent_id = '$parent_idx'";
                                                        $this->db->query($sql);
                                                        echo 'Simpan data Sukses';
						}
				}else{
                                        $up['tgl_input']		= date('Y-m-d h:i:s');
                                        $tgl_inputx		        = date('Y-m-d h:i:s');
					$this->dclModel->insertData("decal_ph",$up);
                                        $sql = "insert into decal_phd (id,id_related,parent_id, item_code, isi_motif,jml,rusak,shift,id_bm,id_bmt,tgli,jam,petugas,inputer,
                                                tgl_input,tgl_update,tgl_delete,deleted)
                                                select NULL,'$idx',parent_id,item_code,isi_motif,isi_motif*$jmlx,0,'$shiftx','$id_bmx','$id_bmtx','$tgli','$jamx','$petugasx','$inputerx',
                                                '$tgl_inputx','0000-00-00 00:00:00','0000-00-00 00:00:00',0 from decal_items_detail where parent_id = '$parent_idx'";
                                        $this->db->query($sql);
					echo 'Simpan data Sukses';		
				}
		}else{
				header('location:'.base_url());
		}
	
	}
	
	public function cetak()
	{
		$cek = $this->session->userdata('logged_in');
		if(!empty($cek)){
			
			$d['prg']= $this->config->item('prg');
			$d['web_prg']= $this->config->item('web_prg');
			
			$d['nama_program']= $this->config->item('nama_program');
			$d['instansi']= $this->config->item('instansi');
			$d['usaha']= $this->config->item('usaha');
			$d['alamat_instansi']= $this->config->item('alamat_instansi');
			
			$d['judul'] = "Form Order Produksi Glasir";
			
			$id = $this->uri->segment(3);
			$text = "SELECT * FROM glasir_ph WHERE no_prod='$id'";
			$data = $this->dclModel->manualQuery($text);
			if($data->num_rows() > 0){
				foreach($data->result() as $db){
					$d['no_prod']	= $id;
					$d['tgl_plng']	= $this->dclModel->tgl_indo($db->tgl_plng);
					$d['no_po']	= $db->no_po;
                                        $d['planner']	= $db->planner;
                                        $d['parent_id']	= '';
				}
			}else{
                                        $d['parent_id']	= '';
					$d['tgl_plng']	='';
					$d['no_po']	='';
                                        $d['planner']	='';
			}
			
			$text = "SELECT a.no_prod,a.id_glasir,c.nama_gps,a.volume,a.densitas,
					d.nama_bm,e.nama_tong,a.petugas,a.inputer
					FROM glasir_phd as a 
					JOIN glasir_ph as b
					ON a.no_prod=b.no_prod
					JOIN glasir_patt as c
					ON a.idgps=c.idgps
					JOIN global_mesin as d
					ON a.id_bm=d.id_bm
					JOIN global_tong as e
					ON a.id_tong=e.id_tong
					WHERE a.no_prod='$id'";
			$d['data']= $this->dclModel->manualQuery($text);
									
			$this->load->view('glasir_prod/cetak',$d);
		}else{
			header('location:'.base_url());
		}
	}
	
	public function edit()
	{
		$cek = $this->session->userdata('logged_in');
		if(!empty($cek)){
			
			$d['prg']= $this->config->item('prg');
			$d['web_prg']= $this->config->item('web_prg');
			
			$d['nama_program']= $this->config->item('nama_program');
			$d['instansi']= $this->config->item('instansi');
			$d['usaha']= $this->config->item('usaha');
			$d['alamat_instansi']= $this->config->item('alamat_instansi');
			
			$d['judul'] = "Ubah input stock stock decal";
			
			$id = $this->uri->segment(3);
			$text = "SELECT * FROM decal_ph WHERE id='$id'";
			$data = $this->dclModel->manualQuery($text);
			if($data->num_rows() > 0){
				foreach($data->result() as $db){
					$d['id']                = $id;
                                        $d['batch']             = '';
                                        $d['no_po']             = '';
                                        $d['parent_id']         = '';
                                        $d['nama_decal']        = '';
                                        $d['tgli']              = '';
                                        $d['jam']               = '';
                                        $d['jenis_decal']       = '';
                                        $d['shift']             = 1;
                                        $d['id_bm']             = 1;
                                        $d['id_bmt']            = 1;
                                        $d['size_kertas']       = 0;
                                        $d['size_kat']          = 1;
                                        $d['warna']             = '';
                                        $d['komposisi']         = '';
                                        $d['kw1']               = '';
                                        $d['kw2']               = '';
                                        $d['kw3']               = '';
                                        $d['petugas']           = '';
				}
			}else{
					$d['id']                =$id;
                                        $d['batch']             = '';
                                        $d['no_po']             = '';
                                        $d['parent_id']	= '';
                                        $d['nama_decal']        = '';
                                        $d['tgli']              = '';
                                        $d['jam']               = '';
                                        $d['jenis_decal']       = '';
                                        $d['shift']             = 1;
                                        $d['id_bm']             = 1;
                                        $d['id_bmt']            = 1;
                                        $d['size_kertas']       = 0;
                                        $d['size_kat']          = 1;
                                        $d['warna']             = '';
                                        $d['komposisi']         = '';
                                        $d['kw1']               = '';
                                        $d['kw2']               = '';
                                        $d['kw3']               = '';
                                        $d['petugas']           = '';
			}
			
                        $jd = "SELECT * FROM global_jenis_decal";
			$d['l_jd'] = $this->dclModel->manualQuery($jd);
			$bm = "SELECT * FROM global_mesin where jns_bm like '%decal_cetak%' OR jns_bm like '%Tidak Ada%'";
			$d['l_bm'] = $this->dclModel->manualQuery($bm);
                        $xbm = "SELECT * FROM global_mesin where jns_bm like '%Tidak Ada%'";
			$d['x_bm'] = $this->dclModel->manualQuery($xbm);
                        $sft = "SELECT * FROM global_shift";
			$d['l_sft'] = $this->dclModel->manualQuery($sft);
                        $uk = "SELECT * FROM global_size where nama like '%decal_size_paper%' OR nama like '%Tidak Ada%'";
			$d['l_uk'] = $this->dclModel->manualQuery($uk);
                        $ul = "SELECT * FROM global_size where nama like '%decal_size_kat%' OR nama like '%Tidak Ada%'";
			$d['l_ul'] = $this->dclModel->manualQuery($ul);
									
			$d['content'] = $this->load->view('decal_prod/form', $d, true);		
			$this->load->view('home',$d);
		}else{
			header('location:'.base_url());
		}
	}
        
        public function editDetail()
	{
		$cek = $this->session->userdata('logged_in');
		if(!empty($cek)){
			
			$d['prg']= $this->config->item('prg');
			$d['web_prg']= $this->config->item('web_prg');
			
			$d['nama_program']= $this->config->item('nama_program');
			$d['instansi']= $this->config->item('instansi');
			$d['usaha']= $this->config->item('usaha');
			$d['alamat_instansi']= $this->config->item('alamat_instansi');
			
			$d['judul'] = "Ubah input stock glasir turun ball mill";
			
			$id = $this->uri->segment(3);
                        $id_decal_items = $this->uri->segment(4);
                        $batch = $this->uri->segment(5);
			//$text = "";
			//$data = $this->dclModel->manualQuery($text);
									
			$this->load->view('data_decal',$d);	
			//$this->load->view('home',$d);
		}else{
			header('location:'.base_url());
		}
	}
        
        public function DataDetail()
	{
		$cek = $this->session->userdata('logged_in');
		if(!empty($cek)){
			
			$id = $this->input->post('kode');
			$text1 = "SELECT a.id,a.id_group,a.tgli,a.jam,a.id_related,a.parent_id,a.item_code,sum(a.isi_motif) as isi_motif,sum(a.jml) as jml,sum(a.rusak) as rusak,a.shift,a.id_bm,a.id_bmt,a.petugas,a.inputer from decal_phd a  
                                    WHERE a.id_related='$id' AND a.deleted = 0 group by a.id_group";
			$d['data']= $this->dclModel->manualQuery($text1);
                        
                        $text2 = "SELECT a.id,a.id_group,a.id_related,a.parent_id,a.item_code,b.item,a.isi_motif,a.jml,a.rusak,a.shift,a.id_bm,a.id_bmt,a.petugas,a.inputer from decal_phd a 
                                    left join decal_items_detail b on a.item_code = b.item_code
                                    WHERE a.id_related='$id' AND a.deleted = 0";
			$d['dataDetail']= $this->dclModel->manualQuery($text2);

			$this->load->view('decal_prod/detail',$d);
		}else{
			header('location:'.base_url());
		}
	}
	
	public function hapus()
	{
		$cek = $this->session->userdata('logged_in');
		if(!empty($cek)){
                    
                                $id = $this->uri->segment(3);
                                $tgl_deleted = date('Y-m-d h:i:s');
                                $this->dclModel->manualQuery("UPDATE decal_phd SET deleted = 1,tgl_delete = '$tgl_deleted' WHERE id_related='$id'");
                                $this->dclModel->manualQuery("UPDATE decal_ph SET deleted = 1,tgl_delete = '$tgl_deleted' WHERE id='$id'");
                                echo "<meta http-equiv='refresh' content='0; url=".base_url()."index.php/decal_prod'>";			
		}else{
			header('location:'.base_url());
		}
	}
        
	public function hapus_detail()
	{
		$cek = $this->session->userdata('logged_in');
		if(!empty($cek)){                        
                                $id = $this->uri->segment(3);
                                $kode = $this->uri->segment(4);
                                $batch = $this->uri->segment(5);
                                $tgl_deleted = date('Y-m-d h:i:s');
                                $this->dclModel->manualQuery("UPDATE decal_phd SET deleted = 1,tgl_delete = '$tgl_deleted' WHERE id_related='$id' AND id_decal_items='$kode' AND id='$batch'");
                                $this->edit();
                        
		}else{
			header('location:'.base_url());
		}
	}
        
        public function getPicProdDecal()
	{
                $this->load->model('dclModel');
		$data['data_passed'] = $this->dclModel->getPicProdDecal();

		if ($data['data_passed']){

			#convert data array passed into json
			echo json_encode($data['data_passed']);
			//echo $data['data_passed'];

		}
	}
	
}

/* End of file profil.php */
/* Location: ./application/controllers/profil.php */