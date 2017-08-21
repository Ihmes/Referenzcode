<?php

class Quickbase_Contextio extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url_helper');
        $this->load->helper('html');
        $this->load->helper('form_helper');
        $this->load->model('Quickbase_Contextio_model');
    }

    public function index(){

        if(isset($aAddData)){
            $data['additional'] = $aAddData;
        }

        $data['title'] = 'Quickbase | Contextio Functions';

        $this->load->view('qb_cio/cio_xmlexport',$data);
    }

    public function xmlloader(){

        $data['ocio'] = $this->Quickbase_Contextio_model->cio_get_attachments();
        $this->load->view('qb_cio/cio_xmlexport',$data);
    }


}