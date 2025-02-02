<h1>Deploy ses-dashboard to k8s</h1>

1. Build the php-fpm docker image:

```
docker build -t <my-repo-url>/php-fpm:latest -f docker/Dockerfile .
docker push <my-repo-url>/php-fpm:latest
```

2. Deploy:

```
kubectl apply -f .
```

3. Once deployed, exec into ses-php-fpm container and run the following command inside the working directory:

```
git clone https://github.com/Nikeev/sesdashboard.git && composer install --no-dev

```
**Note:** before installing dependencies using `composer install` command, you need to modify `config/bootstrap.php` file, by commenting the following line, otherwise it will throw an error regarding `.env` file, as we are passing environment variables using secrets we dont need `.env` file, delete existing `.env` file:

```
 (new Dotenv(false))->loadEnv(dirname(__DIR__).'/.env');
```
