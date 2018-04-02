wget https://github.com/asterion-cms/asterion/archive/master.zip
rm -rf app
rm index.php
rm robots.txt
rm .htaccess
unzip master.zip
rm master.zip
cp -r asterion-master/* .
rm -rf asterion-master
