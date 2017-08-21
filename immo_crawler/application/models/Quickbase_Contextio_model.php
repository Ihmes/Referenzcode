<?php
/**
 * Created by PhpStorm.
 * User: Markus Bühler
 * Date: 08.01.16
 * Time: 18:50
 */


include_once(BASEPATH."libraries/External/context.io/class.contextio.php");

class Quickbase_Contextio_model extends CI_Model{

    protected static $contextIO = NULL;
    protected static $accountId = 'xxxxx';
    protected static $aMails = array('xxxxxx',
                                     'xxxxxx',);


    public function __construct()
    {
        $this->load->database();
        $this->load->helper('url_helper');
        $this->load->helper('html');
        $this->load->helper('form_helper');
        if(!isset(self::$contextIO)){
            self::$contextIO = new ContextIO('skg4iy4o','TpKYi2nPscHFBpiY');
        }

    }


    public function cio_login(){

        $accountId = null;

        // list your accounts
        $r = self::$contextIO->listAccounts();
        foreach ($r->getData() as $account) {
            if (is_null($accountId)) {
                $accountId = $account['id'];
            }
        }

        if (is_null($accountId)) {
            die;
        }

        return self::$contextIO;
    }

    public function cio_get_attachments($accountId = NULL, $sFiletype = NULL){


        $saveToDir = "/var/www/vhosts/xxxx.de/httpdocs/immocrawler/export/";
        mkdir($saveToDir, 0777);
        $args = array('from'=>self::$aMails,'email'=>self::$aMails);

        $r = self::$contextIO->listFiles(self::$accountId, $args);
        foreach ($r->getData() as $document) {
            echo "Downloading attachment '" . $document['file_name'] . "' to $saveToDir ... <br><br>";
            self::$contextIO->getFileContent(self::$accountId, array('file_id' => $document['file_id']), $saveToDir . "/" . $document['file_name']);
        }
    }


}
