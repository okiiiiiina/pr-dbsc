# PHP + Apache ベースイメージ
FROM php:8.2-apache

# Apache設定ファイルを差し替える（必要なら）
COPY etc/apache2/apache2.conf /etc/apache2/apache2.conf

# ドキュメントルートにHTML/PHPファイルを配置
WORKDIR /var/www/html
COPY ./src /var/www/html

# 必要なPHP拡張を追加（例: mysqli）
# RUN docker-php-ext-install mysqli

# Apacheはベースイメージで自動起動されるため、CMDは不要