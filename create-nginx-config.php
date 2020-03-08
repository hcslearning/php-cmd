<?php

print_r( $argv );

$hasta = intval( $argv[1] );

for($i = 1; $i <= $hasta; $i++) {
    $site = "s$i";
    $domain = "$site.ticbiz.cl";
    echo "$site => $domain \n";
    
    $template = <<<EOD
server {
    server_name $domain;
    root /var/www/wp/$site/wordpress;
    index index.php index.html;

    include /etc/nginx/default.d/*.conf;

    location ~ /\.(git|nginxpass) {
        deny all;
    }

    location @rewrite {
        rewrite ^ /index.php;
    }

    location / {
        try_files \$uri \$uri/ @rewrite;
    }

    listen 80;
    listen 443 ssl; # managed by Certbot
    ssl_certificate /etc/letsencrypt/live/ticbiz.cl/fullchain.pem; # managed by Certbot
    ssl_certificate_key /etc/letsencrypt/live/ticbiz.cl/privkey.pem; # managed by Certbot
    include /etc/letsencrypt/options-ssl-nginx.conf; # managed by Certbot
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem; # managed by Certbot
}
EOD;
    file_put_contents("${domain}.conf", $template);
}
