<?php
require_once './modelos/Venta.php';


class VentaController {
    public function alta($request, $response, $args) {
        $data = $request->getParsedBody();
        $files = $request->getUploadedFiles();
        
        // Verifica si hay archivo subido
        if (!isset($files['imagen'])) {
            return $response->withJson(['status' => 'error', 'message' => 'No se subió la imagen'], 400);
        }

        $imagen = $files['imagen'];
        $email = $data['email'];
        $nombre = $data['nombre'];
        $tipo = $data['tipo'];
        $talla = $data['talla'];
        $cantidad = $data['cantidad'];
        
        // Crea el path para guardar la imagen
        $fecha = date('Ymd_His');
        $emailUser = explode('@', $email)[0];
        $imagePath = __DIR__ . "/../ImagenesDeVenta/2024/{$nombre}_{$tipo}_{$talla}_{$emailUser}_{$fecha}.jpg";
        
        // Mueve el archivo a la ubicación deseada
        $imagen->moveTo($imagePath);
        
        // Crea una nueva instancia de la clase Venta y guarda la venta
        $venta = new Venta();
        $result = $venta->alta([
            'email' => $email,
            'nombre' => $nombre,
            'tipo' => $tipo,
            'talla' => $talla,
            'cantidad' => $cantidad,
            'imagen' => $imagePath,
        ]);

        return $response->withJson($result);
    }

    public function productosVendidos($request, $response, $args) {
        $params = $request->getQueryParams();
        $fecha = $params['fecha'] ?? null;
        $venta = new Venta();
        $result = $venta->productosVendidos($fecha);
        return $response->withJson($result);
    }

    public function ventasPorUsuario($request, $response, $args) {
        $params = $request->getQueryParams();
        $email = $params['email'];
        $venta = new Venta();
        $result = $venta->ventasPorUsuario($email);
        return $response->withJson($result);
    }

    public function ventasPorProducto($request, $response, $args) {
        $params = $request->getQueryParams();
        $tipo = $params['tipo'];
        $venta = new Venta();
        $result = $venta->ventasPorProducto($tipo);
        return $response->withJson($result);
    }

    public function ingresosPorDia($request, $response, $args) {
        $params = $request->getQueryParams();
        $fecha = $params['fecha'] ?? null;
        $venta = new Venta();
        $result = $venta->ingresosPorDia($fecha);
        return $response->withJson($result);
    }

    public function productoMasVendido($request, $response, $args) {
        $venta = new Venta();
        $result = $venta->productoMasVendido();
        return $response->withJson($result);
    }
}
?>
