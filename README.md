composer create-project october/october projectfoldername dev-master
setup mysql connection at config/database.php
php artisan october:up 
find storage -type f -exec chmod 664 {} \;
find storage -type d -exec chmod 775 {} \;
find themes -type f -exec chmod 664 {} \;
find themes -type d -exec chmod 775 {} \;
find plugins -type f -exec chmod 664 {} \;
find plugins -type d -exec chmod 775 {} \;