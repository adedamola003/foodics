<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Ordering System
This is a Laravel application for a food ordering system. The application has three main models: Product, Ingredient, and Order.
A burger (Product) can have several ingredients with their respective amounts, and the system keeps track of the stock levels of each ingredient in the database.

## Requirements
PHP >= 8.0
Composer
Mysql


## Installation Guide
- Clone the repository: git clone https://github.com/adedamola003/foodics.git
- Navigate to the project directory: cd repo
- Install dependencies: 'composer install'
- create a .env file based on .env.example
- run composer install
- create a key (php artisan key:generate)
- run migrations (php artisan migrate).
- run php artisan db:seed
- Documentation [Documentation](https://documenter.getpostman.com/view/23520495/2s93CEyHmE).
- clear cache (php artisan optimize)
- clear config (php artisan config:clear)
- run migrations for testing (php artisan migrate --env=testing)


#Usage
To place an order, send a POST request to the /v1/order/create endpoint with the order details in the request payload. The payload should be in the following format:
{
    "products": [
        {
        "product_id": 1,
        "quantity": 2
        },
        {
        "product_id": 2,
        "quantity": 1
        }
    ]
}

When an order is placed, the system updates the stock levels of the ingredients based on the amounts consumed.
If the stock level of an ingredient falls below 50%, an email is sent to alert the merchant to buy more of that ingredient.

##Testing
To run the tests, run the following command: php artisan test

##API Documentation
The API documentation can be found here: https://documenter.getpostman.com/view/23520495/2s93CEyHmE

##Database Schema
products
- id: primary key
- name: string
- price: decimal
- status: enum
- created_at: timestamp
- updated_at: timestamp

ingredients
- id: primary key
- name: string
- stock_unit: string
- max_stock: decimal
- warning_threshold: decimal
- created_at: timestamp
- updated_at: timestamp

ingredient_usages
- id: primary key
- order_id: foreign key
- ingredient_id: foreign key
- quantity: decimal
- balance: decimal
- unit: string
- usage_type: enum (credit, debit)
- created_at: timestamp
- updated_at: timestamp

product_ingredients
- id: primary key
- product_id: foreign key referencing products.id
- ingredient_id: foreign key referencing ingredients.id
- quantity: decimal
- quantity_unit: string
- created_at: timestamp
- updated_at: timestamp

orders
- id: primary key
- customer_name: string
- user_id: foreign key referencing users.id
- order_date: date
- created_at: timestamp
- updated_at: timestamp

order_items
- id: primary key
- order_id: foreign key referencing orders.id
- product_id: foreign key referencing products.id
- quantity: integer
- created_at: timestamp
- updated_at: timestamp

In this schema, each product can have many ingredients, which is represented by the product_ingredients table with a many-to-many relationship between products and ingredients tables.
Each order can have many products, which is represented by the order_items table with a many-to-many relationship between orders and products tables, and each ingredient usage can be tracked on the ingrdient_usage table.


##License
The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
