<?php

/**
 * Migration tool for CodeIgniter
 */
class Tools extends CI_Controller
{

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // only be called from the command line
        if (!$this->input->is_cli_request()) {
            exit('Direct access is not allowed. This is a command line tool, so you should use the terminal');
        }

        // load the forge database class
        $this->load->dbforge();

        // initiate faker
        $this->faker = Faker\Factory::create();

        // path to the migration & seedes folders
        $this->migration_path = APPPATH."database/migrations/";
        $this->seed_path = APPPATH."database/seeds/";
    }

    /**
     * Display the message to the console
     *
     * @param string $message [string]
     *
     * @return void
     */
    public function message($message)
    {
        echo $message.PHP_EOL;
    }

    /**
     * Show the details of the in-built commands
     *
     * @return void
     */
    public function help()
    {
        $result  = "The following are the available command line interface commands\n\n";
        $result .= "php index.php tools migration [file_name] [table_name]  Create new migration file\n";
        $result .= "php index.php tools migrate [version_number]            Run all migrations. The version_number is optional.\n";
        $result .= "php index.php tools seeder [file_name]                  Creates a new seed file.\n";
        $result .= "php index.php tools seed [file_name]                    Run the specified seed file.\n";
        $result .= "php index.php tools seed                                Run all seed files.\n";

        $this->message($result);
    }

    /**
     * Create a migration file
     *
     * @param string $name  [file_name]
     * @param string $table [table_name]
     *
     * @return void
     */
    public function migration($name, $table = null)
    {
        $this->make_migration($name, $table);
    }

    /**
     * Run all the pending migration files
     *
     * @param string $version [version]
     *
     * @return void
     */
    public function migrate($version = null)
    {
        $this->load->library('migration');

        if ($version != null) {
            if ($this->migration->version($version) === false) {
                show_error($this->migration->error_string());
            } else {
                $this->message("Migrations run successfully");
            }
            return;
        }

        if ($this->migration->latest() === false) {
            show_error($this->migration->error_string());
        } else {
            $this->message("Migrations run successfully");
        }
    }

    /**
     * Create the seeder file
     *
     * @param string $name  [file_name]
     * @param string $table [table_name]
     *
     * @return void
     */
    public function seeder($name, $table = '')
    {
        $this->make_seeder($name, $table);
    }

    /**
     * Run seed
     *
     * @param string $name [filename]
     *
     * @return void
     */
    public function seed($name = null)
    {
        (new Seeder)->call($name);
    }

    /**
     * Create the migration file
     *
     * @param string $name  [name]
     * @param string $table [name]
     *
     * @return void
     */
    protected function make_migration($name, $table = null)
    {
        $timestamp = date('YmdHis');
        $table = !empty($table) ? $table : strtolower($name);

        $path = $this->migration_path."{$timestamp}_{$name}.php";
        $migration = fopen($path, "w") or die("Unable to create the migration file!");

        fwrite($migration, $this->_template_migration($name, $table));
        fclose($migration);

        $this->message("The migration file has successfully been created.\n$path");
    }

    /**
     * Create the seeder file
     *
     * @param string $name  [file_name]
     * @param string $table [table_name]
     *
     * @return void
     */
    protected function make_seeder($name, $table = '')
    {
        $path = $this->seed_path."$name.php";
        $seed = fopen($path, "w") or die("Unable to create seed file!");

        fwrite($seed, $this->_template_seeder($name, $table));
        fclose($seed);

        $this->message("The seeder $name has successfully been created.\n$path");
    }

    /**
     * The migration template
     *
     * @param string $name  [file_name]
     * @param string $table [table_name]
     *
     * @return string
     */
    private function _template_migration($name, $table)
    {
        return "
<?php

class Migration_$name extends CI_Migration
{
    /**
     * Create table with columns
     *
     * @return void
     */
    public function up()
    {
        \$this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ]
        ]);

        \$this->dbforge->add_key('id', true);
        \$this->dbforge->create_table('$table');
    }

    /**
     * Drop table
     *
     * @return void
     */
    public function down()
    {
        \$this->dbforge->drop_table('$table');
    }
}
        ";
    }

    /**
     * The seeder template
     *
     * @param string $name  [file_name]
     * @param string $table [table_name]
     *
     * @return string
     */
    private function _template_seeder($name, $table)
    {
        return "
<?php

class $name extends Seeder
{

    private \$table = '$table';

    /**
     * Run seeder
     *
     * @return void
     */
    public function run()
    {
        \$this->db->truncate(\$this->table);

        //seed manually
        \$data = [
            'username' => 'admin',
            'password' => '9876543210'
        ];
        \$this->db->insert(\$this->table, \$data);

        //seed using faker
        for (\$i = 0; \$i < 10; \$i++) {
            \$data = [
                'username' => \$this->faker->unique()->userName,
                'password' => '1234567890',
            ];
            \$this->db->insert(\$this->table, \$data);
        }
    }
}
        ";
    }
}

/**
 * Seeder class
 */
class Seeder
{
    private $CI;
    protected $db;
    protected $dbforge;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->dbforge();
        $this->db = $this->CI->db;
        $this->dbforge = $this->CI->dbforge;
    }
    /**
     * Run another seeder
     * 
     * @param string $seeder Seeder classname
     *
     * @return void
     */
    public function call($seeder)
    {
        $seeder = empty($seeder) ? 'DatabaseSeeder' : $seeder;
        $file   = $this->seed_path."$seeder.php";

        if (file_exists($file) && is_readable($file)) {
            include_once $file;

            (new $seeder)->run();

            echo 'Seed successfully.'.PHP_EOL;
            return;
        }
        throw new Exception("Unanle read file [$file].");
    }

    /**
     * Get property
     *
     * @param string $property [property name]
     *
     * @return mixed
     */
    public function __get($property)
    {
        return $this->CI->$property;
    }
}
