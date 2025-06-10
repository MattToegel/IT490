#!/usr/bin/env bash
sudo apt update
sudo apt install -y php php-bcmath php-amqp rabbitmq-server openssh-server git composer
sudo systemctl enable --now rabbitmq-server
sudo systemctl enable --now ssh
if ! id -u hs747 &>/dev/null; then
  sudo adduser --gecos "" --disabled-password hs747
  echo "hs747:Farzaneh" | sudo chpasswd
  sudo usermod -aG sudo hs747
fi
if [ ! -d "/home/hs747/IT490" ]; then
  sudo -u hs747 git clone https://github.com/MattToegel/IT490.git /home/hs747/IT490
fi
sudo -u hs747 bash -c "cd /home/hs747/IT490 && composer install"

echo "Setup complete! You can now run the RabbitMQ samples as hs747."
