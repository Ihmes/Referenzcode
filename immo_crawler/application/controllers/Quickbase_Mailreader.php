<?php

include_once(BASEPATH."libraries/External/quickbase/quickbase.php");

class Quickbase_Mailreader extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url_helper');
        $this->load->helper('html');
        $this->load->helper('form_helper');
        $this->load->model('Quickbase_Mailreader_model');
    }

    public function index(){

        if(isset($aAddData)){
            $data['additional'] = $aAddData; 
        }

        $data['title'] = 'Quickbase | Cron Results';
        $data['lastmail'] = $this->Quickbase_Mailreader_model->email_pull('UNSEEN',true);

        $this->load->view('qb_cron/qb_cron_resultpage',$data);
    }

    public function qb(){

    }
}

?>