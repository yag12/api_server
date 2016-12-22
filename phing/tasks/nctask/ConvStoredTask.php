<?php

require_once "phing/Task.php";

/**
 * 指定された SQL にエラーコードの置換処理を行います。
 *
 * @param string perlpath perl パス
 * @param string convpath 置換処理パス *必須
 * @param string sqlpath もしパスがディレクトリなら、そのディレクトリ内のSQLを全て対象とします。 *必須
 * @param string outpath 出力先ファイルパス *必須
 */
class ConvStoredTask extends Task {
	
	private $perlpath = null;
	public function setPerlpath($str) {
		$this->perlpath = $str;
	}

	private $convpath = null;
	public function setConvpath($str) {
		$this->convpath = $str;
	}
	
	private $labelpath0 = null;
	public function setLabelpath0($str) {
		$this->labelpath0 = $str;
	}

	private $labelpath1 = null;
	public function setLabelpath1($str) {
		$this->labelpath1 = $str;
	}

	private $labelpath2 = null;
	public function setLabelpath2($str) {
		$this->labelpath2 = $str;
	}

	private $labelpath3 = null;
	public function setLabelpath3($str) {
		$this->labelpath3 = $str;
	}

	private $labelpath4 = null;
	public function setLabelpath4($str) {
		$this->labelpath4 = $str;
	}

	private $sqlpath = null;
	public function setSqlpath($str) {
		$this->sqlpath = $str;
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
		
		if (! is_dir($this->outpath)) {
			print '  error! ' .$this->outpath . ' is not a directory.';
			return;
		} 
		
		$this->exec($this->sqlpath);		
	}

	public function exec($path) {
		if (is_dir($path)) {
			$h = opendir($path);
			while($filename = readdir($h)){
				if(is_dir($path . '/' . $filename) && $filename !== '.' && $filename !== '..' ) {
					$this->exec($path . '/' . $filename);	
				} else if (preg_match('/\.sql$/', $filename)) {
					$this->execConv($path . '/' . $filename);	
				}
			}
			closedir($h);
		} else if (preg_match('/\.sql$/', $path)) {
			$this->execConv($path);	
		} else {
			return;
		}
	}

	public function execConv($filename) {
		
		$cmd = $this->perlpath !== null 
			 ? $this->perlpath.' '
			 : 'perl ';
		
		$cmd .= '"' . $this->convpath  . '" '
			  . '"' . $this->labelpath0 . '" '
			  . '"' . $this->labelpath1 . '" '
			  . '"' . $this->labelpath2 . '" '
			  . '"' . $this->labelpath3 . '" '
			  . '"' . $this->labelpath4 . '" '
			  . '"' . $filename        . '" '
			  . '"' . $this->outpath . '/' . basename($filename) . '" ';
//		print "$cmd"."\n";
		$last_line = system($cmd, $retval);
		if ($retval === 0) {
			print '     [convstored] ('.$filename . ') is success.'."\n";
		} else {
			print '     [convstored] ('.$filename . ') is failed.'."\n";
		}
		// print 'Last line of the output: ' . $last_line. "\n";
		// print 'Return value: ' . $retval;
	}

}

?>
