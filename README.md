## Champions League (UCL) Simulator

## Table of Contents
- [About](#about)
- [Tech Stack](#tech-stack)
- [Installation](#installation)
- [Screenshots](#screenshots)

## About

This is a Champions League (UCL) simulator built with Laravel 12 and Vue.js.

## Tech Stack

- **Backend**: PHP 8.2, Laravel 12
- **Frontend**: Vue.js, Inertia.js, TypeScript, Tailwind CSS
- **Database**: PostgreSQL

## Installation

Follow these steps to run the project locally:

1. **Clone the repository**

   ```bash
   git clone https://github.com/mertbag96/champions-league-simulator.git
   cd champions-league-simulator
   ```

2. **Install PHP dependencies**

   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**

   ```bash
   npm install
   ```

4. **Create and configure environment file**

   ```bash
   cp .env.example .env
   ```

5. **Generate application key**

   ```bash
   php artisan key:generate
   ```

6. **Run database migrations (and seed if you want)**

   ```bash
   php artisan migrate # without default teams

   or
   
   php artisan migrate --seed # with default teams
   ```

7. **Build frontend assets**

   ```bash
   npm run build
   
   or for local development:

   npm run dev
   ```

8. **Serve the application**

   If you are using Laravel Herd, the site will be available at your `.test` domain.  

## Screenshots

### Home Page (Without Teams)
![Home Page 1](https://github.com/mertbag96/champions-league-simulator/blob/main/public/assets/images/landing-1.png)

### Home Page (With Teams)
![Home Page 2](https://github.com/mertbag96/champions-league-simulator/blob/main/public/assets/images/landing-2.png)
