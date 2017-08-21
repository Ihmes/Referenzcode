<?php

class Immocrons_model extends CI_Model{

    public function __construct()
    {
        $this->load->database();
    }

    public function get_new_object_count($aUrls){

        foreach($aUrls as $UrlList){

        $counter = NULL;
        $str = file_get_contents($UrlList['URL']);

        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($str);
        libxml_clear_errors();
        $selector = new DOMXPath($doc);

        $result = $selector->query('//li[@data-item="result"]');

        // Update total Amount
        $this->immocrons_model->update_total_items($UrlList['URL']);

        foreach($result as $node) {

            if($counter == 0){
                $sql = "UPDATE urls2crawl SET LASTEXPOSE='".$node->getAttribute('data-obid')."' WHERE URL='".$UrlList['URL']."'";
                $this->db->query($sql);
            }

            if($node->getAttribute('data-obid') == $UrlList['LASTEXPOSE'])
            {
                break;
            }else{
                $counter++;
            }
        }
            $sql = "UPDATE urls2crawl SET NEWOBJECTCOUNT='".$counter."' WHERE URL='".$UrlList['URL']."'";
            $this->db->query($sql);
        }
    }

    public function update_total_items($sURL){
        $this->load->helper('url');
        $iCount = $this->immospider_model->getSearchCount($sURL);

        try{
            $sql = "UPDATE urls2crawl SET ITEMCOUNT='".$iCount."' WHERE URL='".$sURL."'";
            ;
            if(!$this->db->query($sql)){
                throw new Exception('Itemcount not updated.');
            }

        }catch (Exception $e){
            echo  $e->getMessage();
        }
    }

}
