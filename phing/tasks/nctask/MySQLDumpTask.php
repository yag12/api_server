<?php

require_once "phing/Task.php";

/**
 * mysqldump を実行します。
 *
 *
 * @param string mysqldumppath mysqldump実行パス *必須
 * @param string username ユーザ名 *必須
 * @param string password パスワード *必須
 * @param string dbname データベース名
 * @param string outpath 出力先ファイルパス *必須
 */
class MySQLDumpTask extends Task {

	private $mysqldumppath = null;
	public function setMysqldumppath($str) {
		$this->mysqldumppath = $str;
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

	private $outpath = null;
	public function setOutpath($str) {
		$this->outpath = $str;
	}

	/**
	 * The init method: Do init steps.
	 */
	public function init() {
	}

	/**
	 * The main entry point method.
	 */
	public function main() {
		
		print '  [mysqldump] start.'."\n";
		
		$cmd = $this->mysqldumppath . ' '
			 . '-h ' . $this->host . ' '
			 . '-P ' . $this->port . ' '
			 . '-u ' . $this->username . ' '
			 . '-p' . $this->password  . ' ';
	
		if ($this->dbname !== null) {
			$cmd .= ' -B ' . $this->dbname . ' ';
		} else {
			$cmd .= '--A ';
		}
		
		$filename = 'mysqldump-' . date('YmdHis') . '.sql';

		$cmd .= '> ' . $this->outpath . '/' . $filename;

		$last_line = system($cmd, $retval);
	
		if ($retval === 0) {
			print '  [mysqldump] mysqldump is success.'."\n";
		} else {
			print '  [mysqldump] mysqldump is failed.'."\n";
		}
		// print 'Last line of the output: ' . $last_line. "\n";
		// print 'Return value: ' . $retval;
		
	}
}

?>
