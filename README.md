# Laravel News Aggregator

This project is a news aggregator built with Laravel, designed to aggregate and display news articles from various sources.

## Getting Started

### Prerequisites
Make sure you have docker and docker-compose installed on your machine.

### Installation

1. Clone the repository:

    ```
    git clone https://github.com/AdryannMillos/innoscripta.git
    ```

2. Change into the project directory:

    ```
    cd innoscripta
    ```

3. Create a .env file
 
Create a .env file in the project root based on the provided .env.example. Update the variables as needed.

4. Docker:

    ```
   docker-compose build
   docker-compose up -d
    ```
4.5 Configure the project:

    ```
    docker-compose exec app bash
    composer install
    php artisan key:generate
    php artisan migrate
    ```

