<?php

class public_suffix_json{
    private $url = '';
    private $localfile = '';
    private $destination_dir = '';
    private $destination_file = '';

    public function __construct(){
	$this->url = 'https://publicsuffix.org/list/public_suffix_list.dat';
	$this->localfile = __DIR__ . '/local.dat';
	$this->destination_dir = __DIR__ . '/repo';
	$this->destination_file = $this->destination_dir . '/public_suffix_list.json';
    }	    

    public function populate_data(){
	$local_time = 0;
	if(file_exists($this->localfile)){
	    $local_time = $this->get_local_time();
	}
	
	$remote_time = $this->get_remote_time();
        if($remote_time == $local_time){
                echo date('[d/m/Y H:i:s] ') . 'Already Up-To-Date' . PHP_EOL;
                exit;
	}

	echo date('[d/m/Y H:i:s] ') . 'Downloading Dat file' . PHP_EOL;
        $this->downloadFile();

	//$download_new = false;
        //if(!file_exists($this->localfile)){
	//    $this->downloadFile();
	//    //$download_new = true;	
        //}

	if(!file_exists($this->localfile)){
	    echo date('[d/m/Y H:i:s] ') . 'ERROR: Invalid dat file!' . PHP_EOL;
	    exit;
        }

	//if(false == $download_new){
	//    $local_time = $this->get_local_time();
        //    $remote_time = $this->get_remote_time();
	//    if($remote_time == $local_time){
	//        echo date('[d/m/Y H:i:s] ') . 'Already Up-To-Date' . PHP_EOL;
	//	exit;    
        //    }
	//}

	//echo date('[d/m/Y H:i:s] ') . 'Downloading Dat file' . PHP_EOL;
	//$this->downloadFile();

	echo date('[d/m/Y H:i:s] ') . 'Populating json file' . PHP_EOL;
        $obj = new StdClass;
        $data = $this->parse_dat_file();
        foreach($data as $type => $value){
            $obj->$type = $value;
        }

        $contents = json_encode($obj);
        return file_put_contents($this->destination_file, $contents);
    }

    public function git_sync(){
	echo date('[d/m/Y H:i:s] ') . 'Syncing GIT Repo' . PHP_EOL;
	$cmd = 'cd '.$this->destination_dir.' && (';
        $cmd .= 'git checkout master;';
	$cmd .= 'git add .;';
	$cmd .= 'git pull origin master;';
	$cmd .= 'git commit -m "Automatic json file update";';
        $cmd .= "git push origin master;";
        $cmd .= ')';

	passthru($cmd);
	//echo $cmd;
    }

    private function downloadFile(){
	echo date('[d/m/Y H:i:s] ') . 'Downloading DAT raw file' . PHP_EOL;
        $cmd = '$(which wget) ' . $this->url . ' -O ' . $this->localfile;
        return shell_exec($cmd . ' 2>&1');
    }

    private function get_local_time(){
	echo date('[d/m/Y H:i:s] ') . 'Getting local DAT file mtime' . PHP_EOL;
        return (int) @filemtime($this->localfile);
    }

    private function get_remote_time(){
	echo date('[d/m/Y H:i:s] ') . 'Getting remote DAT file mtime' . PHP_EOL;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_FILETIME, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        return (int) $info['filetime'];
    }

    private function parse_dat_file(){
	echo date('[d/m/Y H:i:s] ') . 'Parsing local DAT file' . PHP_EOL; 
        $handle = fopen ($this->localfile, "r");

        $flag = '';
        $ret = array();
        while (($buffer = fgets($handle, 4096)) !== false) {
            if(preg_match('#^$#', $buffer)){
                continue;
            }

            if(preg_match('#BEGIN ICANN DOMAINS#i', $buffer)){
                $flag = 'icann';
            }

            if(preg_match('#BEGIN PRIVATE DOMAINS#i', $buffer)){
                $flag = 'private';
            }

            if(preg_match('#^//#', $buffer)){
                continue;
            }

            if(preg_match('#^\*\.(.*)#', $buffer, $output)){
                $buffer = $output[1];
                unset($output);
            }

            $ret[$flag][] = trim($buffer);
        }

        return $ret;
    }
}
