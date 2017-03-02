<?php
class db
{
    private $connected = false;
    private $config=array();
    private $h = null; // db handle

    function __construct($config)
    {
        $this->config=$config;
    }

    private function config_ok()
    {
        $check=false;
        if(count($this->config)>0) {
            if(isset($this->config['db_server']) && 
                isset($this->config['db_name']) && 
                isset($this->config['db_user']) && 
                isset($this->config['db_pass']) && 
                isset($this->config['db_charset']))
            {
                $check=true;
            }
        }
        return $check;
    }

    private function connect()
    {
        if($this->config_ok()){
            try {
                $dsn='mysql:host='.$this->config['db_server'].';dbname='.$this->config['db_name'];
                $opt=array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                );
                $this->h = new PDO($dsn,$this->config['db_user'],$this->config['db_pass'],$opt);
                $this->h->query('SET NAMES '.$this->config['db_charset']);
                $this->connected=true;
                $this->config=array();
            } catch(PDOException $e) {
                app::log('err','Something wrong with DB connection');
                app::log('debug','[db]: '.$e->getMessage());
            }
        } else {
            app::log('err','db config error');
        }
    }

    public function ok()
    {
    	if(!$this->connected) $this->connect();
        //if($this->connected) app::log('debug','[db]: connected');
    	return $this->connected;
    }

    public function close()
    {
    	if($this->connected && $this->h!=null)
    	{
	        $this->h=null;
	        $this->connected=false;
            //app::log('debug','[db]: closed');
	    }
    }
/*
// not need here, because: app->stop() do this
    function __destruct()
    {
    	if($this->connected) $this->close();
    }
*/
}