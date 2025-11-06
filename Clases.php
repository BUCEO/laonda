class Omnibus {
    private $id;
    private $matricula;
    private $modelo; // Ej: "Mercedes-Benz O-364"
    private $anio; // 1985
    private $capacidad;
    private $estado; // "activo", "vintage", "baja"

    public function __construct($matricula, $modelo, $anio, $capacidad) {
        $this->matricula = $matricula;
        $this->modelo = $modelo;
        $this->anio = $anio;
        $this->capacidad = $capacidad;
        $this->estado = "activo";
    }

    // Getters y setters...
    public function getFotoVintage() {
        return "assets/omnibus/" . $this->matricula . ".jpg";
    }
}
class Trayecto {
    private $id;
    private $origen; // Ej: "Montevideo"
    private $destino; // Ej: "Paysandú"
    private $duracion; // "6 horas"
    private $precio; // 120.50 (pesos uruguayos vintage)
    private $omnibusAsignado; // Objeto Omnibus

    public function __construct($origen, $destino, $duracion, $precio) {
        $this->origen = $origen;
        $this->destino = $destino;
        $this->duracion = $duracion;
        $this->precio = $precio;
    }

    public function asignarOmnibus(Omnibus $omnibus) {
        $this->omnibusAsignado = $omnibus;
    }
}
class Agencia {
    private $id;
    private $nombre; // Ej: "Terminal Baltasar Brum"
    private $ciudad;
    private $trayectos = []; // Array de objetos Trayecto

    public function __construct($nombre, $ciudad) {
        $this->nombre = $nombre;
        $this->ciudad = $ciudad;
    }

    public function agregarTrayecto(Trayecto $trayecto) {
        $this->trayectos[] = $trayecto;
    }
}