# 使用官方的 PHP 8.2 镜像，并包含 Apache
FROM php:8.2-apache

# 安装所需的 PHP 扩展
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libcurl4-openssl-dev \
    unzip \
    redis \
    && docker-php-ext-install zip curl pdo_mysql

# 安装 Redis 扩展
RUN pecl install redis && docker-php-ext-enable redis

# 启用 Apache 的 rewrite 模块
RUN a2enmod rewrite

# 设置工作目录
RUN mkdir -p /var/www/html/aipptx
WORKDIR /var/www/html/aipptx

# 复制代码到容器中
COPY . .

# 安装 git Node.js 和 npm
RUN apt-get update && apt-get install -y git nodejs npm

# 克隆 ai-to-pptx 项目到 /var/www/html/ai-to-pptx
RUN mkdir -p /var/www/html/ai-to-pptx
RUN git clone https://github.com/chatbookai/ai-to-pptx.git /var/www/html/ai-to-pptx

# 安装 ai-to-pptx 项目的依赖
WORKDIR /var/www/html/ai-to-pptx
RUN npm install
RUN npm run build
RUN mv /var/www/html/ai-to-pptx/webroot/* /var/www/html

# 暴露端口 80（Apache 默认端口）和 6379（Redis 默认端口）
EXPOSE 80 6379 3000

# 启动 Apache 和 Redis
CMD redis-server --daemonize yes && apache2-foreground
