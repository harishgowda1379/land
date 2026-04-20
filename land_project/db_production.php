<?php
session_start();

// Production database configuration for Render PostgreSQL
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '5432';
$db = getenv('DB_NAME') ?: 'land_chain';
$user = getenv('DB_USER') ?: 'postgres';
$pass = getenv('DB_PASSWORD') ?: '';

// Fallback to file-based storage if database is not available
$db_file = __DIR__ . '/data.json';

try {
    // Try PostgreSQL connection first
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    // Initialize database tables if they don't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id SERIAL PRIMARY KEY,
        name VARCHAR(120) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS lands (
        id SERIAL PRIMARY KEY,
        owner_name VARCHAR(120) NOT NULL,
        location VARCHAR(255) NOT NULL,
        survey_number VARCHAR(100) NOT NULL UNIQUE,
        area VARCHAR(80) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS transactions (
        id SERIAL PRIMARY KEY,
        land_id INTEGER NOT NULL REFERENCES lands(id) ON DELETE CASCADE,
        seller VARCHAR(120) NOT NULL,
        buyer VARCHAR(120) NOT NULL,
        date TIMESTAMP NOT NULL,
        current_hash VARCHAR(255) NOT NULL,
        previous_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Insert sample data if tables are empty
    $userCount = $pdo->query("SELECT COUNT(*) as count FROM lands")->fetch()['count'];
    if ($userCount == 0) {
        $pdo->exec("INSERT INTO lands (owner_name, location, survey_number, area, created_at)
            VALUES ('Alice Johnson', 'Block B, Sector 14, Cityview', 'SVY-1001', '2500 sq.ft', CURRENT_TIMESTAMP)");
        
        $pdo->exec("INSERT INTO transactions (land_id, seller, buyer, date, current_hash, previous_hash, created_at)
            VALUES (1, 'Alice Johnson', 'Alice Johnson', '2026-01-01 12:00:00', '4201c703342af68bd4852d111bbec29ee677fe229d40694a4a39f00977216ece', '0', CURRENT_TIMESTAMP)");
    }
    
} catch (PDOException $e) {
    // Fallback to file-based storage
    if (!file_exists($db_file)) {
        $initial_data = [
            'users' => [],
            'lands' => [
                [
                    'id' => 1,
                    'owner_name' => 'Alice Johnson',
                    'location' => 'Block B, Sector 14, Cityview',
                    'survey_number' => 'SVY-1001',
                    'area' => '2500 sq.ft',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ],
            'transactions' => [
                [
                    'id' => 1,
                    'land_id' => 1,
                    'seller' => 'Alice Johnson',
                    'buyer' => 'Alice Johnson',
                    'date' => '2026-01-01 12:00:00',
                    'current_hash' => '4201c703342af68bd4852d111bbec29ee677fe229d40694a4a39f00977216ece',
                    'previous_hash' => '0',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ]
        ];
        file_put_contents($db_file, json_encode($initial_data, JSON_PRETTY_PRINT));
    }
    
    // Simple database abstraction class
    class SimpleDB {
        private $data;
        private $file;
        
        public function __construct($file) {
            $this->file = $file;
            $this->data = json_decode(file_get_contents($file), true);
        }
        
        public function query($sql, $params = []) {
            return new SimpleStatement($this->data, $sql, $params);
        }
        
        public function prepare($sql) {
            return new SimpleStatement($this->data, $sql, []);
        }
        
        public function save() {
            file_put_contents($this->file, json_encode($this->data, JSON_PRETTY_PRINT));
        }
    }
    
    class SimpleStatement {
        private $data;
        private $sql;
        private $params;
        private $result;
        
        public function __construct(&$data, $sql, $params) {
            $this->data = &$data;
            $this->sql = $sql;
            $this->params = $params;
            $this->result = null;
        }
        
        public function execute($params = []) {
            if (!empty($params)) {
                $this->params = $params;
            }
            
            // Handle SELECT queries
            if (strpos($this->sql, 'SELECT') !== false) {
                if (strpos($this->sql, 'users') !== false) {
                    $this->result = $this->data['users'];
                    if (!empty($this->params) && strpos($this->sql, 'WHERE email = ?') !== false) {
                        $email = $this->params[0];
                        $this->result = array_filter($this->result, function($user) use ($email) {
                            return $user['email'] === $email;
                        });
                    }
                } elseif (strpos($this->sql, 'lands') !== false) {
                    $this->result = $this->data['lands'];
                    if (!empty($this->params) && strpos($this->sql, 'WHERE survey_number = ?') !== false) {
                        $survey = $this->params[0];
                        $this->result = array_filter($this->result, function($land) use ($survey) {
                            return $land['survey_number'] === $survey;
                        });
                    }
                } elseif (strpos($this->sql, 'transactions') !== false) {
                    $this->result = $this->data['transactions'];
                    if (!empty($this->params) && strpos($this->sql, 'WHERE land_id = ?') !== false) {
                        $land_id = $this->params[0];
                        $this->result = array_filter($this->result, function($trans) use ($land_id) {
                            return $trans['land_id'] == $land_id;
                        });
                    }
                }
            }
            
            // Handle INSERT queries
            if (strpos($this->sql, 'INSERT') !== false) {
                if (strpos($this->sql, 'users') !== false) {
                    $newId = count($this->data['users']) + 1;
                    $this->data['users'][] = [
                        'id' => $newId,
                        'name' => $this->params[0],
                        'email' => $this->params[1],
                        'password_hash' => $this->params[2],
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    file_put_contents(__DIR__ . '/data.json', json_encode($this->data, JSON_PRETTY_PRINT));
                } elseif (strpos($this->sql, 'lands') !== false) {
                    $newId = count($this->data['lands']) + 1;
                    $this->data['lands'][] = [
                        'id' => $newId,
                        'owner_name' => $this->params[0],
                        'location' => $this->params[1],
                        'survey_number' => $this->params[2],
                        'area' => $this->params[3],
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    file_put_contents(__DIR__ . '/data.json', json_encode($this->data, JSON_PRETTY_PRINT));
                } elseif (strpos($this->sql, 'transactions') !== false) {
                    $newId = count($this->data['transactions']) + 1;
                    $this->data['transactions'][] = [
                        'id' => $newId,
                        'land_id' => $this->params[0],
                        'seller' => $this->params[1],
                        'buyer' => $this->params[2],
                        'date' => $this->params[3],
                        'current_hash' => $this->params[4],
                        'previous_hash' => $this->params[5],
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    file_put_contents(__DIR__ . '/data.json', json_encode($this->data, JSON_PRETTY_PRINT));
                }
            }
            
            return true;
        }
        
        public function fetch() {
            if ($this->result !== null) {
                $item = current($this->result);
                next($this->result);
                return $item !== false ? $item : false;
            }
            return false;
        }
        
        public function fetchAll() {
            if ($this->result !== null) {
                return is_array($this->result) ? array_values($this->result) : [];
            }
            return [];
        }
    }
    
    $pdo = new SimpleDB($db_file);
}

function hashBlock($land_id, $seller, $buyer, $date, $previous_hash)
{
    return hash('sha256', $land_id . $seller . $buyer . $date . $previous_hash);
}

function escape($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
