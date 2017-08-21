<?php

include_once(BASEPATH."libraries/External/quickbase/quickbase.php");

class Quickbase_Mailreader_model extends CI_Model {

    public $conn;

    private $inbox;
    private $msg_cnt;

    private $server = "imap.gmail.com:993";
    private $user   = 'XXXXXXXXXX';
    private $pass   = 'XXXXXXXXXX';

    function __construct() {
        $this->connect();
        $this->inbox();
    }

    function close() {
        $this->inbox = array();
        $this->msg_cnt = 0;

        imap_close($this->conn);
    }

    function connect() {
        $this->conn = imap_open('{'.$this->server.'/imap/ssl}INBOX', $this->user, $this->pass);
    }

    function move($msg_index, $folder='INBOX.Processed') {
        imap_mail_move($this->conn, $msg_index, $folder);
        imap_expunge($this->conn);

        $this->inbox();
    }

    function get($msg_index=NULL) {
        if (count($this->inbox) <= 0) {
            return array();
        }
        elseif ( ! is_null($msg_index) && isset($this->inbox[$msg_index])) {
            return $this->inbox[$msg_index];
        }

        return $this->inbox[0];
    }

    function inbox() {
        $this->msg_cnt = imap_num_msg($this->conn);

        $in = array();
        for($i = 1; $i <= $this->msg_cnt; $i++) {
            $in[] = array(
                'index'     => $i,
                'header'    => imap_headerinfo($this->conn, $i),
                'body'      => imap_body($this->conn, $i,FT_PEEK),
                'structure' => imap_fetchstructure($this->conn, $i)
            );
        }

        $this->inbox = $in;
    }

    function email_pull($sMailflag = 'UNSEEN',$iExportToQB = 0,$bSandboxmode = false){

        $emails = imap_search($this->conn,$sMailflag);

        /* useful only if the above search is set to 'ALL' */
        $max_emails = 16;


        /* if any emails found, iterate through each email */
        if($emails) {

            $count = 1;

            /* put the newest emails on top */
            rsort($emails);

            /* for every email... */
            foreach($emails as $email_number)
            {

                /* get mail structure */
                $structure = imap_fetchstructure($this->conn, $email_number);

                $attachments = array();

                /* if any attachments found... */
                if(isset($structure->parts) && count($structure->parts))
                {
                    for($i = 0; $i < count($structure->parts); $i++)
                    {
                        $attachments[$i] = array(
                            'is_attachment' => false,
                            'filename' => '',
                            'name' => '',
                            'attachment' => ''
                        );

                        if($structure->parts[$i]->ifdparameters)
                        {
                            foreach($structure->parts[$i]->dparameters as $object)
                            {
                                if(strtolower($object->attribute) == 'filename')
                                {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['filename'] = $object->value;
                                }
                            }
                        }

                        if($structure->parts[$i]->ifparameters)
                        {
                            foreach($structure->parts[$i]->parameters as $object)
                            {
                                if(strtolower($object->attribute) == 'name')
                                {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['name'] = $object->value;
                                }
                            }
                        }

                        if($attachments[$i]['is_attachment'])
                        {
                            $attachments[$i]['attachment'] = imap_fetchbody($this->conn, $email_number, $i+1,FT_PEEK);

                            /* 3 = BASE64 encoding */
                            if($structure->parts[$i]->encoding == 3)
                            {
                                $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                            }
                            /* 4 = QUOTED-PRINTABLE encoding */
                            elseif($structure->parts[$i]->encoding == 4)
                            {
                                $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                            }
                        }
                    }
                }

                /* iterate through each attachment and save it */
                foreach($attachments as $attachment)
                {
                    if($attachment['is_attachment'] == 1)
                    {
                        $filename = $attachment['name'];
                        if(empty($filename)) $filename = $attachment['filename'];

                        if(empty($filename)) $filename = time() . ".dat";

                        /* prefix the email number to the filename in case two emails
                         * have the attachment with the same file name.
                         */

                        if(strpos($attachment['filename'],'.xml'))
                        {
                            $fp = fopen("/var/www/vhosts/xxxx.de/httpdocs/immocrawler/export/" . $email_number . "-" . $filename, "w+");
                            $sFilePath = "/var/www/vhosts/xxxx.de/httpdocs/immocrawler/export/" . $email_number . "-" . $filename;

                            fwrite($fp, $attachment['attachment']);
                            fclose($fp);

                            //call Xml Exporter to Quickbase
                            if($iExportToQB === true){
                                $this->export_to_qb($sFilePath);
                            }
                        }

                    }

                }

                if($count++ >= $max_emails) break;
            }

        }
    }

    function export_to_qb($sFilepath, $bSandboxmode = false){


        $xml = simplexml_load_file($sFilepath);

        if(!$bSandboxmode && $xml->sender->name == 'IS24'){
            $oQB = new QuickBase('XXXXXX','XXXXXX',true, '', '', 'XXXXXX');

            // DB: CRM Anfragen ID XXXXXXX
            $oQB->set_database_table('XXXXXXX');

            $sClearAddText = htmlentities($xml->objekt->interessent->anfrage);

            $ort = explode(" ",$xml->objekt->interessent->ort);
            $oQB->import_from_csv(
                $xml->objekt->portal_obj_id.",". //ScoutID
                $xml->objekt->oobj_id.",".  //ObjektID
                $xml->sender->datum.",". //Datum
                $xml->objekt->interessent->anrede.",".  //Anrede
                $xml->objekt->interessent->vorname.",". //Vorname
                $xml->objekt->interessent->nachname.",".  //Nachname
                $xml->objekt->interessent->email.",".  //Email
                $xml->objekt->interessent->tel.",".  //Telefon
                'NEU,'.
                $xml->objekt->interessent->strasse.",".
                $ort[1].",".
                $ort[0],
                '18.19.20.21.22.23.28.27.69.71.73.75');
        }

    }

}

?>
