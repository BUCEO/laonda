<?php
// ============= INTERFACES DEL PATRÓN OBSERVER =============
interface Observer {
    public function update(Historia $historia);
}

interface Subject {
    public function attach(Observer $observer);
    public function detach(Observer $observer);
    public function notifyObservers(Historia $historia);
}

// ============= MODELOS =============
class Historia {
    public $id_historia;
    public $titulo;
    public $fecha;
    public $id_trayecto;
    public $id_agencia;
    public $id_omnibus;
    public $URL_Historia;
    public $URL_Fotos;
    
    public function __construct($id = null, $titulo = '', $fecha = null, 
                               $idTrayecto = null, $idAgencia = null, 
                               $idOmnibus = null, $urlHistoria = '', $urlFotos = '') {
        $this->id_historia = $id;
        $this->titulo = $titulo;
        $this->fecha = $fecha ?? date('Y-m-d H:i:s');
        $this->id_trayecto = $idTrayecto;
        $this->id_agencia = $idAgencia;
        $this->id_omnibus = $idOmnibus;
        $this->URL_Historia = $urlHistoria;
        $this->URL_Fotos = $urlFotos;
    }
}

class Agencia {
    public $id_agencia;
    public $ubicacion;
    public $nombre;
    public $link_a_foto_agencia;
}

class Omnibus {
    public $id;
    public $numero;
    public $apodo;
    public $modelo;
    public $anioInicio;
    public $anioFin;
    public $link_a_foto_omnibus;
}

class Trayecto {
    public $id;
    public $origen; // Agencia
    public $destino; // Agencia
    public $omnibusAsignados = [];
    public $descripcion_trayecto;
    public $inicio;
    public $fin;
}

// ============= CONFIGURACIÓN BASE DE DATOS =============
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        $host = 'localhost';
        $dbname = 'sistema_omnibus';
        $username = 'root';
        $password = '';
        
        try {
            $this->connection = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch(PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
}

// ============= REPOSITORY =============
class HistoriaRepository {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function guardar(Historia $historia) {
        $sql = "INSERT INTO historias (titulo, fecha, id_trayecto, id_agencia, 
                id_omnibus, url_historia, url_fotos) 
                VALUES (:titulo, :fecha, :id_trayecto, :id_agencia, 
                :id_omnibus, :url_historia, :url_fotos)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':titulo' => $historia->titulo,
            ':fecha' => $historia->fecha,
            ':id_trayecto' => $historia->id_trayecto,
            ':id_agencia' => $historia->id_agencia,
            ':id_omnibus' => $historia->id_omnibus,
            ':url_historia' => $historia->URL_Historia,
            ':url_fotos' => $historia->URL_Fotos
        ]);
        
        $historia->id_historia = $this->db->lastInsertId();
        return $historia;
    }
    
    public function obtenerTodas() {
        $sql = "SELECT * FROM historias ORDER BY fecha DESC";
        $stmt = $this->db->query($sql);
        $historias = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $historia = new Historia(
                $row['id_historia'],
                $row['titulo'],
                $row['fecha'],
                $row['id_trayecto'],
                $row['id_agencia'],
                $row['id_omnibus'],
                $row['url_historia'],
                $row['url_fotos']
            );
            $historias[] = $historia;
        }
        
        return $historias;
    }
    
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM historias WHERE id_historia = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            return new Historia(
                $row['id_historia'],
                $row['titulo'],
                $row['fecha'],
                $row['id_trayecto'],
                $row['id_agencia'],
                $row['id_omnibus'],
                $row['url_historia'],
                $row['url_fotos']
            );
        }
        
        return null;
    }
    
    public function eliminar($id) {
        $sql = "DELETE FROM historias WHERE id_historia = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}

// ============= OBSERVERS CONCRETOS =============
class PanelHistoriasUIObserver implements Observer {
    public function update(Historia $historia) {
        // En una aplicación real, esto enviaría datos vía WebSocket o Server-Sent Events
        // Por ahora, guardamos en sesión para mostrar en el frontend
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['ultima_historia'] = [
            'id' => $historia->id_historia,
            'titulo' => $historia->titulo,
            'fecha' => $historia->fecha
        ];
        
        error_log("🖼️ Panel UI actualizado con nueva historia: " . $historia->titulo);
    }
}

class NotificacionServiceObserver implements Observer {
    public function update(Historia $historia) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Agregar notificación a la sesión
        if (!isset($_SESSION['notificaciones'])) {
            $_SESSION['notificaciones'] = [];
        }
        
        $notificacion = [
            'id' => uniqid(),
            'mensaje' => "Nueva historia agregada: \"{$historia->titulo}\"",
            'fecha' => date('H:i:s'),
            'timestamp' => time()
        ];
        
        array_unshift($_SESSION['notificaciones'], $notificacion);
        $_SESSION['notificaciones'] = array_slice($_SESSION['notificaciones'], 0, 5);
        
        // Enviar email (opcional)
        // $this->enviarEmail($historia);
        
        error_log("🔔 Notificación enviada: " . $notificacion['mensaje']);
    }
    
    private function enviarEmail(Historia $historia) {
        // Implementación de envío de email
        // mail($to, $subject, $message, $headers);
    }
}

