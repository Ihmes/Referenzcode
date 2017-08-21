<?php

class CP_Google_Api extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->model('CP_Google_Api_Model');
        $this->load->helper('url_helper');
        $this->load->helper('html');
        $this->load->helper('form_helper');

    }

    public function index(){

    }

}