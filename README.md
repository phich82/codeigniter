# 1. Loading Faker Library in CodeIgniter
    - composer require --dev fzaninotto/faker
    - Open /index.php file in the root of the project:
        => add the following line before the line "require_once BASEPATH.'core/CodeIgniter.php';":
            /*
            * --
            * LOAD THE COMPOSER AUTOLOAD FILE
            * --
            */
            include_once './vendor/autoload.php';

# 2. Database Configuration
    Open /application/config/database.php

# 3. Migration Configuration
    - Create migrations folder: /application/database/migrations
    - Create seeds folder:      /application/database/seeds
    - Open /application/config/migration.php:
            $config['migration_path'] = APPPATH . 'migrations/'; 
        change to =>
            $config['migration_path'] = APPPATH.'database/migrations/';

            $config['migration_enabled'] = FALSE;
        change to =>
            $config['migration_enabled'] = TRUE;

# 4. CodeIgniter Command Line Interface (CLI)
No.	FUNCTION	    DESCRIPTION	                            PARAMETER(S)        USAGE
1	__construct	    Class initialization function	        None	            None
2	message	        Displays hello world in the console	    Name	            php index.php tools message "Rodrick Kaze"
3	help	        Displays the available commands and     None	            php index.php tools help
                    what tasks they perform
4	migration	    Creates a new migration file	        Migration file_name	php index.php tools migration "users"
5	migrate	        Executes all migration files that have  None	            php index.php tools migrate"
                    not been executed yet
6	seeder	        Create a new seed file	                Seed file name	    php index.php tools seeder "UsersSeeder"
7	seed	        Executes a specific seed file	        Seed file name	    php index.php tools seed "UsersSeeder"

# 5. How to use
    1. Generate migration file
        php index.php tools migration [file_name] [table_name]

        Ex: php index.php tools migration create_users_table users [with table name is users]
            => yyyymmddhhmmss_create_users_table.php will be created in application/database/migrations folder

        Ex: php index.php tools migration blogs [with table name is blogs]
            => yyyymmddhhmmss_blogs.php will be created in application/database/migrations folder

    2. Run migrate
        - Run with the specified migration file
            php index.php tools migrate [file_name]

        - Run all the migration files
            php index.php tools migrate

    3. Generate seeder file
        php index.php tools seeder [file_name] [table_name]

        Ex: php index.php tools seeder UsersSeeder users [with table name is users]
            => UsersSeeder.php will be created in application/database/seedes folder

        Ex: php index.php tools seeder UsersSeeder [with table name is empty]
            => UsersSeeder.php will be created in application/database/seeds folder

    4. Run seed
        - Run with the specified seeder file
            php index.php tools seed [file_name]

            Ex: php index.php tools seed UsersSeeder
            => Only run UsersSeeder

        - Run all the seeder files
            php index.php tools seed

            Ex: php index.php tools seed
            => Run all seeder files in DatabaseSeeder.php

# 6. How to run tests
    vendor/bin/phpunit --testdox --testsuite unitest:
    vendor/bin/phpunit --testdox --testsuite unitest:all
    vendor/bin/phpunit --testdox --testsuite unitest:controller
    vendor/bin/phpunit --testdox --testsuite unitest:model
    vendor/bin/phpunit --testdox --testsuite unitest:service

# 7. Customize the tests folder (tests => tests/unitest)
## Open the tests/unitest/Bootstrap.php file, replace the line 235 "define('TESTPATH', __dir__.DIRECTORY_SEPARATOR);" with the following line:

    define('TESTPATH', __dir__.DIRECTORY_SEPARATOR.'unitest'.DIRECTORY_SEPARATOR);

## Next, find to the line 369 "require __DIR__ . '/_ci_phpunit_test/CIPHPUnitTest.php';" and replace it with the following line:

    require __DIR__ . '/unitest/_ci_phpunit_test/CIPHPUnitTest.php';

##   Open the CIPHPUnitTest.php, insert the following line right after the line 42 "require TESTPATH . 'TestCase.php';":

	require TESTPATH . 'MyTestCase.php';
