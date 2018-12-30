
S/N	FUNCTION	    DESCRIPTION	                            PARAMETER(S)        USAGE
1	__construct	    Class initialization function	        None	            None
2	message	        Displays hello world in the console	    Name	            php index.php tools message "Rodrick Kaze"
3	help	        Displays the available commands and     None	            php index.php tools help
                    what tasks they perform
4	migration	    Creates a new migration file	        Migration file_name	php index.php tools migration "users"
5	migrate	        Executes all migration files that have  None	            php index.php tools migrate"
                    not been executed yet
6	seeder	        Create a new seed file	                Seed file name	    php index.php tools seeder "UsersSeeder"
7	seed	        Executes a specific seed file	        Seed file name	    php index.php tools seed "UsersSeeder"