class LogServiceObserver implements Observer {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function update(Historia $historia) {
        $sql = "INSERT INTO logs (evento, detalles, timestamp) 
                VALUES (:evento, :detalles, :timestamp)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':evento' => 'HISTORIA_CREADA',
            ':detalles' => "ID: {$historia->id_historia} - Título: {$historia->titulo}",
            ':timestamp' => date('Y-m-d H:i:s')
        ]);
        
        error_log("📝 Log registrado: HISTORIA_CREADA - {$historia->titulo}");
    }
}

// ============= MANAGER (SUBJECT) =============
class HistoriaManager implements Subject {
    private $observers = [];
    private $repository;
    
    public function __construct() {
        $this->repository = new HistoriaRepository();
    }
    
    public function attach(Observer $observer) {
        $this->observers[] = $observer;
    }
    
    public function detach(Observer $observer) {
        $key = array_search($observer, $this->observers, true);
        if ($key !== false) {
            unset($this->observers[$key]);
        }
    }
    
    public function notifyObservers(Historia $historia) {
        foreach ($this->observers as $observer) {
            $observer->update($historia);
        }
    }
    
    public function agregarHistoria(Historia $historia) {
        $historiaGuardada = $this->repository->guardar($historia);
        $this->notifyObservers($historiaGuardada);
        return $historiaGuardada;
    }
    
    public function obtenerHistorias() {
        return $this->repository->obtenerTodas();
    }
    
    public function obtenerPorId($id) {
        return $this->repository->obtenerPorId($id);
    }
    
    public function eliminarHistoria($id) {
        return $this->repository->eliminar($id);
    }
}

// ============= API REST ENDPOINTS =============
class HistoriaController {
    private $historiaManager;
    
    public function __construct() {
        $this->historiaManager = new HistoriaManager();
        
        // Registrar observers
        $this->historiaManager->attach(new PanelHistoriasUIObserver());
        $this->historiaManager->attach(new NotificacionServiceObserver());
        $this->historiaManager->attach(new LogServiceObserver());
    }
    
    public function handleRequest() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
        
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        switch ($method) {
            case 'GET':
                if (preg_match('/\/api\/historias\/(\d+)$/', $path, $matches)) {
                    $this->obtenerHistoria($matches[1]);
                } else {
                    $this->listarHistorias();
                }
                break;
                
            case 'POST':
                $this->crearHistoria();
                break;
                
            case 'DELETE':
                if (preg_match('/\/api\/historias\/(\d+)$/', $path, $matches)) {
                    $this->eliminarHistoria($matches[1]);
                }
                break;
                
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
        }
    }
    
    private function crearHistoria() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $historia = new Historia(
            null,
            $data['titulo'] ?? '',
            date('Y-m-d H:i:s'),
            $data['id_trayecto'] ?? null,
            $data['id_agencia'] ?? null,
            $data['id_omnibus'] ?? null,
            $data['url_historia'] ?? '',
            $data['url_fotos'] ?? ''
        );
        
        $nuevaHistoria = $this->historiaManager->agregarHistoria($historia);
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'data' => $nuevaHistoria
        ]);
    }
    
    private function listarHistorias() {
        $historias = $this->historiaManager->obtenerHistorias();
        echo json_encode([
            'success' => true,
            'data' => $historias
        ]);
    }
    
    private function obtenerHistoria($id) {
        $historia = $this->historiaManager->obtenerPorId($id);
        
        if ($historia) {
            echo json_encode([
                'success' => true,
                'data' => $historia
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Historia no encontrada'
            ]);
        }
    }
    
    private function eliminarHistoria($id) {
        $success = $this->historiaManager->eliminarHistoria($id);
        
        if ($success) {
            http_response_code(204);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Historia no encontrada'
            ]);
        }
    }
}

