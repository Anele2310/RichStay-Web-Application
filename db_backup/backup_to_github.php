<?php
//connect to database
$host = "localhost";
$user = "root";
$pass = "";
$dbName = "rental_portal";

//Git hub repo and branch details
$githubRepo = "https://github.com/Viddy-8112/ITProject_Pages-.git";
$branch = "DATABASE-DATA-BACKUP";

//creating the backup file
$backupFile = "backup_" . date("Y-m-d_H-i-s") . ".sql";
$command = "C:\\xampp\\mysql\\bin\\mysqldump.exe --user=$user --password=$pass --host=$host $dbName > C:\\xampp\\htdocs\\db_backup\\$backupFile";
system($command, $output);

// push to github
chdir("C:\\xampp\\htdocs\\db_backup");
exec("git init");
exec("git remote add origin $githubRepo");
exec("git checkout -b $branch");
exec("git add $backupFile");
exec('git commit -m "Automated backup: ' . date("Y-m-d H:i:s") . '"');
exec("git push -u origin $branch --force");

echo " Database has been backedup successfully to GithUB";
?>
