# 99speedmart
# Installing inside the Ec2

#!/bin/bash
yum update -y
amazon-linux-extras install -y php8.2
yum install -y httpd
yum install -y git.x86_64
yum install -y mariadb.x86_64
yum install -y php-mbstring php-xml php-pdo php-curl php-openssl php-gd php-zip
