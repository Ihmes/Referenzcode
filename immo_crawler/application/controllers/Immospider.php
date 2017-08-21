<?php

class Immospider extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('immospider_model');
        $this->load->helper('url_helper');
        $this->load->helper('html');
        $this->load->helper('form_helper');
    }

    public function index($aAddData = NULL)
    {
        if(isset($aAddData)){
            $data['additional'] = $aAddData;
        }
        $data['title'] = 'Immospider | Url Overview';
        $data['urls'] = $this->immospider_model->get_urls();
        $data['immoModel'] = $this->immospider_model;

        $this->load->view('templates/header', $data);
        $this->load->view('immospider/overview', $data);
        $this->load->view('templates/footer');
    }

    public function view($slug = NULL)
    {
        $data['urls'] = $this->immospider_model->get_urls();
    }

    public function create_url()
    {
        $sCreatedUrl = array('createdUrl' => $this->immospider_model->createImmo24Link($this->input->post('immoprice'),$this->input->post('immocity'),$this->input->post('immotype')));
        $this->index($sCreatedUrl);
    }

    public function save_url(){
        $sCURL = $this->input->post('createdUrl');
        $iCount = $this->immospider_model->getSearchCount($sCURL);
        if(isset($sCURL)){
            $this->immospider_model->add_url($sCURL,$iCount);
            $this->index();
        }else{
            $this->index();
        }
    }

    public function delete_url(){
        $sCURL = $this->input->post('uniqueURL');
        if(isset($sCURL)){
            $this->immospider_model->delete_url($sCURL);
            $this->index();
        }else{
            $this->index();
        }
    }


}