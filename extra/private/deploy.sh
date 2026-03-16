#!/bin/bash

set -e

extra="https://raw.githubusercontent.com/dwilding/oranguru/refs/heads/main/extra"

cd /home/protected
wget -O install_oranguru.sh "$extra/install_oranguru.sh"
chmod +x install_oranguru.sh
./install_oranguru.sh /home/public --overwrite

cd /home/public
wget -O install_oranguru.sh "$extra/install_oranguru.sh"
wget -O sitemap.xml "$extra/sitemap.xml"
wget -qO- "$extra/extra.htaccess" >> .htaccess
sed -i'.backup' 's/data-registration=\"short\"/data-registration=\"long\"/' help.html
rm *.backup

echo "Building changes.xml…"
cd /home/private
. .venv/bin/activate
./build_changes.py
deactivate
echo "Done!"
