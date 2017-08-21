<?php

class Immocrons extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('immospider_model');
        $this->load->model('immocrons_model');
    }

    public function index($aAddData = NULL){

        $oImmospiderModel = $this->load->model('immospider_model');

        if(isset($aAddData)){
            $data['additional'] = $aAddData;
        }

        $data['title'] = 'Immospider | Url Overview';
        $data['urls'] = $this->immospider_model->get_urls();

        $this->load->view('templates/header', $data);
        $this->load->view('immospider/overview', $data);
        $this->load->view('templates/footer');

    }

    public function update_item_count(){

        $oImmospiderModel = $this->load->model('immospider_model');
        $aUrls = $this->immospider_model->get_urls();
        $this->immocrons_model->get_new_object_count($aUrls);

    }
}