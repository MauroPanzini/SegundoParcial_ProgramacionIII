<?php
class Producto {
    private $db;

    public function __construct() {
        $this->db = AccesoDatos::obtenerInstancia();
    }
    public function alta($data) {
        
        // Verificar si el producto ya existe
        $consulta = $this->db->prepararConsulta('SELECT * FROM productos WHERE nombre = ? AND tipo = ?');
        $consulta->execute([$data['nombre'], $data['tipo']]);
        $producto = $consulta->fetch();

        if ($producto) {
            // Actualizar el precio y el stock existente
            $consulta = $this->db->prepararConsulta('UPDATE productos SET precio = ?, stock = stock + ? WHERE id = ?');
            $consulta->execute([$data['precio'], $data['stock'], $producto['id']]);
        } else {
            // Insertar nuevo producto
            $consulta = $this->db->prepararConsulta('INSERT INTO productos (nombre, precio, tipo, talla, color, stock, imagen) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $consulta->execute([$data['nombre'], $data['precio'], $data['tipo'], $data['talla'], $data['color'], $data['stock'], $data['imagen']]);
        }

        return ['status' => 'success'];
    }
    
    public function consultar($data) {
        $this->db = AccesoDatos::obtenerInstancia();
        $consulta = $this->db->prepararConsulta('SELECT * FROM productos WHERE nombre = ? AND tipo = ? AND color = ?');
        $consulta->execute([$data['nombre'], $data['tipo'], $data['color']]);
        $producto = $consulta->fetch();

        if ($producto) {
            return ['status' => 'existe'];
        } else {
            $consulta = $this->db->prepararConsulta('SELECT * FROM productos WHERE nombre = ?');
            $consulta->execute([$data['nombre']]);
            if (!$consulta->fetch()) {
                return ['status' => 'No hay productos del nombre ' . $data['nombre']];
            }

            $consulta = $this->db->prepararConsulta('SELECT * FROM productos WHERE tipo = ?');
            $consulta->execute([$data['tipo']]);
            if (!$consulta->fetch()) {
                return ['status' => 'No hay productos del tipo ' . $data['tipo']];
            }

            return ['status' => 'No hay productos del color ' . $data['color']];
        }
    }

    public function productosEntreValores($min, $max) {
        $stmt = $this->db->prepararConsulta("SELECT * FROM productos WHERE precio BETWEEN ? AND ?");
        $stmt->execute([$min, $max]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
