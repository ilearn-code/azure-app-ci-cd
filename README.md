# PHP Azure Demo Project

This is a simple PHP web application with database connectivity, designed to demonstrate CI/CD with GitHub and Azure App Service.

## Project Structure
- `index.php` - Main application file that displays users from database
- `config.php` - Database configuration with environment variables
- `database.sql` - Database schema and sample data
- `web.config` - Azure App Service configuration
- `.github/workflows/azure-deploy.yml` - GitHub Actions workflow for deployment

## Local Setup
1. Set up a local MySQL database
2. Import the `database.sql` file
3. Configure environment variables or update `config.php` with your local database settings
4. Run with a local PHP server: `php -S localhost:8000`

## Azure Deployment
Follow the step-by-step documentation for complete Azure setup and CI/CD configuration.

## Environment Variables
- `DB_HOST` - Database server hostname
- `DB_USER` - Database username
- `DB_PASS` - Database password  
- `DB_NAME` - Database name

## Features
- Database connectivity with PDO
- Environment-based configuration
- Error handling
- Responsive HTML table display
- Deployment timestamp tracking
