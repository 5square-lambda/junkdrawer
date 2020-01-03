$cmd = "bin\php\php5\php.exe -c .\etc\php.ini -d error_log=./log/php.log src\junkdrawer.php"

$cmd = $cmd & " " & " > log\backup.log"

Run($cmd, "", @SW_HIDE)
