<?php

require_once "phing/Task.php";

/**
 * 指定された SQL 文を MySQL で実行します。
 *
 * sqlpath にディレクトリを指定した場合、そのディレクトリ内の全てのSQLを実行します。
 * このとき、順序に関係なく実行しますので、依存関係がないようにしてください。
 *
 *
 * @param string mysqlpath MySQL実行パス *必須
 * @param string username ユーザ名 *必須
 * @param string password パスワード *必須
 * @param string dbname データベース名
 * @param string sqlpath もしパスがディレクトリなら、そのディレクトリ内のSQLを全て実行します。 *必須
 */
class ExecMySQLTask extends Task {

	private $mysqlpath = null;
	public function setMysqlpath($str) {
		$this->mysqlpath = $str;
	}

	private $host = null;
	public function setHost($str) {
		$this->host = $str;
	}

	private $port = null;
	public function setPort($str) {
		$this->port = $str;
	}

	private $username = null;
	public function setUsername($str) {
		$this->username = $str;
	}

	private $password = null;
	public function setPassword($str) {
		$this->password = $str;
	}

	private $dbname = null;
	public function setDbname($str) {
		$this->dbname = $str;
	}

	private $sqlpath = null;
	public function setSqlpath($str) {
		$this->sqlpath = $str;
	}

	private $fileprefixfilter = null;
	public function setfileprefixfilter($str) {
		$this->fileprefixfilter = $str;
	}

	private $sql_file_names = null;

	/**
	 * The init method: Do init steps.
	 */
	public function init() {
	}

	/**
	 * The main entry point method.
	 */
	public function main() {
		$this->exec($this->sqlpath);		
	}

	public function exec($path) {
		if (is_dir($path)) {
			$files=array();
			$h = opendir($path);
			while($filename = readdir($h)){
				$files[]=$filename;
			}
			closedir($h);
			//アルファベット順ソート
			sort($files);
			foreach($files as $filename)
			{
				if(is_dir($path . '/' . $filename) && $filename !== '.' && $filename !== '..' ) {
					$this->exec($path . '/' . $filename);	
				} else if (preg_match('/\.sql$/', $filename)) {
					if (is_null($this->fileprefixfilter) || preg_match('/^' . $this->fileprefixfilter . '/', $filename)) {
						$this->execMysql($path . '/' . $filename);
					}
				}
			}
		} else if (preg_match('/\.sql$/', $path)) {
			if (is_null($this->fileprefixfilter) || preg_match('/^' . $this->fileprefixfilter . '/', $path)) {
				$this->execMysql($path);
			}
		} else {
			return;
		}
	}

	public function execMysql($filename) {

		if($this->password=="")
		{
			$cmd = $this->mysqlpath . ' '
				 . '-h ' . $this->host . ' '
				 . '-P ' . $this->port . ' '
				 . '-u ' . $this->username . ' ';
		} else {
			$cmd = $this->mysqlpath . ' '
				 . '-h ' . $this->host . ' '
				 . '-P ' . $this->port . ' '
				 . '-u ' . $this->username . ' '
				 . '-p' . $this->password  . ' ';
		}
		if ($this->dbname !== null 
		 && $this->dbname !== "" ) {
			$cmd .= '-D ' . $this->dbname . ' ';
		}

		$cmd .= '< ' . $filename;

		$last_line = system($cmd, $retval);

		if ($retval === 0) {
			print '     [execmysql] ('.$filename . ') is success.'."\n";
		} else {
			print '     [execmysql] ('.$filename . ') is failed.'."\n";
		}
		// print 'Last line of the output: ' . $last_line. "\n";
		// print 'Return value: ' . $retval;
	}

}

?>
