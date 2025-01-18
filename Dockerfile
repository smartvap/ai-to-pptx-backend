# 使用官方的 PHP 8.2 镜像，并包含 Apache
FROM php:8.2-apache

# 安装所需的 PHP 扩展
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libcurl4-openssl-dev \
    unzip \
    && docker-php-ext-install zip curl pdo_mysql

# 安装 Redis 扩展
RUN pecl install redis && docker-php-ext-enable redis

# 启用 Apache 的 rewrite 模块
RUN a2enmod rewrite

# 设置工作目录
WORKDIR /var/www/html

# 复制代码到容器中
COPY . .

# 暴露端口 80（Apache 默认端口）
EXPOSE 80
