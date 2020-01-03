<?php
class Junkdrawer {
    private $path;
    private $days;
    private $name;
    
    private $it;
    
    public function __construct($name, $path, $days) {
        $this->name = $name;    
    
        $days = (int) $days;
        if ($days <= 0) throw new Exception("Days has to be a positive integer, but was [$days]");
        if (!is_dir($path)) throw new Exception("path [$path] for junkdrawer [$name] is not valid");

        $this->path = $path;
        $this->days = $days;
        $this->it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
    }
    
    private function isEmptyDir($dir){
        return (($files = scandir($dir)) && count($files) <= 2);
    } 
    
    public function clean() {
        $it = $this->it;
        
        $tooOld = strtotime("-{$this->days} days");
        
        $notDeleted = array();
        while($it->valid()) {
        
            if (!$it->isDot()) {
                if ($it->getMTime() < $tooOld) {
                    $res = @unlink($it->key());
                    if (!$res) {
                    	logger('File could not be deleted: ' . $it->key());
                    	if (!isset($notDeleted[$it->key()])) {
                    		$notDeleted[$it->key()] = true;
                    		logger('change access rights and try to delete again...');
                    		chmod($it->key(), 0777);
                    		continue;
                    	}
                    	logger('Not deleted. Skipping...');
                    }
                    else {
                    	logger('File deleted: ' . $it->key());	
                    }
                }
            }
            
            if ($it->isDir() && $this->isEmptyDir($it->key())) {
                unlink($it->key());
            }
        
            $it->next();
        }
        
        $this->cleanDirs($this->path);
    }
    
    public function cleanDirs($path) {
        $iterator = new DirectoryIterator($path);

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isDot()) continue;
            if ($fileinfo->isFile()) continue;
            
            $this->cleanDirs($fileinfo->getPathname());
            if ($this->isEmptyDir($fileinfo->getPathname())) {
                chmod($fileinfo->getPathname(), 0755);
                $deleted = @rmdir($fileinfo->getPathname());
                if ($deleted) logger('Dir removed: '.$fileinfo->getPathname());
            }
        }
    }
}

class JunkdrawerException extends Exception {}