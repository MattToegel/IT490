#!/bin/bash 

echo "==== Updating system and installing dependencies ==="

sudo apt update && sudo apt upgrade 

echo "=== Starting RabbitMQ and enabling it at boot ==="
sudo systemctl enable rabbitmq-server
sudo systemctl start rabbitmq-server

echo "=== Installing Composer ==="
sudo apt install composer 
composer update 

echo "=== Cloning IT490 repo ==="

if [! -d "$HOME/IT490"]; then
git clone https://github.com/MattToegel/IT490.git "$HOME/IT490"
fi 


echo "=== Installing PHP dependencies via Composer ==="
cd "$HOME/IT490"
composer install


echo "=== Setup complete! ===" 