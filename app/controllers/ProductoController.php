<?php
require_once __DIR__ . '/../modelos/Producto.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProductoController {
    public function alta(Request $request, Response $response, $args) {
        $data = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();

        // Verificar y procesar la imagen
        if (isset($uploadedFiles['imagen'])) {
            $image = $uploadedFiles['imagen'];
            if ($image->getError() === UPLOAD_ERR_OK) {
                $filename = sprintf('%s_%s.%s', $data['nombre'], $data['tipo'], pathinfo($image->getClientFilename(), PATHINFO_EXTENSION));
                $image->moveTo(__DIR__ . '/../ImagenesDeRopa/2024/' . $filename);
                $data['imagen'] = 'ImagenesDeRopa/2024/' . $filename;
            }
        }

        $producto = new Producto();
        $resultado = $producto->alta($data);
        $response->getBody()->write(json_encode($resultado));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function consultar(Request $request, Response $response, $args) {
        $data = $request->getParsedBody();
        $producto = new Producto();
        $resultado = $producto->consultar($data);
        $response->getBody()->write(json_encode($resultado));
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function productosEntreValores($request, $response, $args) {
        $params = $request->getQueryParams();
        $min = $params['min'];
        $max = $params['max'];
        $venta = new Producto();
        $result = $venta->productosEntreValores($min, $max);
        return $response->withJson($result);
    }
}