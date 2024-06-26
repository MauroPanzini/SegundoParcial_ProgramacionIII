<?php
require_once './db/AccesoDatos.php';

class Venta {
    private $db;

    public function __construct() {
        $this->db = AccesoDatos::obtenerInstancia();
    }
    public function alta($data) {
        $email = $data['email'];
        $nombre = $data['nombre'];
        $tipo = $data['tipo'];
        $talla = $data['talla'];
        $cantidad = $data['cantidad'];
        $imagen = $data['imagen'];

        $stmt = $this->db->prepararConsulta("SELECT * FROM productos WHERE nombre = ? AND tipo = ? AND talla = ? AND stock >= ?");
        $stmt->execute([$nombre, $tipo, $talla, $cantidad]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            $fecha = date('Y-m-d H:i:s');
            $stmt = $this->db->prepararConsulta("INSERT INTO ventas (email, nombre, tipo, talla, cantidad, fecha, numero_pedido, imagen) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $numero_pedido = rand(1000, 9999);
            $stmt->execute([$email, $nombre, $tipo, $talla, $cantidad, $fecha, $numero_pedido, $imagen]);

            $stmt = $this->db->prepararConsulta("UPDATE productos SET stock = stock - ? WHERE nombre = ? AND tipo = ? AND talla = ?");
            $stmt->execute([$cantidad, $nombre, $tipo, $talla]);

            return ['status' => 'success'];
        } else {
            return ['status' => 'stock insuficiente'];
        }
    }

    public function productosVendidos($fecha = null) {
        if ($fecha === null) {
            $fecha = date('Y-m-d', strtotime('yesterday'));
        }
        $stmt = $this->db->prepararConsulta("SELECT SUM(cantidad) AS total_vendidos FROM ventas WHERE DATE(fecha) = ?");
        $stmt->execute([$fecha]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function ventasPorUsuario($email) {
        $stmt = $this->db->prepararConsulta("SELECT * FROM ventas WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ventasPorProducto($tipo) {
        $stmt = $this->db->prepararConsulta("SELECT * FROM ventas WHERE tipo = ?");
        $stmt->execute([$tipo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ingresosPorDia($fecha = null) {
        if ($fecha === null) {
            $stmt = $this->db->prepararConsulta("SELECT fecha, SUM(precio * cantidad) AS total_ingresos FROM ventas JOIN productos ON ventas.nombre = productos.nombre GROUP BY fecha");
        } else {
            $stmt = $this->db->prepararConsulta("SELECT fecha, SUM(precio * cantidad) AS total_ingresos FROM ventas JOIN productos ON ventas.nombre = productos.nombre WHERE DATE(fecha) = ? GROUP BY fecha");
            $stmt->execute([$fecha]);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function productoMasVendido() {
        $stmt = $this->db->prepararConsulta("SELECT nombre, tipo, SUM(cantidad) AS total_vendidos FROM ventas GROUP BY nombre, tipo ORDER BY total_vendidos DESC LIMIT 1");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
