[![CircleCI](https://circleci.com/gh/jakubgg/shopping-basket/tree/circleci-project-setup.svg?style=shield)](https://circleci.com/gh/jakubgg/shopping-basket/tree/circleci-project-setup)

# Shopping Basket PoC for Acme Widget Co

This is just a proof of concept of Shopping Basket implementation. 

## Installation

This project requires PHP v8.0.10 or higher. 
If you do not have it in your environment please use the included docker container.
The docker container will also help you with debugging as it comes with Xdebug preconfigured and turned on.

If you do not intend to use the docker container provided you can jump to the `Using project without Docker` section.

#### Docker setup
Adjust the `.env` (use `.env.example` as source) if you need to provide `IDE KEY` for debugging with your IDE.

Run following command in the project directory:

```bash
docker-compose build
```

If all went well and you see something like `[+] Building 10.1s (10/10) FINISHED ` the docker image was built successfully.
Now you should run `Composer` and install project dependencies:

```bash
docker-compose run --rm dev-box composer install
```

Once that is done you are ready to run project tests and examples. 

### Running tests in Docker
Since running tests is a common occurrence there is a `tests.sh` file that you can execute to run included tests.
Check if the file has `execute` -`x` permissions `ls -la ./tests.sh`). 

```bash
-rwxr-xr-x  1 user  group  10  9 Sep 22:54 ./tests.sh
```
If it does not, run `chmod +x ./tests.sh`, and it should be good to go. 

If you would rather run the tests without the bash file, that is also fine. Just type:

```bash
docker-compose run --rm dev-box ./vendor/bin/phpunit tests --testdox --colors
```
The last two parameters `--testdox --colors` are optional, they improve visual quality of test output.

### Running examples in Docker
There is an `examples.php` file located in the root folder of the project. 
It provides some code examples and acts as an easy point of entry if you would like to debug the code with Xdebug. 

To run it, type in your terminal:
```bash
docker-compose run --rm dev-box php -f examples.php
```

### Setting project without Docker
To use the project without Docker you need the correct version of PHP installed, composer and optionally Xdebug installed and configured. 

Clone the project from Github. 
```
git clone https://github.com/jakubgg/shopping-basket.git
```

Run `composer install` in the project folder,  that will install all project requirements. 
To run tests type `./vendor/bin/phpunit tests --testdox --colors`.
To run examples type `php -f examples.php` in the project folder and enjoy the output.


## Usage

### Project structure
The main code is in the `./src` directory which is also a root for the `Basket\` namespace.
./src/`ShoppingBasket.php` is the main class of the shopping basket. 
./src/Offers - directory contains `Offer` interface and offers logic. 
./src/Shipping - directory contains `Shipping` interface and shipping rates logic.
./tests - directory contains project tests. 

### Class usage
To instantiate the `ShoppingBasket` class:
```php
$shopping_basket = new ShoppingBasket();
```
It is not necessary to initialise the class on instantiation, as it also exposes the `init()` method that takes the same signature. 

To be able to obtain shipping rates, or basket values, you will need to initialise `ShoppingBasket`.
The constructor (or `init()`) accepts three parameters `$product_catalogue`, `$shipping_rates`, `$offers`. 
Out of these three the `$product_catalogue` and `$shipping_rates` are necessary for the class to work and return correct values.

`$product_catalogue` - is an array with product name, code and price. The price is an integer in cents to avoid issues with operations on floats. 
```php
[
    'product' =>'Red Widget',
    'code' =>'R01',
    'price' => 3295,
],
```
`$shipping_rates` and `$offers` are objects of type `Shipping` and `Offer` respectively, that provide logic for applying shipping rates and special offers. 

To initialise the Shopping Basket:
```php
$shopping_basket->init(product_catalogue: $products, shipping_rates: new StandardShipping(), offers: new RedOffer());
```

The class exposes a fluent interface that allows easy operation e.g.:
Add one item to the basket:
```php
$shopping_basket->add('R01');
```

Add two or more items to the basket:
```php
$shopping_basket->add('R01')->add('B01')->add('G01');
```

Add two items to the basket and request basket total, without shipping:
```php
$value = $shopping_basket->add('R01')->getBasketValue();
```

Add three items to the basket and request grand total with offer applied (remember to initialise the basket with `Shipping` rules and `Offer` rules, otherwise it will complain):
```php
$shopping_basket->add('R01')->add('B01')->add('G01')->getTotal();
```


## Additional

#### Configure Xdebug in Visual Studio Code 
Install `PHP Debug` plugin (`felixfbecker.php-debug`).
Go to `Run` -> `Open Configurations`, which should bring the `launch.json` config file.

in the `"name": "Listen for Xdebug",` section add port and mappings : 

```json
"port": 9000,
"pathMappings": {
    "/app": "${workspaceRoot}"
}
```

The whole section should look something like this:

```json
{
    "name": "Listen for Xdebug",
    "type": "php",
    "request": "launch",
    "port": 9000,
    "pathMappings": {
        "/app": "${workspaceRoot}"
    }
},
```

_Note: By default Xdebug 3.x uses port 9003, but I have changed it to use the old 9000 (in Docker setup and here) to be compatible with old setups._

Mark some breakpoints in your script and press `F5` or go to `Run` -> `Start Debugging`. That should bring out the debug console and start listening on the port 9000 for Xdebug. 

Now run your script and VSC should stop execution when the code hits the break point. 


## License
[MIT](https://choosealicense.com/licenses/mit/)