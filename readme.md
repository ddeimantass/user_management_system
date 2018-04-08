# Project:  
 
### Epic: User management system
### Stories:
 - As an admin I can add users. A user has a name
 - As an admin I can delete users
 - As an admin I can assign users to a group they aren’t already part of
 - As an admin I can remove users from a group
 - As an admin I can create groups
 - As an admin I can delete groups when they no longer have members
 
# Instructions

### Build
 Clone the repository to your environment
 
 Enter these comands to terminal:
 - `composer install`
 - edit .env file
 - `php bin/console doctrine:database:create`
 - `php bin/console doctrine:migrations:diff`
 - `php bin/console doctrine:migrations:migrate`
 - `php bin/console doctrine:fixtures:load`
 
 ### Run
  - `bin/console server:run`
  - Go to specified IP address (E.g. 127.0.0.1:8000)
  


