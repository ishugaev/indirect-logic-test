## Steps to run the project

### 1. run build in the root directory of the project
```make build```

### 2. run the project
```make up```

### 3. install the dependencies
```make composer c=install```

### 4. run the migrations
```make sh``` will open shell in the php container

```php bin/console doctrine:migrations:migrate``` will run the migrations

```php bin/console doctrine:fixtures:load``` will load the fixtures

### 5. navigate to the following url, apply the certificate
```https://localhost/test```

### 6. run the tests
```make test```
