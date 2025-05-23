# CreateYourFirstPhpBlog

## Prerequisites

1. PHP >= 8.3.19
2. MySQL >= 8.0.40
3. Composer >= 2.8.6

### Installation

1. [Install PHP](https://www.php.net/downloads.php)
2. [Install MySQL](https://www.mysql.com/downloads/)
3. [Install Composer](https://getcomposer.org/download/)
4. Clone the project with git clone
   5. In SSH : `git clone git@github.com:GabrielSAPONARA/CreateYourFirstPhpBlog.git`
   6. In HTTPS : `git clone https://github.
   com/GabrielSAPONARA/CreateYourFirstPhpBlog.git`
   7. Or with Github CLI : `gh repo clone GabrielSAPONARA/CreateYourFirstPhpBlog`

### Run the project

1. Go to the file location which clone the project.
2. `cd CreateYourFirstPhpBlog`
3. `composer install` : to install PHP dependencies
4. `npm install` : to install NPM dependencies
5. Create a MySQL database and update the Bootstrap.php file in .
   /src/Bootstrap.php
6. `php bin/doctrine orm:schema-tool:create` : to create the database schema
7. `php bin/doctrine orm:schema-tool:update --force` : to update the 
   database schema
8. `sass public/assets/style/sass/:public/assets/style/css/` : to compile SCSS
   * In dev you can use `sass public/assets/style/sass/:public/assets/style
   /css/ --watch` : to compile SCSS
9. `php -S localhost:8000 -t public/` : to launch the PHP server
   * In dev you can use `php -S localhost:8000 -t public/ -d 
   error_reporting=E_ALL` : to see all PHP errors
10. Insert role in database :
   ```sql
   INSERT INTO role (name)
   VALUES ("Administrator", "Moderator", "Member")
   ```
11. Register a first with the page Sign up page

12. Find administrator id
```sql
    SELECT id
    FROM role
    WHERE name="Administrator"
```
13. Update your rights account
```sql
UPDATE user
SET role_id =
WHERE user.username = "";
```

