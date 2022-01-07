# Como habilitar a Apache para hacer deploys 

1. Loguearse como Apache
```
sudo -u apache -s
```
2. Crear deploy keys 
```
ssh-keygen -t rsa 
```
3. Agregar github.com a known_hosts
```
ssh-keyscan -t rsa github.com >> ~/.ssh/known_hosts
```



# Ejemplo deploy_script.php
```
_command("git checkout .");
_command("git checkout {$_ENV['BRANCH']}");
_command("git pull");
_command("composer install");
_command("npm install");
_command("chmod -R 777 public");
```