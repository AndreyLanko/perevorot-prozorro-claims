composer create-project october/october projectfoldername dev-master<br /> 
setup mysql connection at config/database.php<br /> 
php artisan october:up <br /> 
find storage -type f -exec chmod 664 {} \;<br /> 
find storage -type d -exec chmod 777 {} \;<br /> 
find themes -type f -exec chmod 664 {} \;<br /> 
find themes -type d -exec chmod 777 {} \;<br /> 
find plugins -type f -exec chmod 664 {} \;<br /> 
find plugins -type d -exec chmod 777 {} \;<br /> 