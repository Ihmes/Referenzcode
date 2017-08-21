<?php

class Immospider_model extends CI_Model{

    public function __construct()
    {
        $this->load->database();
    }

    public function get_urls()
    {
            $query = $this->db->get('urls2crawl');
            return $query->result_array();
    }

    public function add_url($sURL,$iCount = NULL){

        $this->load->helper('url');

        $data = array(
            'URL' => $sURL,
            'ITEMCOUNT' => $iCount,
        );

        try{
            if(!$this->db->insert('urls2crawl', $data)){
                throw new Exception('URL Duplikat');
            }

        }catch (Exception $e){
            echo  $e->getMessage();
        }
    }

    public function delete_url($sURL){
        $sql = "DELETE FROM urls2crawl WHERE URL='".$sURL."'";
        $this->db->query($sql);
    }

    public function createImmo24Link($iPrice = NULL, $sCity = NULL, $sType = NULL){

        $sURL = "http://www.immobilienscout24.de/Suche/S-2/$sType/$sCity/$sCity/-/-/-/EURO--$iPrice?enteredFrom=result_list";
        return $sURL;

    }

    public function getSearchCount($sURL){
        $html = file_get_contents($sURL);
        $first_step = explode( '<span id="resultCount" class="font-normal">' , $html );
        $second_step = explode("</span>" , $first_step[1] );
        return $second_step[0];
    }
}
