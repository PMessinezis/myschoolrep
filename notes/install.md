**Dev environment**
apt install php-pdo
apt install php-xml
apt install php-curl
apt install php-mbstring
apt install php-sqlite3
art key:generate
cd /tmp
curl -fsSL https://deb.nodesource.com/setup_19.x | sudo -E bash - &&sudo apt-get install -y nodejs
cd -
git clone git@github.com:PMessinezis/myschoolrep.git
cd myschoolrep
composer install
npm install
npm run dev
